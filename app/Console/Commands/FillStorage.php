<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FillStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fillstorage {count}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заполнение хранилища';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        for ($i = 0; $i < (int) $this->argument('count'); $i++) {
            echo $i.PHP_EOL;

            $uuid = Str::uuid();

            echo $uuid.PHP_EOL;
            $levels = [
                Str::substr($uuid, 0, 2),
                Str::substr($uuid, 2, 2),
                Str::substr($uuid, 4, 2)
            ];

            $filename = Str::substr($uuid, 6);

//            print_r($levels);
//            echo $filename.PHP_EOL;

//            dd('public/image_storage/'.implode('/', $levels).$filename.'.jpeg');
            Storage::copy('public/images/17606b1a-fa0b-4a5c-9369-034057a3bc15.png', 'public/image_storage/'.implode('/', $levels).'/'.$filename.'.png');
        }
        return true;
    }
}
