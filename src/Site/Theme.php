<?php


namespace Akademiano\Sites\Site;

class Theme extends Directory
{
    protected $name;

    public function __construct($name, $path)
    {
        $this->setName($name);
        $this->setPath($path);
    }


    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    protected function createFile($path)
    {
        return parent::createFile($path);
    }
}
