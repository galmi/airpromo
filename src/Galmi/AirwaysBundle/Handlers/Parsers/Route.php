<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 21.07.15
 * Time: 10:27
 */

namespace Galmi\AirwaysBundle\Handlers\Parsers;


class Route
{
    /** @var string */
    private $origin;
    /** @var string */
    private $destination;
    /** @var string */
    private $airway;

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
    }

    /**
     * @return string
     */
    public function getAirway()
    {
        return $this->airway;
    }

    /**
     * @param string $airway
     */
    public function setAirway($airway)
    {
        $this->airway = $airway;
    }
}