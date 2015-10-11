<?php

namespace Galmi\AirwaysBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class AirAsiaAirportsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('galmi_airways:air_asia_airports_th')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileName = 'airports.json';
        $importPath = $this->getContainer()->get('kernel')->locateResource('@GalmiAirwaysBundle/Resources/public/js/');
        if (file_exists($importPath . $fileName)) {
            $airports = json_decode(file_get_contents($importPath . $fileName), true);
        } else {
            $airports = [];
        }
        $importPath = $this->getContainer()->get('kernel')->locateResource('@GalmiAirwaysBundle/Resources/import/');
        if (file_exists($importPath . 'out.yml')) {
            $result = Yaml::parse(file_get_contents($importPath . 'out.yml'));
        } else {
            $result = ['airports' => []];
        }
        foreach ($airports as $code => &$airport) {
            $airport = [
                'code' => $airport['code'],
                'name' => $result['airports'][$airport['code']]
            ];
        }
        file_put_contents($importPath . 'out.json', json_encode($airports, JSON_UNESCAPED_UNICODE));
    }
}
