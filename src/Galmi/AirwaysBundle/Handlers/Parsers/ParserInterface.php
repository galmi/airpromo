<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 18.09.15
 * Time: 12:06
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;

interface ParserInterface
{
    /**
     * @param string $html
     * @param Params $params
     * @return Result[]
     */
    public function parse($html, Params $params);
}
