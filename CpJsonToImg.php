<?php

/**
 * Convert Adobe Captivate bundeld JSON images back to their source files
 * 
 * @author David Wolf <david@wolf.gdn>
 */
class CpJsonToImg
{
    public $readPath;
    public $writePath;
    public $mapPath;
    public $map;
    public $sources;
    
    /**
     * @param string $readPath
     * @param string $writePath
     */
    public function __construct(string $readPath, string $writePath)
    {
        $this->readPath = $this->fileCheck($readPath);
        $this->writePath = $this->fileCheck($writePath);

        $mapPath = $this->readPath . DIRECTORY_SEPARATOR . 'imgmd.json';
        $this->mapPath =$this->fileCheck($mapPath);

        // $this->map = $this->loadMap();
        $this->sources = $this->loadSources();
    }

    /**
     * 
     */
    public function convert()
    {
        foreach ($this->sources as $filename => $base64) {
            $this->writeImg($filename, $base64);
        }
    }

    /**
     * 
     */
    public function writeImg(string $filename, string $base64)
    {
        $binary = base64_decode($base64);
        $imgPath = $this->writePath . DIRECTORY_SEPARATOR . basename($filename);
        $written = file_put_contents($imgPath, $binary);
        return $written;
    }

    /**
     * @param string $path
     * 
     * @return string|Exception
     */
    private function fileCheck(string $path)
    {
        if (!file_exists($path)) {
            throw new Exception("$path does not exist");
        }
        if (!is_writable($path)) {
            throw new Exception("$path is not readable");
        }
        return $path;
    }

    /**
     * @param string $path
     */
    public function loadJson(string $path)
    {
        $path = $this->fileCheck($path);
        $js = file_get_contents($path);
        $js = strstr($js, '{');
        $json = rtrim($js, ';');
        // echo $map; die;
        $array = json_decode($json, true);
        if (json_last_error()) {
            throw new Exception("JSON error");
        }
        if (array_key_exists('___', $array)) {
            unset($array['___']);
        }
        return $array;
    }

    /**
     * @return object|Exception
     */
    public function loadMap()
    {
        $map = $this->loadJson($this->mapPath);
        return $map;
    }

    /**
     * @param string $path
     * 
     * @return array|Exception
     */
    public function loadSources()
    {
        $sources = [];
        $sourcPaths = glob($this->readPath . DIRECTORY_SEPARATOR  . 'img*.json');
        array_pop($sourcPaths); // remove map
        foreach ($sourcPaths as $key => $path) {
            $sources += $this->loadJson($path);
        }
        return $sources;
    }
}
