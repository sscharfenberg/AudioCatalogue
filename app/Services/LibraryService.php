<?php
namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class LibraryService {

    /**
     * @function get file list from smb csv file, parse it and return a collection of file paths
     * @param string $filename
     * @param string $path
     * @return Collection
     */
    public function getFileList(string $filename, string $path): Collection
    {
        $csv = file_get_contents($path.$filename);

        if (strlen($csv) == 0) {
            Log::channel('lib')->error("error, csv file is empty");
        } else {
            Log::channel('lib')->debug("csv file has ".strlen($csv)." bytes.");
        }

        // map string to a laravel collection, trim leading path,
        // prune empty items, and return
        return collect(explode("\n", $csv))
            ->filter( function(string $value) {
                return !empty($value);
            })->map(function (string $item) use ($path) {
                return str_replace($path, '', $item);
            });
    }

    /**
     * @function return the pure number value of track/disc, not x/y
     * @param string $track
     * @return int
     */
    public function getNum(string $track): int
    {
        if (str_contains($track, '/')) {
            return intval(explode('/', $track)[0]);
        } else {
            return intval($track);
        }
    }

    /**
     * @function format a search result item
     * @param string $to
     * @param string $id
     * @param string $label
     * @param string $aside
     * @param string $icon
     * @return string[]
     */
    public function formatSearchItem(string $to, string $id, string $label, string $aside, string $icon): array
    {
        return [
            'link' => [
                'to' => $to,
                'param' => $id
            ],
            'label' => $label,
            'aside' => $aside,
            'icon' => $icon,
        ];
    }

}
