<?php


namespace Akademiano\Sites\Site;


class PublicStore extends FlowDirectory
{
    const GLOBAL_DIR = "published";
    const INTERNAL_DIR = "public";

    protected $webPath;

    public function __construct($internalPath, $globalPath, $webPath)
    {
        parent::__construct($internalPath, $globalPath);
        $this->setWebPath($webPath);
    }


    protected function createFile($path)
    {
        $file = new PublicFile($path);
        $file->setWebPath($this->getWebPath());
        return $file;
    }

    /**
     * @return mixed
     */
    public function getWebPath()
    {
        return $this->webPath;
    }

    /**
     * @param mixed $webPath
     */
    public function setWebPath($webPath)
    {
        $this->webPath = $webPath;
    }

    /**
     * @param $relativeFilePath
     * @return PublicFile|null
     */
    public function getFile($relativeFilePath)
    {
        /** @var PublicFile $file */
        $file = parent::getFile($relativeFilePath);
        if (!$file) {
            return null;
        }

        $fileWebPath = $this->getWebPath() . "/" . $relativeFilePath;
        $file->setWebPath($fileWebPath);
        return $file;
    }
}
