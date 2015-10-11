<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 11.08.15
 * Time: 12:00
 */

namespace Galmi\AirwaysBundle\Handlers\Sources;


use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Galmi\AirwaysBundle\Handlers\Parsers\Result;
use Galmi\AirwaysBundle\Handlers\Sources\SourceAbstract;

class ThaiSmile extends SourceAbstract
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
        $uri = $this->uri.'?'.$this->getParamsString($params);
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
            's' => 'true',
            'mon' => 'true',
        ];
        if (!empty($params->getReturnDate())) {
            $data['dd2'] = $params->getReturnDate()->format('Y-m-d');
            $data['r'] = 'true';
        }

        return http_build_query($data);
    }
}
