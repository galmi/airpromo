<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 21.07.15
 * Time: 9:45
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;

use Symfony\Component\DomCrawler\Crawler;

class AirAsia extends ParserAbstract
{

    /*
     * Request URL:https://booking.airasia.com/Flight/InternalSelect?o1=DMK&d1=URT&dd1=2015-08-18&dd2=2015-08-18&r=true&ADT=1&CHD=0&inl=0&s=true&mon=true&cc=THB
     */
    /** @var string */
    protected $uri = 'https://booking.airasia.com/Flight/Select';

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
            'CHD' => 0,
            'inl' => 0,
            's' => 'true',
            'mon' => 'true',
            'cc' => 'THB'
        ];
        if (!empty($params->getReturnDate())) {
            $data['dd2'] = $params->getReturnDate()->format('Y-m-d');
            $data['r'] = 'true';
        }
        return http_build_query($data);
    }

    /**
     * @param $html
     * @return Result[]
     */
    protected function parseResults($html, Params $params)
    {
        $results = [];
        $crawler = new Crawler($html);
        $table_avail_tables = $crawler->filter('table.table.avail-table');
        $departure = $table_avail_tables->first();
        $departure
            ->filter('.fare-light-row, .fare-dark-row')
            ->reduce(function (Crawler $node) use (&$results, $params) {
                if ($node->filter('.avail-table-detail-table')->count() == 0)
                    return;
                $result = new Result();
                $result->setDepartureTime($node->filter('.avail-table-detail-table')->eq(0)->filter('.avail-table-detail .text-center div')->eq(0)->text());
                $result->setOrigin($params->getOrigin());
                $result->setArrivalTime($node->filter('.avail-table-detail-table')->eq(0)->filter('.avail-table-detail')->eq(1)->filter('.text-center div')->eq(0)->text());
                $result->setDestination($params->getDestination());
                $price = trim($node->filter('.LF .promo-discount-amount')->text());
                if (empty($price))
                    $price = $node->filter('.LF .avail-fare-price:not(.discount)')->text();
                $price = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $result->setPrice($price);
                $result->setDate($params->getDepartDate());

                $results[] = $result;
            });
        return $results;
    }
}