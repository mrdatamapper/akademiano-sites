<?php


namespace Akademiano\Sites\Site;


use Akademiano\HttpWarp\Exception\AccessDeniedException;
use Akademiano\Utils\FileSystem;

class ThemesDir extends Directory
{
    /** @var Theme[] */
    protected $themes = [];

    public function __construct($path)
    {
        $this->setPath($path);
    }

    public function getTheme($name)
    {
        if (!key_exists($name, $this->themes)) {
            $themePath = realpath($this->getPath() . DIRECTORY_SEPARATOR . $name);
            if (!is_dir($themePath)) {
                $this->themes[$name] = false;
            } else {
                if (!FileSystem::inDir($this->getPath(), $themePath)) {
                    throw new AccessDeniedException();
                }
                $this->themes[$name] = new Theme($name, $themePath);
            }
        }
        return (false !== $this->themes[$name]) ? $this->themes[$name] : null;
    }
}
