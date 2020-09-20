<?php

namespace App\Http\Controllers;

use App\Http\Resources\MediaResource;
use App\TempMedia;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaController extends Controller
{
    public function getImage(Media $image)
    {
        return new MediaResource($image);
    }

    public function storeImage(Request $request)
    {
        $file = $request->file('file');

        /** @var UploadedFile $file */
        $fileName = Str::orderedUuid()->toString() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('/temp/image', $fileName);

        TempMedia::create([
            'file_name' => $fileName,
            'original_file_name' => $file->getClientOriginalName(),
            'path' => $filePath,
        ]);

        return response()->json([
            'file' => $fileName
        ]);
    }
}
