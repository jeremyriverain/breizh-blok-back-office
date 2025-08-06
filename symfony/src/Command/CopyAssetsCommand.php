<?php

namespace App\Command;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\When;

#[AsCommand(
    name: 'app:copy-assets',
    description: 'Télécharge dans un répertoire local les ressources présentes dans le bucket Cloud Storage',
)]
#[When(env: 'dev')]
#[When(env: 'test')]
class CopyAssetsCommand extends Command
{
    public function __construct(private FilesystemOperator $picturesStorage, private FilesystemOperator $privateLocalStorage)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $source */
        $source = $input->getOption('from');

        /** @var string $destination */
        $destination = $input->getOption('to');

        try {
            $listing = $this->$source->listContents('/', true);

            /** @var \League\Flysystem\StorageAttributes $item */
            foreach ($listing as $item) {
                if ($item instanceof \League\Flysystem\FileAttributes) {
                    $this->$destination->write($item->path(), $this->$source->read($item->path()));
                }
            }

            $io->success("Les ressources de $source ont été copiées vers $destination.");

            return Command::SUCCESS;
        } catch (\Throwable $th) {
            $io->error($th);

            return Command::FAILURE;
        }
    }

    protected function configure(): void
    {
        $this
            ->addOption(name: 'from', shortcut: null, mode: InputOption::VALUE_REQUIRED, description: 'source filesystem', suggestedValues: ['privateLocalStorage', 'picturesStorage'])
            ->addOption(name: 'to', shortcut: null, mode: InputOption::VALUE_REQUIRED, description: 'destination filesystem', suggestedValues: ['privateLocalStorage', 'picturesStorage']);
    }
}
