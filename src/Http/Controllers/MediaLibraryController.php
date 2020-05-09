<?php

namespace Eonlab\LaravelAdmin\MediaLibrary\Http\Controllers;

use Illuminate\Routing\Controller;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibraryController extends Controller
{
    public function download($id)
    {
        $media = Media::findOrFail($id);

        return response()->download(
            $media->getPath(),
            $media->file_name, ['Content-Type' => $media->mime_type],
            'inline'
        );
    }
}
