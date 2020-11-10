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

        if ($request->file('images')) {
            $path = $this->getPath();
            foreach ($request->file('images') as $image) {
                $extension = Str::afterLast($image['file']->getMimeType(), '/');
                $filename = $path['filename'] . '.' . $extension;

                Storage::putFileAs($this->path . '/' . $path['levels'], $image['file'], $filename);

                $images[] = [
                    'name' => $filename,
                    'path' => config('app.url') . Storage::url($this->path . '/' . $path['levels'] . '/' . $filename)
                ];
            }
        }

        if ($request->input('images')) {
            foreach ($request->input('images') as $image) {
                $path = $this->getPath();
                if ($image['file']) {
                    dd($image);
                } elseif (isset($image['base64'])) {
                    $image = $image['base64'];
                    if (preg_match('/^data:image\/(\w+);base64,/', $image)) {
                        $extension = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];

                        $data = substr($image, strpos($image, ',') + 1);

                        $data = base64_decode($data);

                        $filename = $path['filename'] . '.' . $extension;
                        Storage::put($this->path . '/' . $path['levels'] . '/' . $filename, $data);

                        $images[] = [
                            'name' => $filename,
                            'path' => config('app.url') . Storage::url($this->path . '/' . $path['levels'] . '/' . $filename)
                        ];
                    }
                } elseif (isset($image['url'])) {
                    try {
                        $extension = Str::afterLast(image_type_to_mime_type(exif_imagetype($image['url'])), '/');
                        $filename = Str::uuid() . '.' . $extension;

                        $data = file_get_contents($image['url']);

                        $filename = $path['filename'] . '.' . $extension;
                        Storage::put($this->path . '/' . $path['levels'] . '/' . $filename, $data);

                        $images[] = [
                            'name' => $filename,
                            'path' => config('app.url') . Storage::url($this->path . '/' . $path['levels'] . '/' . $filename)
                        ];
                    } catch (\Exception $exception) {
                        //
                    }
                }
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

    private function getPath()
    {
        $uuid = Str::uuid();
        return [
            'uuid' => $uuid,
            'levels' => implode('/', [
                Str::substr($uuid, 0, 2),
                Str::substr($uuid, 2, 2),
                Str::substr($uuid, 4, 2)
            ]),
            'filename' => Str::substr($uuid, 6)
        ];
    }
}
