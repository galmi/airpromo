<?php

namespace Galmi\AirwaysBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NokAirRoutesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('galmi_airways:nok_air_routes')
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

        $airportsJson = $this->getContainer()->get('kernel')->locateResource('@GalmiAirwaysBundle/Resources/import/NokAirAirports.json');
        $airportsNokAir = json_decode(file_get_contents($airportsJson), true);

        $routesJson = $this->getContainer()->get('kernel')->locateResource('@GalmiAirwaysBundle/Resources/import/NokAirRoutes.json');
        $routesNokAir = json_decode(file_get_contents($routesJson), true);
        $source = 'nokair';
        foreach ($airportsNokAir as $airport) {
            //обработаем только Таиланд
            if ($airport['country'] == 'THAILAND') {
                $destinations = $routesNokAir[$airport['code']];
                foreach ($destinations as $dest) {
                    if ($dest['country'] == 'THAILAND') {
                        //Если уже существует аэропорт отправления, ищем есть ли такой же порт прибытия
                        $updated = false;
                        if (!isset($routes[$airport['code']])) {
                            $routes[$airport['code']] = [];
                        } else {
                            foreach ($routes[$airport['code']] as &$route) {
                                if ($route['code'] == $dest['code']) {
                                    if (!in_array($source, $route['sources'])) {
                                        $route['sources'][] = $source;
                                    }
                                    $updated = true;
                                }
                            }
                        }
                        if (!$updated) {
                            $routes[$airport['code']][] = [
                                'code' => $dest['code'],
                                'sources' => [$source]
                            ];
                        }
                    }
                }
                if (!isset($airports[$airport['code']])) {
                    $airports[$airport['code']] = [
                        'code' => $airport['code'],
                        'name' => $airport['name']
                    ];
                }
            }
        }

        $resultsJson = $this->getContainer()->get('kernel')->locateResource('@GalmiAirwaysBundle/Resources/import/');
        file_put_contents($resultsJson . 'routes.json', json_encode($routes));

        file_put_contents($resultsJson . 'airports.json', json_encode($airports));

        $output->writeln('Exported');
    }
}
