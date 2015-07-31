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

    public function __construct($downloader, $uri = null)
    {
        $this->downloader = $downloader;
        if (!empty($uri)) {
            $this->uri = $uri;
        }
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
}