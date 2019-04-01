<?php

namespace PHPixie;
require_once __DIR__."/Image/Drivers.php";
class Image
{
    protected $defaultDriver;
    protected $drivers;

    public function __construct($defaultDriver = 'gd')
    {
        $this->defaultDriver = $defaultDriver;
    }
    
    /**
     * Creates a blank image and fill it with specified color.
     *
     * @param int $width Image width
     * @param int $height Image height
     * @param int $color Image color
     * @param float $float Color opacity
     *
     * @return \PHPixie\Image\Resource Returns self
     */
    public function create($width, $height, $color = 0xffffff, $opacity = 0, $driver = null)
    {
        $driver = $this->driver($driver);
        return $driver->create($width, $height, $color, $opacity);
    }

    /**
     * Reads image from file.
     *
     * @param   string $file Image file
     *
     * @return  \PHPixie\Image\Resource Initialized Image
     */
    public function read($file,$base64=false, $driver = null)
    {
        $driver = $this->driver($driver);
        return $driver->read($file,$base64);
    }

    /**
     * Loads image data from a bytestring.
     *
     * @param   string $bytes Image data
     *
     * @return  \PHPixie\Image\Resource Initialized Image
     */
    public function load($bytes, $driver = null)
    {
        $driver = $this->driver($driver);
        return $driver->load($bytes);
    }
    
    public function driver($name = null)
    {
        if($name == null) {
            $name = $this->defaultDriver;
        }
        
        return $this->drivers()->get($name);
    }
    
    protected function drivers()
    {
        if($this->drivers === null) {
            $this->drivers = new Image\Drivers();
        }
        return $this->drivers;
    }
}