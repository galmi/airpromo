<?php

namespace Galmi\AirwaysBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThaiSmileRoutesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('galmi_airways:thai_smile_routes')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importPath = $this->getContainer()->get('kernel')->locateResource('@GalmiAirwaysBundle/Resources/public/js/');
        if (file_exists($importPath . 'routes.json')) {
            $routes = json_decode(file_get_contents($importPath . 'routes.json'), true);
        } else {
            $routes = [];
        }
        $routesJson = $this->getContainer()->get('kernel')->locateResource('@GalmiAirwaysBundle/Resources/import/ThaiSmileRoutes.json');
        $routesThaiSmile = json_decode(file_get_contents($routesJson), true);
        $source = 'thaismile';

        foreach ($routesThaiSmile as $row) {
            $code = $row['code'];
            $destinations = $row['markets'];
            foreach ($destinations as $destCode) {
                $updated = false;
                if (!isset($routes[$code])) {
                    $routes[$code] = [];
                }
                foreach ($routes[$code] as &$route) {
                    if ($route['code'] == $destCode) {
                        if (!in_array($source, $route['sources'])) {
                            $route['sources'][] = $source;
                        }
                        $updated = true;
                    }
                }
                if (!$updated) {
                    $routes[$code][] = [
                        'code' => $destCode,
                        'sources' => [$source]
                    ];
                }
            }
        }
        file_put_contents($importPath . 'out.json', json_encode($routes, JSON_UNESCAPED_UNICODE));
    }
}
