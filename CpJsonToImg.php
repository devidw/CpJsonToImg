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
     * convert all sources in bulk
     */
    public function convert()
    {
        foreach ($this->sources as $filename => $base64) {
            $this->writeImg($filename, $base64);
        }
    }

    /**
     * convert a single image and write its contents to the file system
     * @param string $filename
     * @param string $base64
     * @return
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
     * @return string
     */
    private function fileCheck(string $path): string
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
     * @return array
     */
    public function loadJson(string $path): array
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
     * @return array
     */
    public function loadMap(): array
    {
        $map = $this->loadJson($this->mapPath);
        return $map;
    }

    /**
     * @param string $path
     * @return array
     */
    public function loadSources(): array
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
