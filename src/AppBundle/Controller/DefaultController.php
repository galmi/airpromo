<?php

namespace AppBundle\Controller;

use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/app/example", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/search/origin/{origin}/destination/{destination}/departureDate/{departureDate}", name="search", requirements={
     *  "origin": "[A-Z]{3}", "destination": "[A-Z]{3}", "departureDate": "\d{4}-\d{2}-\d{2}"
     * })
     * @Method("GET")
     * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $params = new Params();
        $departDate = new \DateTime($request->get('departureDate'));
        $params
            ->setOrigin($request->get('origin'))
            ->setDestination($request->get(('destination')))
            ->setDepartDate($departDate);

        $results = $this->get('galmi_airways.searcher')->search($params);

        foreach ($results as &$result) {
            $result = $result->toArray();
        }
        $response = new JsonResponse($results);
        return $response;
    }
}
