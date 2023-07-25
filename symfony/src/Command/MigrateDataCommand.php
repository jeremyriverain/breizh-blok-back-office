<?php

namespace App\Command;

use App\Entity\Boulder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vich\UploaderBundle\Storage\StorageInterface;

#[AsCommand(
    name: 'app:migrate-data',
    description: 'migrate data for centroid properties',
)]
class MigrateDataCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $boulders = $this->em->getRepository(Boulder::class)->findAll();

        foreach ($boulders as $boulder) {
            /**
             * @var Boulder $boulder
             */
            if ($boulder->getDescription()) {
                $boulder->setDescription(str_replace('<br>', "\n", strip_tags($boulder->getDescription(), '<br>')));
            }
        }

        $this->em->flush();

        $io = new SymfonyStyle($input, $output);

        $io->success('data migrated');

        return Command::SUCCESS;
    }
}
