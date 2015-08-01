<?php

namespace Galmi\AirwaysBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LionAirThaiRoutesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('galmi_airways:lion_air_thai_routes')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importPath = $this->getContainer()->get('kernel')->locateResource('@GalmiAirwaysBundle/Resources/import/');
        if (file_exists($importPath . 'airports.json')) {
            $airports = json_decode(file_get_contents($importPath . 'airports.json'), true);
        } else {
            $airports = [];
        }
        if (file_exists($importPath . 'routes.json')) {
            $routes = json_decode(file_get_contents($importPath . 'routes.json'), true);
        } else {
            $routes = [];
        }

        $routesJson = $this->getContainer()->get('kernel')->locateResource('@GalmiAirwaysBundle/Resources/import/LionAirThaiRoutes.json');
        $routesLionAirThai = json_decode(file_get_contents($routesJson), true);
        $source = 'lionairthai';

        foreach ($routesLionAirThai as $code => $destinations) {
            //обработаем только Таиланд
            foreach ($destinations as $dest) {
                if (isset($airports[$dest['code']])) {
                    //Если уже существует аэропорт отправления, ищем есть ли такой же порт прибытия
                    $updated = false;
                    if (!isset($routes[$code])) {
                        $routes[$code] = [];
                    }
                    foreach ($routes[$code] as &$route) {
                        if ($route['code'] == $dest['code']) {
                            if (!in_array($source, $route['sources'])) {
                                $route['sources'][] = $source;
                            }
                            $updated = true;
                        }
                    }
                    if (!$updated) {
                        $routes[$code][] = [
                            'code' => $dest['code'],
                            'sources' => [$source]
                        ];
                    }
                }
            }
        }

        $resultsJson = $this->getContainer()->get('kernel')->locateResource('@GalmiAirwaysBundle/Resources/import/');
        file_put_contents($resultsJson . 'routes.json', json_encode($routes));

        file_put_contents($resultsJson . 'airports.json', json_encode($airports));

        $output->writeln('Exported');
    }
}
