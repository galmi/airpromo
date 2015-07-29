<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 27.07.15
 * Time: 12:03
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;


use Galmi\AirwaysBundle\Handlers\Downloader;
use Symfony\Component\DomCrawler\Crawler;

class NokAir
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
     *      "Departure":"08/19/2015",
     *      "Arrival":"08/19/2015",
     *      "ProductClass":"",
     *      "FareClass":"",
     *      "BookingNo":"",
     *      "IsBulk":0,
     *      "PromotionCode":""
     *  },
     *  "Currency":"THB"
     * }
     */

    private $uri = 'http://www.nokair.com/nokconnext/Services/AvailabilityServices.aspx?outbound=true';
    /** @var  Downloader */
    private $downloader;

    public function __construct($downloader)
    {
        $this->downloader = $downloader;
    }

    /**
     * @param Params $params
     * @return Result[]
     */
    public function getResults(Params $params)
    {
        $uri = $this->uri;
        $data = $this->getParamsData($params);
        $html = $this->downloader->post($uri, $data);
        return $this->parseResults($html, $params);
    }

    /**
     * @param $params
     * @return string
     */
    protected function getParamsData(Params $params)
    {
        $data = [
            "RequestType" => "NewFlight",
            "SegmentSellKey" => "",
            "Criteria" => [
                "From" => $params->getOrigin(),
                "To" => $params->getDestination(),
                "RoundTrip" => "1",
                "Adult" => 1,
                "Child" => 0,
                "Infant" => null,
                "Departure" => $params->getDepartDate() ? $params->getDepartDate()->format('m/d/Y') : "",
                "Arrival" => $params->getReturnDate() ? $params->getReturnDate()->format('m/d/Y') : "",
                "ProductClass" => "",
                "FareClass" => "",
                "BookingNo" => "",
                "IsBulk" => 0,
                "PromotionCode" => ""
            ],
            "Currency" => "THB"
        ];
        return json_encode($data);
    }

    protected function parseResults($html, Params $params)
    {
        $results = [];
        $crawler = new Crawler($html);
        $tableDeparture = $crawler->filter('#Inbound_' . $params->getDepartDate()->format('d-m-Y'));
        $tableDeparture
            ->filter('.Text_body tr')
            ->reduce(function (Crawler $node) use (&$results, $params) {
                $row0 = $node->filter('td')->eq(0)->text();
                if (preg_match("/[0-9]{2}:[0-9]{2}/", $row0)) {
                    $result = new Result();
                    $result
                        ->setOrigin($params->getOrigin())
                        ->setDestination($params->getDestination())
                        ->setDepartureTime($node->filter('td')->eq(0)->text())
                        ->setArrivalTime($node->filter('td')->eq(1)->text())
                        ->setDate($params->getDepartDate());
                    $pricePromo = filter_var($node->filter('td')->eq(6)->text(), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    if ($pricePromo) {
                        $result->setPrice($pricePromo);
                    } else {
                        $priceEco = filter_var($node->filter('td')->eq(7)->text(), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                        if ($priceEco) {
                            $result->setPrice($priceEco);
                        } else {
                            $priceFlexi = filter_var($node->filter('td')->eq(8)->text(), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                            if ($priceFlexi) {
                                $result->setPrice($priceFlexi);
                            } else {
                                return;
                            }
                        }
                    }
                    $results[] = $result;
                }
            });
        return $results;
    }
}