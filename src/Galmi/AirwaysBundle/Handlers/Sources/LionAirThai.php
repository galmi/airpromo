<?php
/**
     * Created by PhpStorm.
     * User: ildar
     * Date: 29.07.15
     * Time: 0:26
     */

namespace Galmi\AirwaysBundle\Handlers\Sources;


use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;

class LionAirThai extends SourceAbstract
{

    /*
     * pjourney:2
     * depCity:DMK
     * arrCity:U*RT
     * dpd1:20/08/2015
     * dpd2:30/08/2015
     * sAdult:1
     * sChild:0
     * sInfant:0
     * currency:THB
     * cTabID:35
     */
    /** @var string */
    protected $uri = 'http://search.lionairthai.com/mobile/Search/SearchFlight';
    /** @var string */
    protected $sourceName = 'lionairthai';

    /**
     * @param Params $params
     * @return Result[]
     */
    public function getResults(Params $params)
    {
        $data = $this->getParamsData($params);
        $html = $this->downloader->submit($this->uri, $data);

        return $this->parseResults($html, $params);
    }

    /**
     * @param Params $params
     * @return array
     */
    private function getParamsData(Params $params)
    {
        $data = [
            'pjourney' => 2,
            'depCity' => $params->getOrigin(),
            'arrCity' => $params->getDestination(),
            'dpd1' => $params->getDepartDate()->format('d/m/Y'), //'20/08/2015',
            'dpd2' => $params->getReturnDate() ? $params->getReturnDate()->format('d/m/Y') : '', //'30/08/2015',
            'sAdult' => 1,
            'sChild' => 0,
            'sInfant' => 0,
            'currency' => 'THB',
            'cTabID' => 35,
        ];

        return $data;
    }
}
