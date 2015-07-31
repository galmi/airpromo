<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 21.07.15
 * Time: 2:04
 */

namespace Galmi\AirwaysBundle\Handlers;


use Buzz\Browser;
use Buzz\Client\Curl;

class Downloader
{
    private $client = null;
    /**
     * @var Browser
     */
    private $browser;

    public function __construct()
    {
        $this->client = new Curl();
        $this->browser = new Browser($this->client);
    }

    /**
     * @param string $uri
     * @return string
     */
    public function get($uri)
    {
        $cookieTmp = tempnam('/tmp','cookie');
        $html = shell_exec("curl -c {$cookieTmp} -L \"$uri\"");

        return $html;
    }

    /**
     * @param string $uri
     * @param array $data
     * @return string
     */
    public function submit($uri, $data)
    {
//        curl -c /private/tmp/coockie123 --data "pjourney=2&depCity=DMK&arrCity=URT&dpd1=06%2F08%2F2015&dpd2=&sAdult=1&sChild=0&sInfant=0&currency=THB&cTabID=35" -L http://search.lionairthai.com/mobile/Search/SearchFlight
        $cookieTmp = tempnam('/tmp','cookie');
        if (is_array($data)) {
            $data = http_build_query($data, '', '&');
        }
        $curl = "curl -c {$cookieTmp} --data '$data' -L \"$uri\"";
        $html = shell_exec($curl);

        return $html;
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