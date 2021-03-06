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
    /**
     * @param string $html
     * @param Params $params
     * @return Result[]
     */
    public function parse($html, Params $params)
    {
        $results = [];
        $crawler = new Crawler($html);
        $table_avail_tables = $crawler->filter('table.table.avail-table');
        $departure = $table_avail_tables->first();
        $departure
            ->filter('.fare-light-row, .fare-dark-row')
            ->reduce(
                function(Crawler $node) use (&$results, $params) {
                    if ($node->filter('.avail-table-detail-table')->count() == 0) {
                        return;
                    }
                    $result = new Result();
                    $result->setDepartureTime(
                        $node->filter('.avail-table-detail-table')->eq(0)->filter(
                            '.avail-table-detail .text-center div'
                        )->eq(0)->text()
                    );
                    $result->setOrigin($params->getOrigin());
                    $result->setArrivalTime(
                        $node->filter('.avail-table-detail-table tr')->last()->filter('.avail-table-detail')->eq(
                            1
                        )->filter('.text-center div')->eq(0)->text()
                    );
                    $result->setDestination($params->getDestination());
                    $promoNode = $node->filter('.LF .promo-discount-amount');
                    $price = null;
                    if ($promoNode->count()) {
                        $price = trim($promoNode->text());
                    }
                    if (empty($price)) {
                        $lfNode = $node->filter('.LF .avail-fare-price:not(.discount)');
                        if ($lfNode->count()) {
                            $price = $lfNode->text();
                        } else {
                            $pfNode = $node->filter('.PF .avail-fare-price:not(.discount)');
                            if ($pfNode->count()) {
                                $price = $pfNode->text();
                            }
                        }
                    }
                    if (empty($price)) {
                        return;
                    }
                    $price = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $result->setPrice($price);
                    $result->setDate($params->getDepartDate());
                    $result->setSourceSubmit($this->getRedirectData($params));
                    $result->setSource('airasia');

                    $results[] = $result;
                }
            );

        return $results;
    }

    /**
     * @param Params $params
     * @return string
     */
    protected function getRedirectData(Params $params)
    {
        /**
         * http://booking.airasia.com/Flight/InternalSelect?dd1=2015-08-23&culture=en-GB&ADT=1&inl=0&o1=DMK&CHD=0&r=false&s=true&d1=URT&mon=true&marker=kxe20w15g
         */
        return [
            'uri' => 'http://booking.airasia.com/Flight/InternalSelect',
            'method' => 'GET',
            'data' => [
                'dd1' => $params->getDepartDate()->format('Y-m-d'),
                'culture' => 'en-GB',
                'ADT' => 1,
                'inl' => 0,
                'o1' => $params->getOrigin(),
                'CHD' => 0,
                'r' => 'false',
                's' => 'true',
                'd1' => $params->getDestination(),
                'mon' => 'true',
            ],
        ];
    }
}
