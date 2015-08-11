<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 11.08.15
 * Time: 12:00
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;


use Symfony\Component\DomCrawler\Crawler;

class ThaiSmile extends ParserAbstract
{
    /*
     * https://booking.thaismileair.com/Flight/InternalSelect?o1=DMK&d1=CNX&dd1=2015-08-25&ADT=1&s=true&mon=true
     */
    /** @var string */
    protected $uri = 'https://booking.thaismileair.com/Flight/InternalSelect';
    /** @var string */
    protected $sourceName = 'thaismile';

    /**
     * @param Params $params
     * @return Result[]
     */
    public function getResults(Params $params)
    {
        $uri = $this->uri . '?' . $this->getParamsString($params);
        $html = $this->downloader->get($uri);
        return $this->parseResults($html, $params);
    }

    /**
     * @param string $html
     * @return Result[]
     */
    protected function parseResults($html, Params $params)
    {
        $results = [];
        $crawler = new Crawler($html);
        $table = $crawler->filter('#availabilityForm');
        $departure = $table->first();
        $departure
            ->filter('.flight-list')
            ->reduce(function (Crawler $node) use (&$results, $params) {
                $price = 0;
                $node->filter('.fare')->reduce(function (Crawler $node) use (&$price) {
                    if ($price > 0) {
                        return;
                    }
                    $amount = $node->filter('input[name=Amount]');
                    if ($amount->count()) {
                        $price = $amount->first()->attr('value');
                    }
                });

                if ($price == 0) {
                    return;
                }
                $price = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $price = number_format($price, 2, '.', '');
                $result = new Result();
                $result
                    ->setDepartureTime(trim($node->filter('.departure .time')->first()->text()))
                    ->setOrigin($params->getOrigin())
                    ->setArrivalTime(trim($node->filter('.arrival .time')->first()->text()))
                    ->setDestination($params->getDestination())
                    ->setPrice($price)
                    ->setDate($params->getDepartDate())
                    ->setSourceSubmit($this->getSourceData($params))
                    ->setSource('thaismile');

                $results[] = $result;
            });
        return $results;
    }

    /**
     * @param Params $params
     * @return array
     */
    protected function getSourceData(Params $params)
    {
        return [
            'uri' => $this->uri,
            'method' => 'GET',
            'data' => [
                'dd1' => $params->getDepartDate()->format('Y-m-d'),
                'ADT' => 1,
                'o1' => $params->getOrigin(),
                's' => 'true',
                'd1' => $params->getDestination(),
                'mon' => 'true'
            ]
        ];
    }

    /**
     * @param Params $params
     * @return string
     */
    private function getParamsString(Params $params)
    {
        $data = [
            'o1' => $params->getOrigin(),
            'd1' => $params->getDestination(),
            'dd1' => $params->getDepartDate() ? $params->getDepartDate()->format('Y-m-d') : null,
            'ADT' => 1,
            's' => 'true',
            'mon' => 'true'
        ];
        if (!empty($params->getReturnDate())) {
            $data['dd2'] = $params->getReturnDate()->format('Y-m-d');
            $data['r'] = 'true';
        }
        return http_build_query($data);
    }

}