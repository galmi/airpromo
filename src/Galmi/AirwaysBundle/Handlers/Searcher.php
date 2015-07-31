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
use Galmi\AirwaysBundle\Handlers\Parsers\Result;

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
     * @return Result[]
     */
    public function search(Params $params)
    {
        $results = [];
        foreach ($this->getSources() as $source)
        {
            $result = $source->getResults($params);
            $results = array_merge($results, $result);
        }
        return $results;
    }
}