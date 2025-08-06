<?php

namespace App\Command;

use App\Services\GoogleCloudStorage;
use Google\Cloud\Storage\Bucket;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\When;

#[AsCommand(
    name: 'app:remove-bucket',
    description: 'Supprime un bucket dans Cloud Storage',
)]
#[When(env: 'dev')]
#[When(env: 'test')]
class RemoveBucketCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('bucketName', InputArgument::REQUIRED, 'Nom du bucket');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $client = new GoogleCloudStorage();
            /** @var string $bucketName */
            $bucketName = $input->getArgument('bucketName');
            $buckets = $client->buckets();

            $bucket = $this->findBucketByName($buckets, $bucketName);
            if ($bucket) {
                foreach ($bucket->objects() as $object) {
                    $object->delete();
                }
                $bucket->delete();
                $io->success("Le bucket $bucketName a été supprimé.");
            }

            return Command::SUCCESS;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * @param \Google\Cloud\Core\Iterator\ItemIterator&iterable<Bucket> $buckets
     */
    public function findBucketByName($buckets, string $bucketName): ?Bucket
    {
        foreach ($buckets as $bucket) {
            if ($bucket->name() === $bucketName) {
                return $bucket;
            }
        }

        return null;
    }
}
