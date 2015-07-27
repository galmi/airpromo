<?php

namespace Galmi\AirwaysBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('GalmiAirwaysBundle:Default:index.html.twig', array('name' => $name));
    }
}
