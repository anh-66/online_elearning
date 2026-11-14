<?php

namespace App\Filesystem;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use RuntimeException;
use Throwable;

/**
 * Flysystem adapter lưu file lên Cloudinary bằng Upload API (không cần SDK).
 *
 * Đường dẫn Laravel (vd: courses/thumbnails/abc.jpg) được giữ nguyên làm public ID,
 * nên giá trị lưu trong database không đổi và URL được tính mà không cần gọi API.
 * Ảnh dùng resource type "image", các file khác (pdf, docx, zip...) dùng "raw".
 */
class CloudinaryAdapter implements FilesystemAdapter
{
    /**
     * @var array<int, string>
     */
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'avif', 'ico'];

    public function __construct(
        private readonly string $cloudName,
        private readonly string $apiKey,
        private readonly string $apiSecret,
        private readonly string $folder = '',
    ) {}

    /**
     * Tạo adapter từ biến CLOUDINARY_URL dạng cloudinary://API_KEY:API_SECRET@CLOUD_NAME.
     */
    public static function fromUrl(string $cloudinaryUrl, string $folder = ''): self
    {
        $parts = parse_url(trim($cloudinaryUrl));

        if (! is_array($parts)
            || ($parts['scheme'] ?? null) !== 'cloudinary'
            || empty($parts['host'])
            || empty($parts['user'])
            || empty($parts['pass'])) {
            throw new InvalidArgumentException('CLOUDINARY_URL phải có dạng cloudinary://API_KEY:API_SECRET@CLOUD_NAME');
        }

        return new self(
            $parts['host'],
            rawurldecode($parts['user']),
            rawurldecode($parts['pass']),
            trim($folder, '/'),
        );
    }

    /**
     * Thư mục gốc của dự án trên Cloudinary (biến CLOUDINARY_FOLDER).
     */
    public function folder(): string
    {
        return $this->folder;
    }

    /**
     * Liệt kê ảnh trong thư mục của dự án bằng Admin API, tối đa 500 ảnh mỗi lần gọi.
     *
     * @return array{resources: array<int, array<string, mixed>>, next_cursor: ?string}
     *
     * @throws RuntimeException khi Cloudinary báo lỗi hoặc không kết nối được
     */
    public function listImages(?string $nextCursor = null, int $maxResults = 500): array
    {
        $response = $this->adminGet('resources/image/upload', array_filter([
            'prefix' => $this->folder === '' ? null : $this->folder.'/',
            'max_results' => $maxResults,
            'next_cursor' => $nextCursor,
        ]));

        return [
            'resources' => (array) $response->json('resources', []),
            'next_cursor' => $response->json('next_cursor'),
        ];
    }

    /**
     * Mức sử dụng của tài khoản Cloudinary (credits, dung lượng, băng thông).
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException khi Cloudinary báo lỗi hoặc không kết nối được
     */
    public function usage(): array
    {
        return (array) $this->adminGet('usage')->json();
    }

    /**
     * URL công khai của file trên CDN Cloudinary.
     */
    public function getUrl(string $path): string
    {
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $this->prefixedPath($path))));

        return sprintf(
            'https://res.cloudinary.com/%s/%s/upload/%s',
            $this->cloudName,
            $this->resourceType($path),
            $encodedPath,
        );
    }

    public function fileExists(string $path): bool
    {
        try {
            return $this->http(15)->head($this->getUrl($path))->successful();
        } catch (Throwable $exception) {
            throw UnableToCheckExistence::forLocation($path, $exception);
        }
    }

    public function directoryExists(string $path): bool
    {
        return false;
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $this->upload($path, $contents);
    }

    /**
     * @param  resource  $contents
     */
    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->upload($path, $contents);
    }

    public function read(string $path): string
    {
        try {
            $response = $this->http(60)->get($this->getUrl($path));
        } catch (Throwable $exception) {
            throw UnableToReadFile::fromLocation($path, $exception->getMessage(), $exception);
        }

        if ($response->failed()) {
            throw UnableToReadFile::fromLocation($path, 'HTTP '.$response->status());
        }

        return $response->body();
    }

    /**
     * @return resource
     */
    public function readStream(string $path)
    {
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, $this->read($path));
        rewind($stream);

        return $stream;
    }

    public function delete(string $path): void
    {
        try {
            $response = $this->http(30)->asForm()->post(
                $this->apiUrl($path, 'destroy'),
                $this->signedParams(['public_id' => $this->publicId($path), 'invalidate' => 'true']),
            );
        } catch (Throwable $exception) {
            throw UnableToDeleteFile::atLocation($path, $exception->getMessage(), $exception);
        }

        if ($response->failed()) {
            throw UnableToDeleteFile::atLocation($path, $this->errorMessage($response));
        }
    }

    public function deleteDirectory(string $path): void
    {
        // Cloudinary không có thư mục thật; file được xóa từng cái qua delete().
    }

    public function createDirectory(string $path, Config $config): void
    {
        // Thư mục trên Cloudinary được tạo tự động theo public ID.
    }

    public function setVisibility(string $path, string $visibility): void
    {
        // File upload kiểu "upload" trên Cloudinary luôn công khai.
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path, null, 'public');
    }

    public function mimeType(string $path): FileAttributes
    {
        throw UnableToRetrieveMetadata::mimeType($path, 'Cloudinary adapter không hỗ trợ.');
    }

    public function lastModified(string $path): FileAttributes
    {
        throw UnableToRetrieveMetadata::lastModified($path, 'Cloudinary adapter không hỗ trợ.');
    }

    public function fileSize(string $path): FileAttributes
    {
        throw UnableToRetrieveMetadata::fileSize($path, 'Cloudinary adapter không hỗ trợ.');
    }

    /**
     * @return iterable<int, never>
     */
    public function listContents(string $path, bool $deep): iterable
    {
        return [];
    }

    public function move(string $source, string $destination, Config $config): void
    {
        try {
            $this->copy($source, $destination, $config);
            $this->delete($source);
        } catch (Throwable $exception) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $exception);
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        try {
            $this->upload($destination, $this->read($source));
        } catch (Throwable $exception) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $exception);
        }
    }

    /**
     * Ký tham số theo chuẩn Cloudinary: sắp xếp theo tên, nối "k=v" bằng "&", thêm API secret rồi SHA-1.
     *
     * @param  array<string, string|int>  $params
     * @return array<string, string|int>
     */
    public function signedParams(array $params): array
    {
        $params['timestamp'] ??= time();
        ksort($params);

        $toSign = implode('&', array_map(
            fn (string $key, string|int $value): string => $key.'='.$value,
            array_keys($params),
            $params,
        ));

        return $params + [
            'api_key' => $this->apiKey,
            'signature' => sha1($toSign.$this->apiSecret),
        ];
    }

    /**
     * @param  string|resource  $contents
     */
    private function upload(string $path, $contents): void
    {
        try {
            $response = $this->http(120)
                ->attach('file', $contents, basename($path))
                ->post($this->apiUrl($path, 'upload'), $this->signedParams([
                    'public_id' => $this->publicId($path),
                    'overwrite' => 'true',
                ]));
        } catch (Throwable $exception) {
            throw UnableToWriteFile::atLocation($path, $exception->getMessage(), $exception);
        }

        if ($response->failed()) {
            throw UnableToWriteFile::atLocation($path, $this->errorMessage($response));
        }
    }

    /**
     * @param  array<string, string|int>  $query
     */
    private function adminGet(string $endpoint, array $query = []): Response
    {
        try {
            $response = $this->adminRequest()->get(
                sprintf('https://api.cloudinary.com/v1_1/%s/%s', $this->cloudName, $endpoint),
                $query,
            );
        } catch (Throwable $exception) {
            throw new RuntimeException('Không kết nối được Cloudinary: '.$exception->getMessage(), 0, $exception);
        }

        if ($response->failed()) {
            throw new RuntimeException('Cloudinary báo lỗi: '.$this->errorMessage($response));
        }

        return $response;
    }

    /**
     * Admin API dùng xác thực Basic bằng API key và API secret.
     */
    private function adminRequest(): PendingRequest
    {
        return $this->http(20)
            ->withBasicAuth($this->apiKey, $this->apiSecret)
            ->acceptJson();
    }

    /**
     * Khởi tạo HTTP client với timeout và tự động bỏ qua verify SSL trên môi trường local/Windows không có chứng chỉ CA.
     */
    private function http(int $timeout = 60): PendingRequest
    {
        $request = Http::timeout($timeout);

        if (app()->environment('local', 'testing') || ! (ini_get('curl.cainfo') || ini_get('openssl.cafile'))) {
            $request = $request->withoutVerifying();
        }

        return $request;
    }

    private function apiUrl(string $path, string $action): string
    {
        return sprintf(
            'https://api.cloudinary.com/v1_1/%s/%s/%s',
            $this->cloudName,
            $this->resourceType($path),
            $action,
        );
    }

    private function resourceType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, self::IMAGE_EXTENSIONS, true) ? 'image' : 'raw';
    }

    /**
     * Ảnh: public ID không có đuôi file (Cloudinary tự thêm). File raw: giữ nguyên đuôi.
     */
    private function publicId(string $path): string
    {
        $prefixed = $this->prefixedPath($path);

        if ($this->resourceType($path) === 'raw') {
            return $prefixed;
        }

        $extension = pathinfo($prefixed, PATHINFO_EXTENSION);

        return $extension === '' ? $prefixed : substr($prefixed, 0, -strlen($extension) - 1);
    }

    private function prefixedPath(string $path): string
    {
        $path = ltrim($path, '/');

        return $this->folder === '' ? $path : $this->folder.'/'.$path;
    }

    private function errorMessage(Response $response): string
    {
        return (string) ($response->json('error.message') ?? 'HTTP '.$response->status());
    }
}
