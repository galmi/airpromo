<?php
/**
 * Created by PhpStorm.
 * User: ildar
 * Date: 21.07.15
 * Time: 12:12
 */

namespace Galmi\AirwaysBundle\Tests\Handlers;

use Galmi\AirwaysBundle\Handlers\Downloader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DownloaderTest extends WebTestCase
{
    /**
     * @coverage Galmi\AirwaysBundle\Handlers\Downloader::get
     */
    public function testGet()
    {
        $downloader = new Downloader();
        $html = $downloader->get('http://www.google.com');
        $this->assertContains('Google', $html);
    }

    public function testPost()
    {
        $downloader = new Downloader();
        $html = $downloader->submit('http://www.posttestserver.com/post.php', array('param' => 'value'));
        $this->assertContains('Successfully dumped 1 post variables', $html);
    }
}