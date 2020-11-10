<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function __invoke(string $filename)
    {
        $levels = [
            Str::substr($filename, 0, 2),
            Str::substr($filename, 2, 2),
            Str::substr($filename, 4, 2)
        ];
        $file = Str::substr($filename, 6);

        if (Storage::exists('public/image_storage/' . implode('/', $levels) . '/' . $file)) {
            return response(Storage::get('public/image_storage/' . implode('/', $levels) . '/' . $file))->header('Content-type','image/png');
        } else {
            return response('Файл не найден', 404);
        }
    }
}
