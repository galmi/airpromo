<?php
/**
     * Created by PhpStorm.
     * User: ildar
     * Date: 21.07.15
     * Time: 9:45
     */

namespace Galmi\AirwaysBundle\Handlers\Sources;

use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;

class AirAsia extends SourceAbstract
{
    /*
     * Request URL:https://booking.airasia.com/Flight/InternalSelect?o1=DMK&d1=URT&dd1=2015-08-18&dd2=2015-08-18&r=true&ADT=1&CHD=0&inl=0&s=true&mon=true&cc=THB
     */
    /** @var string */
    protected $uri = 'https://booking.airasia.com/Flight/Select';
    /** @var string */
    protected $sourceName = 'airasia';

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
            'cc' => 'THB',
        ];
        $dateTime = $params->getReturnDate();
        if (!empty($dateTime)) {
            $data['dd2'] = $params->getReturnDate()->format('Y-m-d');
            $data['r'] = 'true';
        }

        return http_build_query($data);
    }
}
