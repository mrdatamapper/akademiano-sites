<?php


namespace Akademiano\Sites\Site;


use Akademiano\HttpWarp\Exception\AccessDeniedException;
use Akademiano\Utils\FileSystem;

class FlowDirectory extends Directory implements FlowDirectoryInterface
{
    protected $internalPath;
    protected $globalPath;

    public function __construct($internalPath, $globalPath)
    {
        $this->setInternalPath($internalPath);
        $this->setGlobalPath($globalPath);
    }

    /**
     * @return mixed
     */
    public function getInternalPath()
    {
        return $this->internalPath;
    }

    /**
     * @param mixed $internalPath
     */
    public function setInternalPath($internalPath)
    {
        $this->internalPath = $internalPath;
    }

    /**
     * @return mixed
     */
    public function getGlobalPath()
    {
        return $this->globalPath;
    }

    /**
     * @param mixed $globalPath
     */
    public function setGlobalPath($globalPath)
    {
        $this->globalPath = $globalPath;
    }

    public function setPath($path)
    {
        $this->setInternalPath($path);
    }

    public function getPath()
    {
        return $this->getGlobalPath();
    }

    /**
     * @param $relativeFilePath
     * @return File|null
     * @throws AccessDeniedException
     */
    public function getFile($relativeFilePath)
    {
        if (!array_key_exists($relativeFilePath, $this->files)) {
            $filePath = $this->getGlobalPath() . DIRECTORY_SEPARATOR . $relativeFilePath;
            if (!file_exists($filePath)) {
                $fileInternalPath = $this->getInternalPath() . DIRECTORY_SEPARATOR . $relativeFilePath;
                if (!file_exists($fileInternalPath) || !is_readable($fileInternalPath)) {
                    $filePath = null;
                } else {
                    if (!FileSystem::isFileInDir($this->getInternalPath(), $fileInternalPath)) {
                        throw new AccessDeniedException(sprintf('Not allow access to file "%s"', $relativeFilePath));
                    }
                    FileSystem::copyOrThrow($fileInternalPath, $filePath);
                }
            }
            $this->files[$relativeFilePath] = $filePath ? $this->createFile($filePath) : null;
        }
        return $this->files[$relativeFilePath];
    }

    public function __toString()
    {
        return $this->getGlobalPath();
    }
}
