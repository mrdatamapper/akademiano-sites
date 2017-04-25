<?php


namespace Akademiano\Sites\Site;


interface FlowDirectoryInterface extends DirectoryInterface
{
    /**
     * @return mixed
     */
    public function getInternalPath();

    /**
     * @param mixed $internalPath
     */
    public function setInternalPath($internalPath);

    /**
     * @return mixed
     */
    public function getGlobalPath();

    /**
     * @param mixed $globalPath
     */
    public function setGlobalPath($globalPath);

}
