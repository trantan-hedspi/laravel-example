<?php

namespace App\Utils;

class Config
{
    protected $parserUtils;

    public function __construct()
    {
        $this->parserUtils = new ParserUtils();
    }

    public function readConfig($config)
    {
        $parts = explode(".", $config);
        $file_name = $parts[0];
        $file_contents = $this->readYamlFile($file_name);
        unset($parts[0]);

        $result = $file_contents;
        foreach ($parts as $part) {
            $result = $result[$part];
        }

        return $result;
    }

    public function readYamlFile($filename)
    {
        $file_content = file_get_contents(app_path() . "/config/{$filename}.yml");

        return $this->parserUtils->parserYaml($file_content);
    }
}