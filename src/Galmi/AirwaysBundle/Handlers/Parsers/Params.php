<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 21.07.15
 * Time: 2:19
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;


class Params
{
    /** @var string */
    private $origin = null;
    /** @var string */
    private $destination = null;
    /** @var \DateTime */
    private $departDate = null;
    /** @var \DateTime */
    private $returnDate = null;

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
     * @return \DateTime
     */
    public function getDepartDate()
    {
        return $this->departDate;
    }

    /**
     * @param \DateTime $departDate
     */
    public function setDepartDate($departDate)
    {
        $this->departDate = $departDate;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReturnDate()
    {
        return $this->returnDate;
    }

    /**
     * @param \DateTime $returnDate
     */
    public function setReturnDate($returnDate)
    {
        $this->returnDate = $returnDate;
        return $this;
    }
}