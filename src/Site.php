<?php


namespace Akademiano\Sites;


use Akademiano\SimplaView\AbstractView;
use Akademiano\Sites\Site\ConfigDir;
use Akademiano\Sites\Site\DataStore;
use Akademiano\Sites\Site\PublicStore;
use Akademiano\Sites\Site\Theme;
use Akademiano\Sites\Site\ThemesDir;
use Composer\Autoload\ClassLoader;

abstract class Site implements SiteInterface
{
    const NAMESPASE_PART_PREFIX = "\\Site\\";
    const NAMESPASE_PART_PREFIX_LENGTH = 6;
    const NAMESPASE_PART_SUFFIX_LENGTH = 5;

    protected $rootDir;

    protected $name;
    protected $path;

    /** @var  DataStore */
    protected $dataStore;

    protected $publicGlobalPath;

    protected $publicWebPath;

    /** @var  PublicStore */
    protected $publicStore;

    /** @var  ClassLoader */
    protected $loader;

    /** @var  ThemesDir */
    protected $themesDir;

    /** @var  ConfigDir */
    protected $configDir;

    /**
     * Site constructor.
     * @param ClassLoader $loader
     */
    public function __construct(ClassLoader $loader)
    {
        $this->setLoader($loader);
    }


    /**
     * @return ClassLoader
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @param ClassLoader $loader
     */
    public function setLoader($loader)
    {
        $this->loader = $loader;
    }

    /**
     * @return mixed
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            if (defined("ROOT_DIR")) {
                $this->rootDir = ROOT_DIR;
            } else {
                throw new \RuntimeException("Root dir is not defined");
            }
        }
        return $this->rootDir;
    }

    /**
     * @param mixed $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if (null === $this->name) {
            $class = get_class($this);
            $this->name = strtolower(
                substr(
                    $class,
                    strpos($class, self::NAMESPASE_PART_PREFIX) + self::NAMESPASE_PART_PREFIX_LENGTH,
                    -self::NAMESPASE_PART_SUFFIX_LENGTH
                )
            );
        }
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    protected function getPath()
    {
        if (null === $this->path) {
            $class = get_class($this);
            $file = $this->getLoader()->findFile($class);
            $dir = realpath(dirname($file));
            $this->path = $dir;
        }
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }


    /**
     * @return DataStore
     */
    public function getDataStore()
    {
        return $this->dataStore;
    }

    /**
     * @param DataStore $dataStore
     */
    public function setDataStore($dataStore)
    {
        $this->dataStore = $dataStore;
    }

    /**
     * @return mixed
     */
    public function getPublicGlobalPath()
    {
        if (null === $this->publicGlobalPath) {
            $publicGlobalPath = $this->getRootDir() . DIRECTORY_SEPARATOR . PublicStore::GLOBAL_DIR
                . DIRECTORY_SEPARATOR . $this->getName();
            if (!is_dir($publicGlobalPath)) {
                $created = mkdir($publicGlobalPath, 0750);
                if (!$created) {
                    throw new \RuntimeException(sprintf('Could not create public store directory "%s"', $publicGlobalPath));
                }
            }
            $this->publicGlobalPath = $publicGlobalPath;
        }
        return $this->publicGlobalPath;
    }

    /**
     * @param mixed $publicGlobalPath
     */
    public function setPublicGlobalPath($publicGlobalPath)
    {
        $this->publicGlobalPath = $publicGlobalPath;
    }

    /**
     * @return mixed
     */
    public function getPublicWebPath()
    {
        if (null === $this->publicWebPath) {
            if (!$this->getPublicGlobalPath()) {
                throw new \RuntimeException(sprintf('Not exist public store directory'));
            }
            $this->publicWebPath = "/" . PublicStore::GLOBAL_DIR . "/" . $this->getName();
        }
        return $this->publicWebPath;
    }

    /**
     * @param mixed $publicWebPath
     */
    public function setPublicWebPath($publicWebPath)
    {
        $this->publicWebPath = $publicWebPath;
    }

    /**
     * @return PublicStore|null
     */
    public function getPublicStore()
    {
        if (null === $this->publicStore) {
            $publicInternalPath = realpath($this->getPath() . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . PublicStore::INTERNAL_DIR);
            if (!is_dir($publicInternalPath)) {
                $this->publicStore = false;
            } else {
                $publicStore = new PublicStore($publicInternalPath, $this->getPublicGlobalPath(), $this->getPublicWebPath());
                $this->publicStore = $publicStore;
            }
        }
        return (false !== $this->publicStore) ? $this->publicStore : null;
    }

    /**
     * @param PublicStore $publicStore
     */
    public function setPublicStore(PublicStore $publicStore)
    {
        $this->publicStore = $publicStore;
    }

    /**
     * @return ThemesDir
     */
    public function getThemesDir()
    {
        if (null === $this->themesDir) {
            $themesPath = $this->getPath() . DIRECTORY_SEPARATOR . AbstractView::THEMES_DIR;
            if (!is_dir($themesPath)) {
                $this->themesDir = false;
            } else {
                $themesDir = new ThemesDir($themesPath);
                $this->themesDir = $themesDir;
            }
        }
        return (false !== $this->themesDir) ? $this->themesDir : null;
    }

    /**
     * @param mixed $themesDir
     */
    public function setThemesDir(ThemesDir $themesDir)
    {
        $this->themesDir = $themesDir;
    }

    /**
     * @param $theme
     * @return Theme|null
     */
    public function getTheme($theme)
    {
        if ($this->getThemesDir()) {
            return $this->getThemesDir()->getTheme($theme);
        }
    }

    /**
     * @return ConfigDir
     */
    public function getConfigDir()
    {
        if (null === $this->configDir) {
            $configPath = $this->getPath() . DIRECTORY_SEPARATOR . \Akademiano\Config\ConfigLoader::NAME_CONFIG;
            if (!is_dir($configPath) || !is_readable($configPath)) {
                $this->configDir = false;
            } else {
                $this->configDir = new ConfigDir($configPath);
            }
        }
        return (false !== $this->configDir) ? $this->configDir : null;
    }

    /**
     * @param ConfigDir $configDir
     */
    public function setConfigDir($configDir)
    {
        $this->configDir = $configDir;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
