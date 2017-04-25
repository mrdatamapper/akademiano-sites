<?php


namespace Akademiano\Sites\Site;


use Akademiano\Utils\FileSystem;

trait DirectoryFilesTrait
{
    use DirectoryPathTrait;

    protected $files = [];


    protected function createFile($path)
    {
        return new File($path);
    }

    public function getFile($fileName)
    {
        if (!array_key_exists($fileName, $this->files)) {
            $filePath = $this->getPath() . DIRECTORY_SEPARATOR . $fileName;
            if (!file_exists($filePath) || !is_readable($filePath)
                || !FileSystem::isFileInDir($this->getPath(), $filePath)
            ) {
                $this->files[$fileName] = null;
            } else {
                $this->files[$fileName] = $this->createFile($filePath);
            }
        }
        return $this->files[$fileName];
    }
}
