<?php
/**
     * Created by PhpStorm.
     * User: ildar
     * Date: 18.09.15
     * Time: 12:06
     */

namespace Galmi\AirwaysBundle\Handlers\Sources;

use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;

interface SourceInterface
{
    /**
     * @param Params $params
     * @return Result[]
     */
    public function getResults(Params $params);
}
