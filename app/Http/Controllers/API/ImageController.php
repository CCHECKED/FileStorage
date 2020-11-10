<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageStoreRequest;
use App\Http\Resources\ImageResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    private $path = 'public/images';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = Storage::allFiles($this->path);

        $collection = collect($files)->map(function ($item, $key) {
            return [
                'name' => Str::afterLast($item, '/'),
                'path' => config('app.url') . Storage::url($item)
            ];
        });

        return response()->json(['images' => ImageResource::collection($collection)], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ImageStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ImageStoreRequest $request)
    {
        $request->validated();

        $images = [];

        foreach ($request->input('images') as $image) {
            if (preg_match('/^data:image\/(\w+);base64,/', $image)) {
                $extension = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];

                $data = substr($image, strpos($image, ',') + 1);

                $data = base64_decode($data);

                $filename = Str::uuid() . '.' . $extension;
                Storage::put($this->path . '/' . $filename, $data);

                $images[] = [
                    'name' => $filename,
                    'path' => config('app.url') . Storage::url($this->path . '/' . $filename)
                ];
            }
        }

        return response()->json(['images' => ImageResource::collection($images)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $name
     * @return \Illuminate\Http\Response
     */
    public function destroy($name)
    {
        if (Storage::exists($this->path . '/' . $name)) {
            Storage::delete($this->path . '/' . $name);
        } else {
            return response()->json([
                'message' => 'Изображение не найдено.'
            ], 404);
        }


        return response()->json([
            'message' => 'Изображение удалено.'
        ], 200);
    }
}
