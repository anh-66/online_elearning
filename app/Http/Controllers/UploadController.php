<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UploadController extends Controller
{
    /**
     * Handle CKEditor 5 image upload.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
        ]);

        try {
            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
                $filename = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $path = $file->storeAs('editor', $filename, 'public');

                return response()->json([
                    'url' => Storage::disk('public')->url($path),
                ]);
            }

            return response()->json([
                'error' => [
                    'message' => 'Không tìm thấy file ảnh tải lên.',
                ],
            ], 400);
        } catch (Throwable $e) {
            return response()->json([
                'error' => [
                    'message' => 'Tải ảnh lên thất bại: '.$e->getMessage(),
                ],
            ], 500);
        }
    }
}
