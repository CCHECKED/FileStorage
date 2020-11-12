<?php

namespace App\Http\Controllers\API\Images;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageSaveRequest;
use App\Http\Resources\ImageResource;
use App\Models\TempFile;
use Illuminate\Support\Facades\Storage;

class SaveController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param App\Http\Requests\ImageSaveRequest $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ImageSaveRequest $request)
    {
        $images = TempFile::where('hash', $request->input('hash'))->get();

        if (!TempFile::where('hash', $request->input('hash'))->delete()) {
            return response()->json(['message' => 'Ошибка сохранения изображений.'], 500);
        }

        $images = $images->map(function ($item) {
            return ['path' => (config('app.url') . Storage::url($item['path']))];
        });

        return response()->json(['images' => $images], 200);
    }
}
