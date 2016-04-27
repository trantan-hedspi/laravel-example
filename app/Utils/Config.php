<?php
namespace App\Utils;

class Config
{
    public function __construct()
    {
    }

    public static function readConfig($config)
    {
        $parts = explode(".", $config);
        $file_name = array_shift($parts);
        $file_contents = Config::readYamlFile($file_name);

        $result = $file_contents;
        foreach ($parts as $part) {
            $result = $result[$part];
        }

        return $result;
    }

    public static function readYamlFile($filename)
    {
        $parserUtils = new ParserUtils();
        $file_content = file_get_contents(app_path() . "/config/{$filename}.yml");

        return $parserUtils->parserYaml($file_content);
    }
}