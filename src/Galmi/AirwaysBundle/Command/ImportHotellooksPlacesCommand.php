<?php

namespace Galmi\AirwaysBundle\Command;

use Galmi\AirwaysBundle\Handlers\Downloader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportHotellooksPlacesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('galmi_airways:import_hotellooks_places')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $importPath = $this->getContainer()->get('kernel')->locateResource('@GalmiAirwaysBundle/Resources/public/js/');
        $fileName = 'airports_en.json';
        if (file_exists($importPath . $fileName)) {
            $airports = json_decode(file_get_contents($importPath . $fileName), true);
        } else {
            $airports = [];
        }

        $fileName = 'airports_th.json';
        if (file_exists($importPath . $fileName)) {
            $airportsTh = json_decode(file_get_contents($importPath . $fileName), true);
        } else {
            $airportsTh = [];
        }
        foreach ($airports as $code => $airport) {
            if (isset($airport['city_id'])) {
                $airportsTh[$code]['city_id'] = $airport['city_id'];
            }
        }
        file_put_contents($importPath . $fileName, json_encode($airportsTh, JSON_UNESCAPED_UNICODE));
    }
}
