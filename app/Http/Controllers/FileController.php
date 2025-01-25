<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        // Step 1: Validate the file
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,gif|max:2048',
        ]);

        $file = $request->file('file');
        $filePath = $file->getPathname();

        // Step 2: Validate file content using getimagesize()
        $imageInfo = getimagesize($filePath);
        if ($imageInfo === false) {
            return response()->json(['error' => 'The file is not a valid image.'], 400);
        }

        // Step 3: Validate file content using Intervention Image
        try {
            $image = Image::make($filePath);
        } catch (\Exception $e) {
            return response()->json(['error' => 'The file is not a valid image. Line: ' . $e->getLine() . ' Message: ' . $e->getMessage()], 400);
        }

        // Step 4: Scan file content for malicious code
        $fileContent = file_get_contents($filePath);
        if (preg_match('/<\?php/i', $fileContent)) {
            return response()->json(['error' => 'The file contains malicious code.'], 400);
        }

        // Step 5: Re-encode the image to strip out any embedded malicious code
        $fileName = Str::random(40) . '.jpg';
        $cleanImagePath = storage_path('app/uploads/' . $fileName);
        $image->encode('jpg', 75);
        $image->save($cleanImagePath);

        // Step 6: Store the file securely outside the web root
        Storage::disk('local')->put('uploads/' . $fileName, file_get_contents($cleanImagePath));

        // Step 7: Log the upload for monitoring
        Log::info('File uploaded securely:', [
            'filename' => $fileName,
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        // Step 8: Return a success response
        return response()->json([
            'message' => 'File uploaded successfully.',
            'file' => $fileName,
        ]);
    }
}
