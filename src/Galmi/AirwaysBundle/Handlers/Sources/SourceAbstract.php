<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 31.07.15
 * Time: 12:26
 */

namespace Galmi\AirwaysBundle\Handlers\Sources;


use Galmi\AirwaysBundle\Handlers\Downloader;
use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\ParserAbstract;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;

abstract class SourceAbstract implements SourceInterface
{
    /** @var string */
    protected $uri;
    /** @var  Downloader */
    protected $downloader;
    /** @var ParserAbstract $parser */
    protected $parser;
    /** @var string */
    protected $sourceName;

    /**
     * @param $downloader
     * @param null|string $uri
     */
    public function __construct($downloader, ParserAbstract $parser, $uri = null)
    {
        $this->downloader = $downloader;
        $this->parser = $parser;
        if (!empty($uri)) {
            $this->uri = $uri;
        }
    }

    /**
     * @return string
     */
    public function getSourceName()
    {
        return $this->sourceName;
    }

    /**
     * @param Params $params
     * @return Result[]
     */
    abstract public function getResults(Params $params);

    /**
     * @param string $html
     * @return Result[]
     */
    protected function parseResults($html, Params $params)
    {
        return $this->parser->parse($html, $params);
    }
}
