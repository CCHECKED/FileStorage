<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class ShowImages extends Component
{
    public $images;

    public function mount()
    {
        $files = Storage::allFiles('public/images');

        $this->images = collect($files)->map(function ($item) {
            return [
                'name' => Str::afterLast($item, '/'),
                'path' => config('app.url') . Storage::url($item)
            ];
        });
    }

    public function render()
    {
        return view('livewire.show-images');
    }
}
