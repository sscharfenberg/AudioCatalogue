<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use wapmorgan\Mp3Info\Mp3Info;

class CoverService
{

    /**
     * @function get cover path, ensure storage has the correct file
     * @param string $id ID of song|track
     * @param string $path
     * @param string $area
     * @param bool $hasCover
     * @param bool $thumb
     * @return string
     * @throws \Exception
     */
    public function getCover(string $id, string $path, string $area = 'music', bool $hasCover = false, bool $thumb = false): string
    {
        $storagePath = $this->getStorageFileName($id, $thumb);

        // if the file already exists in storage, return early with path.
        if(!Storage::disk('public')->missing($storagePath)) {
            Log::channel('api')->info("image '$storagePath' already present in storage.");
            return "/storage/".$storagePath;
        }

        // no image in storage, create it.
        Log::channel('api')->info("image '$storagePath' missing in storage.");
        if ($hasCover) { // mp3 file has inline cover
            $this->getInlineCover(
                config('collection.server.'.$area.'.path').$path,
                $storagePath,
                $thumb
            );
        }
        // no inline cover, lets try and find an external folder image
        else if ( file_exists(config('collection.server.'.$area.'.path').$path) ) {
            $this->getFolderImage(
                config('collection.server.'.$area.'.path').$path,
                $storagePath,
                $thumb
            );
        }
        // no inline cover, no folder image
        else {
            return "";
        }

        return "/storage/".$storagePath;
    }

    /**
     * @function get the filename of the storage file
     * @param string $id
     * @param bool $thumb
     * @return string
     */
    private function getStorageFileName(string $id, bool $thumb): string
    {
        return $thumb ? $id."-thumb.jpg" : $id.".jpg";
    }

    /**
     * @function get mp3 inline image and copy it to storage
     * @param string $filePath
     * @param string $storagePath
     * @param bool $thumb
     * @return void
     * @throws \Exception
     */
    private function getInlineCover(string $filePath, string $storagePath, bool $thumb = false): void
    {
        $audio = new Mp3Info($filePath, true);
        $image =  Image::read($audio->getCover());
        $image = $this->scaleImage($image, $thumb);
        Storage::disk('public')
            ->put($storagePath, $image->encode());
        if (!Storage::disk('public')->missing($storagePath)) {
            Log::channel('api')->debug("image '$storagePath' created from inline mp3 cover.");
        }
    }

    /**
     * @function get external folder image and copy it to storage
     * @param string $filePath
     * @param string $storagePath
     * @param bool $thumb
     * @return void
     */
    private function getFolderImage(string $filePath, string $storagePath, bool $thumb = false): void
    {
        $path = explode('/', $filePath);
        array_pop($path); // remove last entry; filename of the song.
        $path[] = config('collection.coverFile.name'); // replace last entry with name of cover image, i.e. "Folder.jpg"
        $coverPath = implode('/', $path); // put path string back together
        $image = Image::read(file_get_contents($coverPath));
        $image = $this->scaleImage($image, $thumb);
        Storage::disk('public')
            ->put($storagePath, $image->encode());
        if (!Storage::disk('public')->missing($storagePath)) {
            Log::channel('api')->debug("image '$storagePath' created from external folder image.");
        }
    }

    /**
     * @function function that branches out to resize to thumbnail or "full" cover.
     * @param mixed $image
     * @param bool $thumb
     * @return mixed
     */
    private function scaleImage (mixed $image, bool $thumb = false): mixed
    {
        if ($thumb) {
            return $image
                ->scaleDown(width: config('collection.server.thumb_width'));
        } else {
            return $image
                ->scaleDown(width: config('collection.server.cover_width'));
        }
    }

}
