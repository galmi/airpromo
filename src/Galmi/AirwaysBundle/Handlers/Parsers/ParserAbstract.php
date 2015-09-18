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
    /**
     * @param string $html
     * @return Result[]
     */
    abstract public function parse($html, Params $params);

    /**
     * @param Params $params
     * @return []
     */
    abstract protected function getRedirectData(Params $params);
}