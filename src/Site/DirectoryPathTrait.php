<?php


namespace Akademiano\Sites\Site;


trait DirectoryPathTrait
{
    protected $path;

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    public function __toString()
    {
        return $this->getPath();
    }
}
