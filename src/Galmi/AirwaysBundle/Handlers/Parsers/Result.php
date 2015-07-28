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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;
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
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
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
     */
    public function setDepartureTime($departureTime)
    {
        $this->departureTime = $departureTime;
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
     */
    public function setArrivalTime($arrivalTime)
    {
        $this->arrivalTime = $arrivalTime;
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
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    public function __toString()
    {
        return $this->getDepartureTime() . ' ' . $this->getOrigin() . ' ' . $this->getArrivalTime() . ' ' . $this->getDestination() . ' ' . $this->getPrice();
    }
}