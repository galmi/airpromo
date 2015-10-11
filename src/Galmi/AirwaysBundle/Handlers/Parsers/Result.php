<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 21.07.15
 * Time: 9:47
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;


class Result
{
    /** @var  \DateTime */
    private $date;
    /** @var string */
    private $origin;
    /** @var string */
    private $destination;
    /** @var string */
    private $departureTime;
    /** @var string */
    private $arrivalTime;
    /** @var string */
    private $price;
    /**
     * @var array
     * [
     *  "url",
     *  "method",
     *  "data"
     * ]
     */
    private $sourceSubmit;
    /** @var  string */
    private $source;

    public function __toString()
    {
        return $this->getDate()->format('d.m.Y').' '.$this->getDepartureTime().' '.$this->getOrigin(
        ).' '.$this->getArrivalTime().' '.$this->getDestination().' '.$this->getPrice();
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getDepartureTime()
    {
        return $this->departureTime;
    }

    /**
     * @param string $departureTime
     * @return $this
     */
    public function setDepartureTime($departureTime)
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     * @return $this
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * @return string
     */
    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }

    /**
     * @param string $arrivalTime
     * @return $this
     */
    public function setArrivalTime($arrivalTime)
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     * @return $this
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    public function toArray()
    {
        return [
            'date' => $this->getDate()->format('Y-m-d'),
            'origin' => $this->getOrigin(),
            'destination' => $this->getDestination(),
            'departTime' => $this->getDepartureTime(),
            'arrivalTime' => $this->getArrivalTime(),
            'price' => $this->getPrice(),
            'sourceSubmit' => $this->getSourceSubmit(),
            'source' => $this->getSource(),
        ];
    }

    /**
     * @return string
     */
    public function getSourceSubmit()
    {
        return $this->sourceSubmit;
    }

    /**
     * @param string $sourceSubmit
     * @return $this
     */
    public function setSourceSubmit($sourceSubmit)
    {
        $this->sourceSubmit = $sourceSubmit;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }
}
