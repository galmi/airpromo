<?php

namespace Galmi\AirwaysBundle\Controller;

use Galmi\AirwaysBundle\Handlers\Parsers\Params;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAction(Request $request)
    {
        if (!$request->get('callback', false)) {
            throw new BadRequestHttpException();
        }
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
        return $this->render('GalmiAirwaysBundle:Default:search.html.twig', array(
            'callback' => $request->get('callback'),
            'result' => $results
        ));
    }
}
