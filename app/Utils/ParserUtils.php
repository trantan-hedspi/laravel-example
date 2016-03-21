<?php
namespace App\Utils;

use Symfony\Component\Yaml\Parser;

class ParserUtils
{
    protected $parserYml;

    public function __construct()
    {
        $this->parserYml = new Parser();
    }

    public function parserYaml($ymlContent)
    {
        $contents = $this->parserYml->parse($ymlContent);

        return $contents;
    }
}