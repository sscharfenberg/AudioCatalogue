<?php

namespace App\Console\Commands;

use App\Services\AlbumService;
use App\Services\FormatService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class Cleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup storage folders and remove unwanted files from samba share.';

    /**
     * @function clean samba share of unwanted files
     * @return void
     */
    private function cleanSambaShare(): void
    {
        $areas = [
            config('collection.server.music.path'),
            config('collection.server.audiobooks.path')
        ];
        foreach($areas as $area) {
            $this->cleanArea($area);
        }
    }

    /**
     * @function delete files in an area ['music', 'audiobooks']
     * @param string $path
     * @return void
     */
    private function cleanArea(string $path): void
    {
        foreach(config('collection.server.to_delete') as $mask) {
            $this->info("deleting $mask in $path");
            Log::channel('lib')->info("deleting $mask in $path");
            $result = Process::run('find '.$path.' -follow -type f -iname '.$mask.' -print -delete');
            if (strlen($result->output()) > 0) {
                $this->info("found and deleted $mask files:");
                $this->line($result->output());
                Log::channel('lib')->debug("deleted files:\n\n".$result->output());
            }
        }
    }

    /**
     * @function delete laravel storage/public
     * @return void
     */
    private function cleanStorage(): void
    {

        // clean public disk
        $files = Storage::disk('public')->allFiles();
        $this->info("deleting laravel folder storage/public - ".count($files)." files found.");
        Log::channel('lib')->info("deleting laravel folder storage/public - ".count($files)." files found.");
        foreach($files as $file) {
            if ($file != 'sprite.svg') {
                Storage::disk('public')->delete($file);
                Log::channel('lib')->debug("deleted $file");
                $this->line("deleted '".$file);
            }
        }

        // clean downloads disk
        $directories = Storage::disk('downloads')->directories();
        $this->info("pruning directories from downloads disk - ".count($directories)." directories found.");
        Log::channel('lib')->info("pruning directories from downloads disk - ".count($directories)." directories found.");
        foreach ($directories as $directory) {
            if (Storage::disk('downloads')->deleteDirectory($directory)) {
                Log::channel('lib')->debug("Deleted directory '".$directory."'.");
                $this->line("Deleted directory '".$directory."'.");
            };
        }
        $allFiles = Storage::disk('downloads')->files();
        $this->info("pruning files from downloads disk - ".count($allFiles)." files found.");
        Log::channel('lib')->info("pruning files from downloads disk - ".count($allFiles)." files found.");
        foreach ($allFiles as $file) {
            if (str_ends_with($file, ".zip")) {
                Storage::disk('downloads')->delete($file);
                Log::channel('lib')->debug("Deleted file '".$file."'.");
                $this->line("Deleted file '".$file."'.");
            }
        }
    }

    /**
     * Execute the console command.
     */
    public function handle( FormatService $fd): void
    {
        $start = now();
        $this->comment("artisan command 'app:clean' started.");
        Log::channel('lib')->info("=======================================================");
        Log::channel('lib')->info("artisan command 'app:clean' started.");
        Log::channel('lib')->info("=======================================================");
        $this->cleanStorage(); // begin by cleanup up storage folder
        $this->cleanSambaShare();
        $ms = $start->diffInMilliseconds(now());
        Log::channel('lib')->info("=======================================================");
        Log::channel('lib')->info("finished artisan command 'app:clean' in ".$fd->formatMs($ms).".");
        Log::channel('lib')->info("=======================================================");
        $this->comment("finished artisan command 'app:clean' in ".$fd->formatMs($ms).".");
    }
}
