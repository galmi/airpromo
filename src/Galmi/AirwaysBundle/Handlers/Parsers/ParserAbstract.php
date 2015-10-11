<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 31.07.15
 * Time: 12:26
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;

abstract class ParserAbstract implements ParserInterface
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
