<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 31.07.15
 * Time: 12:32
 */

namespace Galmi\AirwaysBundle\Handlers;


use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\ParserAbstract;

class Searcher
{

    private $sources = [];

    /**
     * @param ParserAbstract $parser
     */
    public function addSource(ParserAbstract $parser)
    {
        $this->sources[] = $parser;
    }

    /**
     * @return ParserAbstract[]
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * @param Params $params
     */
    public function search(Params $params)
    {

    }
}