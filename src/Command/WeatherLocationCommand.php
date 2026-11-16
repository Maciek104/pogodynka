<?php

namespace App\Command;

use App\Entity\Location;
use App\Repository\LocationRepository;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:location',
    description: 'Pobiera prognozę pogody dla podanej lokalizacji',
)]
class WeatherLocationCommand extends Command
{
    public function __construct(
        private readonly WeatherUtil $weatherUtil,
        private readonly LocationRepository $locationRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'Location ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = (int)$input->getArgument('id');

        $location = $this->locationRepository->find($id);

        if ($location === null) {
            $io->error(sprintf('Lokalizacja o ID %d nie została znaleziona.', $id));
            return Command::FAILURE;
        }

        $measurements = $this->weatherUtil->getWeatherForLocation($location);

        $io->writeln(sprintf('Location: %s', $location->getName()));
        $io->table(
            ['Date', 'Temperature'],
            array_map(fn($m) => [
                $m->getDate()->format('Y-m-d'),
                $m->getCelsius(),
            ], $measurements)
        );

        return Command::SUCCESS;
    }
}
