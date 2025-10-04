<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'media' => 'required|file|mimes:jpeg,png,gif,webp,mp3,wav,ogg,m4a|max:5120', // 5MB
        ]);

        try {
            $file = $request->file('media');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            
            // Simpan file ke storage/app/public/media
            $path = $file->storeAs('public/media', $filename);
            
            // Return URL yang bisa diakses
            return response()->json([
                'success' => true,
                'url' => Storage::url($path),
                'filename' => $filename,
                'message' => 'File berhasil diupload'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload file: ' . $e->getMessage()
            ], 500);
        }
    }
}
