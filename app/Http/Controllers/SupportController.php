<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportController extends Controller
{
    /**
     * Display the support center hub.
     */
    public function index(Request $request): View
    {
        $articles = $this->getSupportArticles();
        $categories = $this->getSupportCategories();
        $faqs = $this->getFrequentlyAskedQuestions();
        $quickLinks = $this->getQuickLinks();

        $searchKeyword = trim((string) $request->query('q', ''));
        $activeCategory = (string) $request->query('category', 'all');

        $filteredArticles = $articles;

        if ($searchKeyword !== '') {
            $normalizedKeyword = mb_strtolower($searchKeyword);
            $filteredArticles = array_filter($articles, function ($article) use ($normalizedKeyword) {
                return str_contains(mb_strtolower($article['title']), $normalizedKeyword)
                    || str_contains(mb_strtolower($article['summary']), $normalizedKeyword)
                    || str_contains(mb_strtolower($article['category']), $normalizedKeyword)
                    || str_contains(mb_strtolower(implode(' ', array_column($article['steps'], 'title'))), $normalizedKeyword);
            });
        }

        if ($activeCategory !== 'all' && $activeCategory !== '') {
            $filteredArticles = array_filter($filteredArticles, function ($article) use ($activeCategory) {
                return ($article['category_slug'] ?? '') === $activeCategory;
            });
        }

        return view('support.index', [
            'articles' => $articles,
            'filteredArticles' => array_values($filteredArticles),
            'categories' => $categories,
            'faqs' => $faqs,
            'quickLinks' => $quickLinks,
            'searchKeyword' => $searchKeyword,
            'activeCategory' => $activeCategory,
        ]);
    }

    /**
     * Display the feedback & bug report form.
     */
    public function feedback(): View
    {
        return view('support.feedback');
    }

    /**
     * Handle submission of feedback or bug report.
     */
    public function submitFeedback(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'in:bug,feature,question,other'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
            'description' => ['required', 'string', 'max:5000'],
            'sender_email' => ['nullable', 'email', 'max:255'],
            'sender_name' => ['nullable', 'string', 'max:255'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:png,jpg,jpeg,webp,pdf,log,txt'],
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('feedbacks', 'public');
        }

        $user = $request->user();

        Feedback::create([
            'user_id' => $user?->id,
            'sender_name' => $user?->name ?? $validated['sender_name'] ?? 'Khách truy cập',
            'sender_email' => $user?->email ?? $validated['sender_email'] ?? null,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'attachment' => $attachmentPath,
            'status' => Feedback::STATUS_PENDING,
        ]);

        return back()->with('success', 'Báo cáo sự cố / góp ý của bạn đã được lưu thành công vào hệ thống và chuyển đến ban quản trị EduLearn! Chúng tôi sẽ xem xét và phản hồi trong vòng 24h.');
    }

    /**
     * Display a specific support article guide.
     */
    public function show(string $slug): View
    {
        $articles = $this->getSupportArticles();

        if (! isset($articles[$slug])) {
            abort(404, 'Không tìm thấy bài viết hỗ trợ yêu cầu.');
        }

        $article = $articles[$slug];

        // Related articles excluding current
        $relatedArticles = array_filter($articles, function ($item, $key) use ($slug, $article) {
            return $key !== $slug && ($item['category_slug'] === $article['category_slug'] || in_array($key, ['doi-mat-khau', 'goi-dang-ky', 'dat-lai-mat-khau'], true));
        }, ARRAY_FILTER_USE_BOTH);

        return view('support.show', [
            'article' => $article,
            'relatedArticles' => array_slice($relatedArticles, 0, 3),
            'allArticles' => $articles,
        ]);
    }

    /**
     * Get list of support categories.
     *
     * @return array<int, array<string, string>>
     */
    protected function getSupportCategories(): array
    {
        return [
            [
                'slug' => 'all',
                'name' => 'Tất cả chủ đề',
                'description' => 'Xem tất cả hướng dẫn và giải đáp trong hệ thống',
                'icon' => 'squares',
            ],
            [
                'slug' => 'tai-khoan-bao-mat',
                'name' => 'Tài khoản & Mật khẩu',
                'description' => 'Đổi mật khẩu, khôi phục tài khoản và bảo mật thông tin',
                'icon' => 'shield',
            ],
            [
                'slug' => 'goi-dang-ky-thanh-toan',
                'name' => 'Gói đăng ký & Học phí',
                'description' => 'Học phí, các phương thức thanh toán và đăng ký khóa học',
                'icon' => 'credit-card',
            ],
            [
                'slug' => 'khoa-hoc-hoc-tap',
                'name' => 'Khóa học & Học tập',
                'description' => 'Trình phát video bài giảng, tiến độ học tập và ghi danh',
                'icon' => 'play-circle',
            ],
            [
                'slug' => 'chung-chi-danh-gia',
                'name' => 'Chứng chỉ & Đánh giá',
                'description' => 'Điều kiện tốt nghiệp, cấp chứng chỉ và đánh giá sao',
                'icon' => 'academic-cap',
            ],
            [
                'slug' => 'giang-vien',
                'name' => 'Dành cho Giảng viên',
                'description' => 'Tạo khóa học, tải bài giảng và xuất bản lớp học',
                'icon' => 'user-group',
            ],
        ];
    }

    /**
     * Quick links displayed under the Apple-style search box.
     *
     * @return array<int, array<string, string>>
     */
    protected function getQuickLinks(): array
    {
        return [
            [
                'title' => 'Nếu bạn quên mật khẩu tài khoản EduLearn',
                'slug' => 'dat-lai-mat-khau',
                'url' => route('support.show', 'dat-lai-mat-khau'),
            ],
            [
                'title' => 'Cách đổi mật khẩu tài khoản đang sử dụng',
                'slug' => 'doi-mat-khau',
                'url' => route('support.show', 'doi-mat-khau'),
            ],
            [
                'title' => 'Quản lý gói đăng ký và thanh toán học phí',
                'slug' => 'goi-dang-ky',
                'url' => route('support.show', 'goi-dang-ky'),
            ],
            [
                'title' => 'Hướng dẫn truy cập trình phát video và lưu tiến độ',
                'slug' => 'kich-hoat-khoa-hoc',
                'url' => route('support.show', 'kich-hoat-khoa-hoc'),
            ],
            [
                'title' => 'Điều kiện và hướng dẫn nhận chứng chỉ hoàn thành',
                'slug' => 'chung-chi-hoan-thanh',
                'url' => route('support.show', 'chung-chi-hoan-thanh'),
            ],
            [
                'title' => 'Hướng dẫn tạo giáo trình và xuất bản khóa học cho Giảng viên',
                'slug' => 'huong-dan-giang-vien',
                'url' => route('support.show', 'huong-dan-giang-vien'),
            ],
        ];
    }

    /**
     * Get dictionary of all support articles with step-by-step instructions and action links.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function getSupportArticles(): array
    {
        return [
            'doi-mat-khau' => [
                'slug' => 'doi-mat-khau',
                'title' => 'Đổi mật khẩu tài khoản EduLearn của bạn',
                'category' => 'Tài khoản & Mật khẩu',
                'category_slug' => 'tai-khoan-bao-mat',
                'badge' => 'Bảo mật tài khoản',
                'summary' => 'Hướng dẫn thay đổi mật khẩu định kỳ hoặc đổi sang mật khẩu an toàn hơn khi bạn đã đăng nhập vào hệ thống.',
                'intro' => 'Bạn có thể cập nhật mật khẩu trực tiếp trong mục Hồ sơ cá nhân của mình bất kỳ lúc nào nếu bạn vẫn nhớ mật khẩu hiện tại. Việc đổi mật khẩu định kỳ giúp bảo vệ lịch sử học tập và thông tin cá nhân của bạn.',
                'action_label' => 'Đi đến trang Đổi mật khẩu trong Cài đặt',
                'action_url' => route('profile.edit').'#password',
                'action_help' => 'Bạn sẽ được chuyển trực tiếp đến khu vực Cập nhật mật khẩu trong phần Hồ sơ tài khoản.',
                'illustration_type' => 'password_change',
                'device_title' => 'EduLearn - Quản lý Hồ sơ & Bảo mật',
                'steps_heading' => 'Các bước đổi mật khẩu trực tiếp trên máy tính hoặc điện thoại',
                'steps' => [
                    [
                        'step_number' => 1,
                        'title' => 'Đăng nhập vào tài khoản EduLearn',
                        'description' => 'Truy cập vào hệ thống bằng tài khoản của bạn. Nếu chưa đăng nhập, vui lòng điền thông tin để bắt đầu.',
                        'link_label' => 'Đăng nhập ngay',
                        'link_url' => route('login'),
                    ],
                    [
                        'step_number' => 2,
                        'title' => 'Mở trang Hồ sơ cá nhân',
                        'description' => 'Nhấp vào ảnh đại diện hình tròn ở góc trên bên phải thanh Menu Header, sau đó chọn "Hồ sơ cá nhân" từ danh sách thả xuống.',
                        'link_label' => 'Mở Hồ sơ cá nhân',
                        'link_url' => route('profile.edit').'#password',
                    ],
                    [
                        'step_number' => 3,
                        'title' => 'Chuyển sang mục "Quyền riêng tư"',
                        'description' => 'Tại giao diện Cài đặt, chọn thẻ "Quyền riêng tư" để mở khu vực "Thay đổi mật khẩu" kèm ghi chú bảo mật.',
                    ],
                    [
                        'step_number' => 4,
                        'title' => 'Nhập mật khẩu hiện tại và mật khẩu mới',
                        'description' => 'Điền mật khẩu bạn đang sử dụng vào ô "Mật khẩu hiện tại". Sau đó nhập mật khẩu mới và xác nhận lại một lần nữa ở ô bên dưới.',
                    ],
                    [
                        'step_number' => 5,
                        'title' => 'Nhấn "Cập nhật mật khẩu" để hoàn tất',
                        'description' => 'Bấm nút "Cập nhật mật khẩu". Thông báo thành công sẽ hiển thị và mật khẩu mới sẽ có hiệu lực tức thì cho các lần đăng nhập tiếp theo.',
                        'link_label' => 'Thực hiện đổi mật khẩu ngay',
                        'link_url' => route('profile.edit').'#password',
                    ],
                ],
                'notes' => [
                    'Nên sử dụng mật khẩu kết hợp giữa chữ hoa, chữ thường, số và ký tự đặc biệt.',
                    'Không chia sẻ mật khẩu của bạn với bất kỳ ai để bảo vệ khóa học và chứng chỉ của bạn.',
                    'Nếu bạn đã quên mật khẩu hiện tại, hãy dùng tính năng Đặt lại mật khẩu qua email.',
                ],
            ],

            'dat-lai-mat-khau' => [
                'slug' => 'dat-lai-mat-khau',
                'title' => 'Đặt lại mật khẩu tài khoản khi bị quên',
                'category' => 'Tài khoản & Mật khẩu',
                'category_slug' => 'tai-khoan-bao-mat',
                'badge' => 'Khôi phục tài khoản',
                'summary' => 'Cách khôi phục quyền truy cập vào tài khoản EduLearn qua email xác thực an toàn khi bạn không nhớ mật khẩu đăng nhập.',
                'intro' => 'Nếu không thể đăng nhập do quên mật khẩu, bạn chỉ cần một vài thao tác đơn giản để tạo mật khẩu mới an toàn thông qua địa chỉ email đã đăng ký.',
                'action_label' => 'Đi đến trang Quên mật khẩu',
                'action_url' => route('password.request'),
                'action_help' => 'Hệ thống sẽ gửi một mã gồm 6 số, có hiệu lực 10 phút, đến hộp thư email của bạn.',
                'illustration_type' => 'password_reset',
                'device_title' => 'EduLearn - Xác thực Email & Khôi phục mật khẩu',
                'steps_heading' => 'Các bước khôi phục mật khẩu bằng mã xác nhận gửi qua email',
                'steps' => [
                    [
                        'step_number' => 1,
                        'title' => 'Mở biểu mẫu Quên mật khẩu',
                        'description' => 'Tại trang Đăng nhập, nhấp vào liên kết "Quên mật khẩu?" hoặc bấm trực tiếp vào nút bên dưới để mở giao diện yêu cầu.',
                        'link_label' => 'Đến trang Quên mật khẩu',
                        'link_url' => route('password.request'),
                    ],
                    [
                        'step_number' => 2,
                        'title' => 'Nhập địa chỉ Email tài khoản',
                        'description' => 'Điền chính xác địa chỉ Email bạn đã sử dụng khi đăng ký tài khoản học viên hoặc giảng viên trên hệ thống.',
                    ],
                    [
                        'step_number' => 3,
                        'title' => 'Gửi yêu cầu nhận mã xác nhận',
                        'description' => 'Nhấn nút "Gửi mã xác nhận". Hệ thống sẽ tạo mã gồm 6 số và gửi email cho bạn trong vài giây.',
                    ],
                    [
                        'step_number' => 4,
                        'title' => 'Kiểm tra hộp thư đến của bạn',
                        'description' => 'Mở hòm thư điện tử của bạn (kiểm tra cả mục Hộp thư đến lẫn mục Thư rác/Spam). Mở email từ EduLearn và xem mã gồm 6 số.',
                    ],
                    [
                        'step_number' => 5,
                        'title' => 'Nhập mã rồi tạo mật khẩu mới',
                        'description' => 'Nhập mã 6 số và bấm "Xác nhận mã". Mã đúng thì trang tạo mật khẩu mới mới hiện ra: nhập mật khẩu mới, xác nhận lại rồi bấm "Đổi mật khẩu" để đăng nhập trở lại hệ thống học tập.',
                        'link_label' => 'Yêu cầu mã khôi phục ngay',
                        'link_url' => route('password.request'),
                    ],
                ],
                'notes' => [
                    'Mã đặt lại mật khẩu có hiệu lực 10 phút kể từ lúc gửi. Nhập sai 5 lần thì cần gửi lại mã mới.',
                    'Nếu không nhận được email sau vài phút, hãy kiểm tra lại hòm thư Spam hoặc bấm "Gửi lại mã" (cách nhau 60 giây).',
                ],
            ],

            'goi-dang-ky' => [
                'slug' => 'goi-dang-ky',
                'title' => 'Quản lý gói đăng ký và thanh toán học phí',
                'category' => 'Gói đăng ký & Học phí',
                'category_slug' => 'goi-dang-ky-thanh-toan',
                'badge' => 'Gói học & Thanh toán',
                'summary' => 'Tìm hiểu các hình thức học tập trên EduLearn: khóa học miễn phí cộng đồng, khóa học trả phí trọn đời và cách thanh toán an toàn.',
                'intro' => 'EduLearn cung cấp mô hình học tập linh hoạt với hàng trăm khóa học từ cơ bản đến nâng cao. Bạn có thể tham gia các khóa học miễn phí không giới hạn hoặc sở hữu trọn đời các khóa học chuyên sâu với chi phí tối ưu.',
                'action_label' => 'Khám phá danh mục Khóa học & Gói học',
                'action_url' => route('courses.index'),
                'action_help' => 'Xem chi tiết các khóa học đang mở, bảng giá, chương trình ưu đãi và đăng ký học ngay.',
                'illustration_type' => 'subscription',
                'device_title' => 'EduLearn - Danh mục Khóa học & Cổng thanh toán',
                'steps_heading' => 'Các bước chọn khóa học và kích hoạt gói học phí',
                'steps' => [
                    [
                        'step_number' => 1,
                        'title' => 'Duyệt danh mục khóa học',
                        'description' => 'Truy cập trang danh mục khóa học để lọc theo chủ đề (Lập trình, Thiết kế, Kinh doanh, Ngoại ngữ...) hoặc theo mức học phí (Miễn phí / Có phí).',
                        'link_label' => 'Xem danh sách khóa học',
                        'link_url' => route('courses.index'),
                    ],
                    [
                        'step_number' => 2,
                        'title' => 'Xem đề cương và học thử bài mẫu',
                        'description' => 'Bấm vào khóa học bạn quan tâm để xem danh sách bài giảng, video học thử, thông tin giảng viên và phản hồi từ các học viên đi trước.',
                    ],
                    [
                        'step_number' => 3,
                        'title' => 'Chọn "Đăng ký học ngay" hoặc "Ghi danh miễn phí"',
                        'description' => 'Với khóa miễn phí, hệ thống sẽ tự động thêm ngay vào kho khóa học của bạn chỉ với 1 cú click. Với khóa có phí, bạn sẽ được chuyển đến trang thanh toán.',
                    ],
                    [
                        'step_number' => 4,
                        'title' => 'Thực hiện thanh toán học phí',
                        'description' => 'Hệ thống hỗ trợ quét mã QR chuyển khoản ngân hàng tự động (khuyến nghị để kích hoạt tức thì), ví MoMo, VNPAY hoặc thẻ Visa/Mastercard.',
                    ],
                    [
                        'step_number' => 5,
                        'title' => 'Truy cập khóa học trọn đời',
                        'description' => 'Khóa học đã kích hoạt sẽ hiển thị trong trang "Khóa học của tôi" với quyền truy cập trọn đời, cập nhật bài học mới miễn phí vĩnh viễn.',
                        'link_label' => 'Vào trang Khóa học của tôi',
                        'link_url' => route('my-courses'),
                    ],
                ],
                'notes' => [
                    'Mọi khóa học đã thanh toán đều được cấp quyền sở hữu trọn đời (Lifetime Access).',
                    'Bạn có thể học trên bất kỳ thiết bị nào (máy tính, máy tính bảng, điện thoại di động) đồng bộ dữ liệu theo thời gian thực.',
                ],
            ],

            'kich-hoat-khoa-hoc' => [
                'slug' => 'kich-hoat-khoa-hoc',
                'title' => 'Cách truy cập và học tập trên Trình phát bài giảng',
                'category' => 'Khóa học & Học tập',
                'category_slug' => 'khoa-hoc-hoc-tap',
                'badge' => 'Trải nghiệm học tập',
                'summary' => 'Hướng dẫn trải nghiệm phòng học trực tuyến hiện đại: xem bài giảng không quảng cáo, điều chỉnh tốc độ, tải tài liệu và lưu tiến độ.',
                'intro' => 'Trình phát video bài giảng (Learning Player) của EduLearn được thiết kế chuyên biệt cho việc tiếp thu kiến thức: giao diện tối giản chuẩn Apple, hỗ trợ phím tắt, ghi chú bài giảng và tự động ghi nhớ vị trí bạn đang học.',
                'action_label' => 'Đến phòng học trong Khóa học của tôi',
                'action_url' => route('my-courses'),
                'action_help' => 'Xem toàn bộ danh sách các khóa học bạn đã đăng ký và tiếp tục bài học gần nhất.',
                'illustration_type' => 'player',
                'device_title' => 'EduLearn - Phòng học trực tuyến & Video Player',
                'steps_heading' => 'Các bước bắt đầu học bài giảng trực tuyến',
                'steps' => [
                    [
                        'step_number' => 1,
                        'title' => 'Vào trang Khóa học của tôi',
                        'description' => 'Đăng nhập tài khoản và chọn mục "Khóa học của tôi" trên thanh điều hướng hoặc truy cập đường dẫn bên dưới.',
                        'link_label' => 'Vào Khóa học của tôi',
                        'link_url' => route('my-courses'),
                    ],
                    [
                        'step_number' => 2,
                        'title' => 'Nhấp "Tiếp tục học" trên thẻ khóa học',
                        'description' => 'Hệ thống sẽ ghi nhớ chính xác bài học và giây video cuối cùng bạn đang theo dõi để mở ra ngay lập tức.',
                    ],
                    [
                        'step_number' => 3,
                        'title' => 'Tương tác với trình phát bài giảng',
                        'description' => 'Bạn có thể tăng giảm tốc độ phát (0.75x đến 2x), bật chế độ toàn màn hình, xem tài liệu đính kèm bên dưới bài học.',
                    ],
                    [
                        'step_number' => 4,
                        'title' => 'Đánh dấu hoàn thành bài học',
                        'description' => 'Khi hoàn thành xong bài học, nhấn nút "Hoàn thành bài học" để tích lũy % tiến độ khóa học hướng tới mục tiêu nhận chứng chỉ.',
                    ],
                ],
                'notes' => [
                    'Bạn có thể dùng phím Space để Tạm dừng/Phát, phím Mũi tên trái/phải để tua 10 giây.',
                    'Nếu gặp sự cố về âm thanh hoặc video không tải được, hãy thử làm mới trang hoặc kiểm tra kết nối mạng của bạn.',
                ],
            ],

            'chung-chi-hoan-thanh' => [
                'slug' => 'chung-chi-hoan-thanh',
                'title' => 'Điều kiện và hướng dẫn nhận chứng chỉ hoàn thành',
                'category' => 'Chứng chỉ & Đánh giá',
                'category_slug' => 'chung-chi-danh-gia',
                'badge' => 'Chứng chỉ tốt nghiệp',
                'summary' => 'Quy trình thẩm định và cấp chứng chỉ trực tuyến có mã số định danh QR Code sau khi hoàn thành toàn bộ khóa học.',
                'intro' => 'Chứng chỉ EduLearn là minh chứng xác thực cho những kỹ năng và kiến thức thực chiến bạn đã tích lũy. Chứng chỉ đi kèm mã QR xác thực độc nhất, dễ dàng thêm vào Hồ sơ LinkedIn, CV xin việc hoặc portfolio cá nhân.',
                'action_label' => 'Xem danh sách khóa học có chứng chỉ',
                'action_url' => route('courses.index'),
                'action_help' => 'Chọn khóa học và hoàn thành các điều kiện để nhận chứng chỉ chính quy.',
                'illustration_type' => 'certificate',
                'device_title' => 'EduLearn - Chứng nhận tốt nghiệp có mã QR',
                'steps_heading' => 'Các bước đạt điều kiện và nhận chứng chỉ tốt nghiệp',
                'steps' => [
                    [
                        'step_number' => 1,
                        'title' => 'Hoàn thành 100% thời lượng bài học',
                        'description' => 'Theo dõi toàn bộ các video bài giảng và hoàn thành các bài đọc tài liệu trong tất cả các chương.',
                    ],
                    [
                        'step_number' => 2,
                        'title' => 'Vượt qua tất cả bài kiểm tra trắc nghiệm',
                        'description' => 'Làm các bài Quiz trắc nghiệm đánh giá kiến thức sau mỗi chương và đạt điểm số tối thiểu từ 70% trở lên.',
                    ],
                    [
                        'step_number' => 3,
                        'title' => 'Kiểm tra độ chính xác của họ và tên',
                        'description' => 'Chứng chỉ sẽ in đúng tên hiển thị trong hồ sơ của bạn. Bạn nên kiểm tra lại họ tên tại trang Hồ sơ cá nhân trước khi xuất chứng chỉ.',
                        'link_label' => 'Kiểm tra Hồ sơ cá nhân',
                        'link_url' => route('profile.edit'),
                    ],
                    [
                        'step_number' => 4,
                        'title' => 'Nhấn nút "Tải chứng chỉ" trong phòng học',
                        'description' => 'Sau khi thanh tiến độ đạt 100%, nút Tải chứng chỉ sẽ được kích hoạt. Bạn có thể tải file PDF sắc nét chuẩn in ấn hoặc lấy link chia sẻ công khai.',
                    ],
                ],
                'notes' => [
                    'Chứng chỉ EduLearn hoàn toàn miễn phí đi kèm với khóa học đã đăng ký.',
                    'Nhà tuyển dụng có thể quét mã QR trên chứng chỉ để kiểm tra tính hợp lệ trên hệ thống.',
                ],
            ],

            'huong-dan-giang-vien' => [
                'slug' => 'huong-dan-giang-vien',
                'title' => 'Hướng dẫn tạo giáo trình và xuất bản khóa học cho Giảng viên',
                'category' => 'Dành cho Giảng viên',
                'category_slug' => 'giang-vien',
                'badge' => 'Studio Giảng dạy',
                'summary' => 'Bộ hướng dẫn chi tiết dành cho Giảng viên: cách đăng ký tài khoản giảng dạy, tải video, tổ chức chương mục và phát hành bài học.',
                'intro' => 'EduLearn Studio được xây dựng nhằm mang lại sự tự do và thuận tiện nhất cho giảng viên trong việc tạo bài giảng, quản lý học viên và chia sẻ kiến thức hữu ích đến cộng đồng.',
                'action_label' => auth()->check() && (auth()->user()->isInstructor() || auth()->user()->isAdmin()) ? 'Đi đến Studio Giảng viên' : 'Khám phá các khóa học',
                'action_url' => auth()->check() && (auth()->user()->isInstructor() || auth()->user()->isAdmin()) ? route('instructor.dashboard') : route('courses.index'),
                'action_help' => auth()->check() && (auth()->user()->isInstructor() || auth()->user()->isAdmin())
                    ? 'Mở bảng điều khiển giảng dạy để tạo mới hoặc quản lý danh sách các khóa học của bạn.'
                    : 'Lưu ý: Tính năng Studio Giảng viên chỉ dành riêng cho tài khoản Giảng viên và Quản trị viên.',
                'illustration_type' => 'instructor',
                'device_title' => 'EduLearn - Bảng điều khiển Studio Giảng viên',
                'steps_heading' => 'Các bước xuất bản khóa học trên hệ thống EduLearn',
                'steps' => [
                    [
                        'step_number' => 1,
                        'title' => 'Truy cập Studio Giảng viên',
                        'description' => 'Đăng nhập bằng tài khoản giảng viên và chọn "Studio Giảng viên" từ thanh Menu thả xuống ở góc phải trên cùng.',
                        'link_label' => auth()->check() && (auth()->user()->isInstructor() || auth()->user()->isAdmin()) ? 'Vào Studio Giảng viên' : 'Xem danh mục khóa học',
                        'link_url' => auth()->check() && (auth()->user()->isInstructor() || auth()->user()->isAdmin()) ? route('instructor.dashboard') : route('courses.index'),
                    ],
                    [
                        'step_number' => 2,
                        'title' => 'Tạo khóa học mới',
                        'description' => 'Nhấn nút "Tạo khóa học mới", nhập tiêu đề khóa học, chọn danh mục phù hợp, mức học phí và viết nội dung mô tả chi tiết.',
                        'link_label' => auth()->check() && (auth()->user()->isInstructor() || auth()->user()->isAdmin()) ? 'Tạo khóa học ngay' : 'Khám phá khóa học',
                        'link_url' => auth()->check() && (auth()->user()->isInstructor() || auth()->user()->isAdmin()) ? route('instructor.courses.create') : route('courses.index'),
                    ],
                    [
                        'step_number' => 3,
                        'title' => 'Xây dựng cấu trúc chương và bài giảng',
                        'description' => 'Thêm các chương học (Chapters), sau đó tải video bài học hoặc dán liên kết video, kèm file tài liệu bài tập.',
                    ],
                    [
                        'step_number' => 4,
                        'title' => 'Thiết lập bài kiểm tra trắc nghiệm',
                        'description' => 'Tạo các câu hỏi trắc nghiệm sau bài học để củng cố kiến thức và kiểm tra mức độ hiểu bài của học viên.',
                    ],
                    [
                        'step_number' => 5,
                        'title' => 'Gửi kiểm duyệt và xuất bản',
                        'description' => 'Bật trạng thái xuất bản để khóa học hiển thị công khai trên trang chủ và danh mục cho tất cả học viên tham gia học.',
                    ],
                ],
                'notes' => [
                    'Video bài giảng nên có độ phân giải tối thiểu 1080p Full HD với âm thanh rõ ràng, không lẫn tạp âm.',
                    'Đội ngũ quản trị luôn sẵn sàng hỗ trợ giảng viên trong quá trình chuẩn bị giáo trình và xử lý kỹ thuật.',
                ],
            ],
        ];
    }

    /**
     * Frequently asked questions list.
     *
     * @return array<int, array<string, string>>
     */
    protected function getFrequentlyAskedQuestions(): array
    {
        return [
            [
                'question' => 'Làm sao để đổi mật khẩu tài khoản EduLearn của tôi?',
                'answer' => 'Bạn có thể vào Cài đặt tài khoản (nhấp vào ảnh đại diện ở góc phải trên), chọn mục "Quyền riêng tư", nhập mật khẩu hiện tại và mật khẩu mới rồi bấm "Cập nhật mật khẩu".',
                'article_slug' => 'doi-mat-khau',
            ],
            [
                'question' => 'Tôi bị quên mật khẩu đăng nhập, làm cách nào để khôi phục?',
                'answer' => 'Tại trang Đăng nhập, nhấp vào "Quên mật khẩu?", nhập địa chỉ email đã đăng ký của bạn. Hệ thống sẽ gửi cho bạn một email chứa đường dẫn an toàn để thiết lập mật khẩu mới trong vài phút.',
                'article_slug' => 'dat-lai-mat-khau',
            ],
            [
                'question' => 'Khóa học đã đăng ký có thời hạn truy cập trong bao lâu?',
                'answer' => 'Tất cả các khóa học trên EduLearn đều cấp quyền truy cập trọn đời (Lifetime Access). Bạn có thể học bất cứ lúc nào, xem lại bao nhiêu lần tùy thích mà không lo hết hạn.',
                'article_slug' => 'goi-dang-ky',
            ],
            [
                'question' => 'Tôi có thể học trên thiết bị di động (điện thoại, iPad) được không?',
                'answer' => 'Hoàn toàn được! Giao diện EduLearn được thiết kế chuẩn Responsive tương thích hoàn hảo với mọi kích thước màn hình điện thoại iPhone, Android, iPad và máy tính xách tay.',
                'article_slug' => 'kich-hoat-khoa-hoc',
            ],
            [
                'question' => 'Sau khi học xong có được cấp chứng chỉ không và có mất phí không?',
                'answer' => 'Học viên hoàn thành 100% nội dung và vượt qua bài kiểm tra trắc nghiệm sẽ được cấp Chứng chỉ tốt nghiệp điện tử miễn phí, có mã QR xác thực để đính kèm vào CV hoặc LinkedIn.',
                'article_slug' => 'chung-chi-hoan-thanh',
            ],
            [
                'question' => 'Làm sao để đăng ký trở thành Giảng viên trên EduLearn?',
                'answer' => 'Sau khi tạo tài khoản, bạn có thể gửi yêu cầu nâng cấp quyền Giảng viên hoặc truy cập Studio Giảng viên để bắt đầu xây dựng giáo trình và xuất bản các khóa học của mình.',
                'article_slug' => 'huong-dan-giang-vien',
            ],
        ];
    }
}
