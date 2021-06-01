<?php
namespace Retroace\Storage;

use League\Flysystem\Filesystem as FlyFilesystem;
use League\Flysystem\Util;

class Filesystem extends FlyFilesystem
{

    /**
     * @inheritdoc
     */
    public function listContents($directory = '', $recursive = false)
    {
        $directory = Util::normalizePath($directory);
        $contents = $this->getAdapter()->listContents($directory, $recursive);
        $contents = array_map(function ($path) {
            return [
                "type" => str_contains($path, ".") ? "file": "directory",
                "path" => $path
            ];
        }, $contents);
        return $contents;
    }
}
