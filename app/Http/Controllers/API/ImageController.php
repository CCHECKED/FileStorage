<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageStoreRequest;
use App\Http\Resources\ImageResource;
use App\Models\TempFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    private $path = 'public/images';

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
                $filename_path = $path['levels'] . '/' . $filename;

                Storage::putFileAs($this->path . '/' . $path['levels'], $image['file'], $filename);

                TempFile::create([
                    'hash' => $request->input('hash'),
                    'path' => $this->path . '/' . $path['levels'] . '/' . $filename
                ]);

                $images[] = [
                    'name' => $filename,
                    'name_path' => $filename_path,
                    'path' => config('app.url') . Storage::url($this->path . '/' . $filename_path)
                ];
            }
        }

        if ($request->input('images')) {
            foreach ($request->input('images') as $image) {
                $path = $this->getPath();
                if (isset($image['base64'])) {
                    $image = $image['base64'];
                    if (preg_match('/^data:image\/(\w+);base64,/', $image)) {
                        $extension = explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];

                        $data = substr($image, strpos($image, ',') + 1);

                        $data = base64_decode($data);

                        $filename = $path['filename'] . '.' . $extension;
                        $filename_path = $path['levels'] . '/' . $filename;

                        Storage::put($this->path . '/' . $filename_path, $data);
                        TempFile::create([
                            'hash' => $request->input('hash'),
                            'path' => $this->path . '/' . $filename_path
                        ]);

                        $images[] = [
                            'name' => $filename,
                            'name_path' => $filename_path,
                            'path' => config('app.url') . Storage::url($this->path . '/' . $filename_path)
                        ];
                    }
                } elseif (isset($image['url'])) {
                    try {
                        $extension = Str::afterLast(image_type_to_mime_type(exif_imagetype($image['url'])), '/');
                        $filename = Str::uuid() . '.' . $extension;

                        $data = file_get_contents($image['url']);

                        $filename = $path['filename'] . '.' . $extension;
                        $filename_path = $path['levels'] . '/' . $filename;

                        Storage::put($this->path . '/' . $filename_path, $data);
                        TempFile::create([
                            'hash' => $request->input('hash'),
                            'path' => $this->path . '/' . $filename_path
                        ]);

                        $images[] = [
                            'name' => $filename,
                            'name_path' => $filename_path,
                            'path' => config('app.url') . Storage::url($this->path . '/' . $filename_path)
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
    public function destroy(... $data)
    {
        $path = $this->path.'/'.implode('/', $data);

        if (Storage::exists($path)) {
            Storage::delete($path);
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
