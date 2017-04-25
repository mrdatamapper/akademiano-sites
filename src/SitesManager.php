<?php


namespace Akademiano\Sites;


use Akademiano\Sites\Exception\InvalidSiteNameException;
use Akademiano\Sites\Exception\NoAnySiteException;
use Akademiano\HttpWarp\Environment;
use Akademiano\HttpWarp\EnvironmentIncludeInterface;
use Akademiano\HttpWarp\Parts\EnvironmentIncludeTrait;
use Akademiano\Sites\Exception\NoSharedSiteException;
use Composer\Autoload\ClassLoader;

class SitesManager implements EnvironmentIncludeInterface
{
    const SITE_DEFAULT = "_default";
    const SITE_SHARED = "all";

    use EnvironmentIncludeTrait;

    /** @var  ClassLoader */
    protected $loader;

    protected $rootDir;

    protected $currentSite;

    /** @var Site[] */
    protected $sites = [];


    public function __construct(ClassLoader $loader, Environment $environment)
    {
        if (null !== $loader) {
            $this->setLoader($loader);
        }
        if (null !== $environment) {
            $this->setEnvironment($environment);
        }
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
    public function setLoader(ClassLoader $loader)
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

    public function filterSiteName($name)
    {
        if (strtok($name, " /\\ ? * :") !== $name) {
            throw new InvalidSiteNameException(sprintf('Invalid site name %s', $name));
        }
        $name = strtolower(str_replace(["."], "_", trim($name)));
        if (substr($name, 0, 1) === "_") {
            $name = "_" . ucfirst(substr($name, 1));
        } else {
            $name = ucfirst($name);
        }
        return $name;
    }

    public function getSite($name)
    {
        $name = $this->filterSiteName($name);
        if (!array_key_exists($name, $this->sites)) {
            $siteClass = "Sites\\" . $name . "\\Site";
            if (!class_exists($siteClass)) {
                $this->sites[$name] = false;
            } else {
                /** @var Site $site */
                $site = new $siteClass($this->getLoader());
                $site->setRootDir($this->getRootDir());
                $this->sites[$name]= $site;
            }
        }
        return (false !== $this->sites[$name]) ? $this->sites[$name] : null;
    }

    public function getCurrentSite()
    {
        if (null === $this->currentSite) {
            $siteName = $this->getEnvironment()->getServerName();
            $site = $this->getSite($siteName);
            if (!$site) {
                $site = $this->getSite(self::SITE_DEFAULT);
                if (!$site) {
                    throw new NoAnySiteException($siteName);
                }
            }
            $this->currentSite = $site->getName();
        }
        return $this->getSite($this->currentSite);
    }

    public function getSharedSite()
    {
        $site = $this->getSite(self::SITE_SHARED);
        if (!$site) {
            throw new NoSharedSiteException();
        }
    }
}
