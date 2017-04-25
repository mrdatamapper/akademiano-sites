<?php


namespace Akademiano\Sites\Site;


class ConfigDir extends \Akademiano\Config\FS\ConfigDir implements DirectoryInterface
{
    use DirectoryFilesTrait;

    /**
     * ConfigDir constructor.
     * @param $path
     * @param $level
     */
    public function __construct($path, $level = \Akademiano\Config\FS\ConfigDir::LEVEL_DEFAULT)
    {
        parent::__construct($path, $level);
    }
}
