<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageController extends Controller
{
    private $path = 'public/images';

    /**
     *
     * @param int $width
     * @param int $height
     * @param string $name
     * @return \Illuminate\Http\Response
     */
    public function __invoke(int $width, int $height, string $name)
    {
        if (Storage::exists($this->path . '/' . $name)) {
            $image = Image::make(Storage::get($this->path . '/' . $name));

            if($width < $height) {
                $image->heighten($height);
            } else {
                $image->widen($width);
            }

            return $image->response(\Str::afterLast($image->mime(), '/'));
        } else {
            return response('Изображение не найдено.', 404);
        }
    }
}
