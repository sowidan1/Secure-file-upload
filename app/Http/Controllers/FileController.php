<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        // Step 1: Validate the file --> image
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,gif|max:2048',
        ]);

        // Or Step 1: Validate the file --> add document

        // $request->validate([
        //     'file' => 'required|file|mimes:jpeg,png,gif,pdf,docx,zip|max:20480',
        // ]);

        $file = $request->file('file');
        $filePath = $file->getPathname();

        // Step 2: Validate file content using getimagesize()
        $imageInfo = getimagesize($filePath);
        if ($imageInfo === false) {
            return response()->json(['error' => 'The file is not a valid image.'], 400);
        }

        // Step 3: Validate file content using Intervention Image
        try {
            $image = Image::read($filePath);
        } catch (\Exception $e) {
            return redirect()->back()->with(
                'error',
                'The file is not a valid image. Line: ' . $e->getLine() . ' Message: ' . $e->getMessage()
            );
        }

        // Step 4: Scan file content for malicious code
        $fileContent = file_get_contents($filePath);
        if (preg_match('/<\?php/i', $fileContent)) {
            return redirect()->back()->with('error', 'The file contains malicious code.');
        }

        // Step 5: Re-encode the image to strip out any embedded malicious code
        $fileName = Str::random(40) . '.jpg';
        $cleanImagePath = storage_path('app/clean/' . $fileName);
        $image->encode()->save($cleanImagePath);

        // Step 6: Store the file securely outside the web root
        Storage::disk('local')->put('uploads/' . $fileName, file_get_contents($cleanImagePath));

        // Step 7: Log the upload for monitoring
        Log::info('File uploaded securely:', [
            'filename' => $fileName,
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        // Step 8: Return a success response
        return redirect()->route('dashboard')->with('success', 'File uploaded successfully.');
    }
}
