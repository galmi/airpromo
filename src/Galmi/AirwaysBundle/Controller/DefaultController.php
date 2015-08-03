<?php

namespace Galmi\AirwaysBundle\Controller;

use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
{
    /**
     * @Route("/search/origin/{origin}/destination/{destination}/departureDate/{departureDate}", name="search", requirements={
     *  "origin": "[A-Z0-9]{3}", "destination": "[A-Z0-9]{3}", "departureDate": "\d{4}-\d{2}-\d{2}"
     * })
     * @Method("GET")
     * @param Request $request
     * @return JsonResponse
     */
    public function searchAction(Request $request)
    {
        $params = new Params();
        $departDate = new \DateTime($request->get('departureDate'));
        $params
            ->setOrigin($request->get('origin'))
            ->setDestination($request->get(('destination')))
            ->setDepartDate($departDate);
        $sourceId = $request->get('sourceId', null);
        $results = $this->get('galmi_airways.searcher')->search($params, $sourceId);

        foreach ($results as &$result) {
            $result = $result->toArray();
        }
        $response = new JsonResponse($results);
        return $response;
    }
}
