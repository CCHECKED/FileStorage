<?php

namespace App\Console\Commands;

use App\Models\TempFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearTempImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:images:temp:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистка временных изображений';

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
        TempFile::where('created_at', '<', now()->addDays(-1))->chunk(100, function ($images) {
            foreach ($images as $image) {
                echo 'Удалено: '.$image->path.PHP_EOL;
                if($image->delete()) {
                    Storage::delete($image->path);
                }
            }
        });

        return 0;
    }
}
