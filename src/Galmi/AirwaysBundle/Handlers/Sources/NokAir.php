<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 27.07.15
 * Time: 12:03
 */

namespace Galmi\AirwaysBundle\Handlers\Sources;

use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;
use Symfony\Component\DomCrawler\Crawler;

class NokAir extends SourceAbstract
{

    /*
     * formData
     * 
     * {
     *  "RequestType":"NewFlight",
     *  "SegmentSellKey":"",
     *  "Criteria":{
     *      "From":"DMK",
     *      "To":"URT",
     *      "RoundTrip":"1",
     *      "Adult":1,
     *      "Child":0,
     *      "Infant":null,
     *      "Departure":"2015/08/19",
     *      "Arrival":"2015/08/19",
     *      "ProductClass":"",
     *      "FareClass":"",
     *      "BookingNo":"",
     *      "IsBulk":0,
     *      "PromotionCode":""
     *  },
     *  "Currency":"THB"
     * }
     */

    /** @var string */
    protected $uri = 'http://www.nokair.com/nokconnext/Services/AvailabilityServices.aspx?outbound=true';
    /** @var string */
    protected $sourceName = 'nokair';

    /**
     * @param Params $params
     * @return Result[]
     */
    public function getResults(Params $params)
    {
        $uri = $this->uri;
        $data = $this->getParamsData($params);
        $html = $this->downloader->submit($uri, $data);
        return $this->parseResults($html, $params);
    }

    /**
     * @param $params
     * @return string
     */
    private function getParamsData(Params $params)
    {
        $data = [
            "RequestType" => "NewFlight",
            "SegmentSellKey" => "",
            "Criteria" => [
                "From" => $params->getOrigin(),
                "To" => $params->getDestination(),
                "RoundTrip" => $params->getReturnDate() ? "1" : "0",
                "Adult" => "1",
                "Child" => "0",
                "Infant" => "0",
                "Departure" => $params->getDepartDate() ? $params->getDepartDate()->format('Y/m/d') : "",
                "Arrival" => $params->getReturnDate() ? $params->getReturnDate()->format('Y/m/d') : "",
                "ProductClass" => "",
                "FareClass" => "",
                "BookingNo" => "",
                "IsBulk" => "0",
                "PromotionCode" => ""
            ],
            "Currency" => "THB"
        ];
        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }

}