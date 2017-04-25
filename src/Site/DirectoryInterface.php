<?php


namespace Akademiano\Sites\Site;


use Akademiano\Utils\Object\Prototype\StringableInterface;

interface DirectoryInterface extends StringableInterface
{
    public function getPath();

    /**
     * @param $fileName
     * @return File
     */
    public function getFile($fileName);

}
