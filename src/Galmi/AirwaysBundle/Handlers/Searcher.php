<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 31.07.15
 * Time: 12:32
 */

namespace Galmi\AirwaysBundle\Handlers;

use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Sources\SourceAbstract;

class Searcher
{

    private $sources = [];

    /**
     * @param SourceAbstract $source
     */
    public function addSource(SourceAbstract $source)
    {
        $this->sources[$source->getSourceName()] = $source;
    }

    /**
     * @return SourceAbstract[]
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * @param Params $params
     * @param int $sourceId
     * @return Parsers\Result[]
     */
    public function search(Params $params, $sourceId = null)
    {
        $results = [];
        $sources = $this->getSources();
        if (!is_null($sourceId) && isset($sources[$sourceId])) {
            $results = $sources[$sourceId]->getResults($params);
        } else {
            foreach ($sources as $source) {
                $result = $source->getResults($params);
                $results = array_merge($results, $result);
            }
        }

        return $results;
    }
}
