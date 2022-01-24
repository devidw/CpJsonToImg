<?php

namespace Devidw\CpJsonToImg;

/**
 * Captivate JSON to Image Converter
 * Convert Adobe Captivate bundeld JSON images back to their source files
 * 
 * @version 1.0.0
 */
class CpJsonToImg
{
    /**
     * The directory to read the JSON files from
     * 
     * @var string
     */
    public $readPath;

    /**
     * The directory to write the image files to
     * 
     * @var string
     */
    public $writePath;

    /**
     * The directory of the map file
     * 
     * @var string
     */
    public $mapPath;

    /**
     * The image sources to convert
     * 
     * @var array
     */
    public $sources;

    /**
     * Constructor
     * 
     * @param string $readPath
     * @param string $writePath
     */
    public function __construct(string $readPath, string $writePath)
    {
        $this->readPath = $this->fileCheck($readPath);
        $this->writePath = $this->fileCheck($writePath);

        $mapPath = $this->readPath . DIRECTORY_SEPARATOR . 'imgmd.json';
        $this->mapPath = $this->fileCheck($mapPath);

        $this->sources = $this->loadSources();
    }

    /**
     * Check if a file exists and is readable
     * 
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
     * Load a josn file and prepare its contents for decoding
     * 
     * @param string $path
     * @return array
     */
    private function loadJson(string $path): array
    {
        $path = $this->fileCheck($path);
        $js = file_get_contents($path);
        $js = strstr($js, '{');
        $json = rtrim($js, ';');
        // echo $map; die;
        $array = json_decode($json, true);
        if (json_last_error()) {
            throw new Exception('JSON error');
        }
        if (array_key_exists('___', $array)) {
            unset($array['___']);
        }
        return $array;
    }

    /**
     * Load the bundled image JSON's and its iamge sources
     * 
     * @return array
     */
    private function loadSources(): array
    {
        $sources = [];
        $sourcPaths = glob($this->readPath . DIRECTORY_SEPARATOR  . 'img*.json');
        array_pop($sourcPaths); // remove map
        foreach ($sourcPaths as $key => $path) {
            $sources += $this->loadJson($path);
        }
        return $sources;
    }

    /**
     * Convert a single image and write its contents to the file system
     * 
     * @param string $filename
     * @param string $base64
     * @return int|false
     */
    private function writeImg(string $filename, string $base64): ?int
    {
        $binary = base64_decode($base64);
        $imgPath = $this->writePath . DIRECTORY_SEPARATOR . basename($filename);
        $written = file_put_contents($imgPath, $binary);
        return $written;
    }

    /**
     * Convert all sources in bulk
     * 
     * @return void
     */
    public function convert(): void
    {
        foreach ($this->sources as $filename => $base64) {
            $this->writeImg($filename, $base64);
        }
    }
}
