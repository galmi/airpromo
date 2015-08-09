<?php

namespace Galmi\AirwaysBundle\Controller;

use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
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
        $response->headers->add(array(
            'Access-Control-Allow-Origin' => $request->isSecure()?'https':'http' . '://' . $this->getParameter('base_domain')
        ));
        $response->setPublic();
        $response->setMaxAge(60*60);
        $response->setSharedMaxAge(60*60);
        $response->headers->addCacheControlDirective('must-revalidate', false);

        return $response;
    }
}
