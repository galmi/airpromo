<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 31.07.15
 * Time: 12:26
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;


use Galmi\AirwaysBundle\Handlers\Downloader;

abstract class ParserAbstract
{
    /** @var string */
    protected $uri;
    /** @var  Downloader */
    protected $downloader;
    /** @var string */
    protected $sourceName;

    /**
     * @param $downloader
     * @param null|string $uri
     */
    public function __construct($downloader, $uri = null)
    {
        $this->downloader = $downloader;
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
    abstract protected function parseResults($html, Params $params);

    /**
     * @param Params $params
     * @return array
     */
    abstract protected function getSourceData(Params $params);
}