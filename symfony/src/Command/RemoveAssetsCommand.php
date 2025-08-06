<?php

namespace App\Command;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\When;

#[AsCommand(
    name: 'app:remove-assets',
    description: 'supprime les ressources du projet (images)',
)]
#[When(env: 'dev')]
#[When(env: 'test')]
class RemoveAssetsCommand extends Command
{
    public function __construct(private FilesystemOperator $picturesStorage, private FilesystemOperator $privateLocalStorage)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $filesystem */
        $filesystem = $input->getArgument('filesystem');

        try {
            $this->$filesystem->deleteDirectory('/');

            $io->success("Les ressources de $filesystem ont été supprimées");

            return Command::SUCCESS;
        } catch (\Throwable $th) {
            $io->error($th);

            return Command::FAILURE;
        }
    }

    protected function configure(): void
    {
        $this
            ->addArgument(name: 'filesystem', mode: InputArgument::REQUIRED, description: 'source filesystem');
    }
}
