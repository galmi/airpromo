<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 21.07.15
 * Time: 2:04
 */

namespace Galmi\AirwaysBundle\Handlers;


use Buzz\Browser;
use Symfony\Component\HttpFoundation\Request;

class Downloader
{
    private $client = null;
    /**
     * @var Browser
     */
    private $browser;

    public function __construct()
    {
        $this->browser = new Browser($this->client);
    }

    /**
     * @param string $uri
     * @return string
     */
    public function get($uri)
    {
        $response = $this->browser->get($uri);

        return $response->getContent();
    }

    /**
     * @param string $uri
     * @param array $data
     * @return string
     */
    public function submit($uri, array $data)
    {
        $response = $this->browser->submit($uri, $data);

        return $response->getContent();
    }

    /**
     * @param string $uri
     * @param string $data
     * @return string
     */
    public function post($uri, $data)
    {
        $response = $this->browser->post($uri, array(), $data);

        return $response->getContent();
    }
}