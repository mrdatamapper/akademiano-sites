<?php


namespace Akademiano\Sites\Site;


class Directory implements DirectoryInterface
{
    use DirectoryFilesTrait;

    /**
     * Directory constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->setPath($path);
    }

}
