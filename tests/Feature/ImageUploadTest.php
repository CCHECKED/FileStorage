<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        // Получаем хеш

        $hash = $this->json('GET', '/api/getHash');

        $hash->assertStatus(200);

        $hash_json = $hash->decodeResponseJson();

        echo $hash_json['hash'].PHP_EOL;

        // Загружаем файл
        $images = [];
        for ($i = 0; $i < mt_rand(1, 10); $i++) {
            $images[] = [
                'file' => UploadedFile::fake()->image('image.png')
            ];
        }

        $upload_image = $this->post('/api/images', [
            'hash' => $hash_json['hash'],
            'images' => $images
        ]);

        echo $upload_image->getContent().PHP_EOL;
        $upload_image_json = $upload_image->decodeResponseJson();

        $upload_image->assertStatus(200);


        foreach ($upload_image_json['images'] as $image) {
            $path = str_replace('http://localhost:8000/storage', 'public', $image['path']);
            // Проверяем есть ли файл
            Storage::assertExists($path);
            // Проверяем есть ли в БД
            $this->assertDatabaseHas('temp_files', [
                'hash' => $hash_json['hash'],

            ]);
        }

        // Сохраняем
        $save = $this->json('POST', '/api/images/save', [
            'hash' => $hash_json['hash']
        ]);

        $save->assertStatus(200);

        echo $save->getContent().PHP_EOL;

        foreach ($upload_image_json['images'] as $image) {
            $path = str_replace('http://localhost:8000/storage', 'public', $image['path']);
            // Проверяем удалены ли файлы из временных
            $this->assertDeleted('temp_files', [
                'hash' => $hash_json['hash'],
                'path' => $path
            ]);
        }

    }
}
