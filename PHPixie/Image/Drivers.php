<?php

namespace PHPixie\Image;

class Drivers
{
    protected $drivers = array();
    
    public function get($name)
    {
        if(!array_key_exists($name, $this->drivers)) {
            $method = 'build'.ucfirst($name);
            $this->drivers[$name] = $this->$method();
        }
        
        return $this->drivers[$name];
    }
    
    public function buildGd()
    {
        require __DIR__."/Drivers/Type/GD.php";
        return new Drivers\Type\GD();
    }
    
    public function buildGmagick()
    {
        require __DIR__."/Drivers/Type/Gmagick.php";
        return new Drivers\Type\Gmagick();
    }
    
    public function buildImagick()
    {
        require __DIR__."/Drivers/Type/Imagick.php";
        return new Drivers\Type\Imagick();
    }
}