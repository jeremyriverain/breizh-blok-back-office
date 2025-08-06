<?php

namespace App\Command;

use App\Services\GoogleCloudStorage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\When;

#[AsCommand(
    name: 'app:create-bucket',
    description: 'Créé un bucket dans Cloud Storage',
)]
#[When(env: 'dev')]
#[When(env: 'test')]
class CreateBucketCommand extends Command
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

            $bucket = $client->createBucket($bucketName, [
                'location' => 'EUROPE-WEST9',
                'iamConfiguration' => [
                    'uniformBucketLevelAccess' => [
                        'enabled' => true,
                    ],
                ],
                'softDeletePolicy' => [
                    'retentionDurationSeconds' => 0,
                ],
            ]);

            $policy = $bucket->iam()->policy();

            $policy['bindings'][] = [
                'role' => 'roles/storage.objectViewer',
                'members' => ['allUsers'],
            ];

            // Update the bucket's IAM policy
            $bucket->iam()->setPolicy($policy);

            $io->success("Le bucket $bucketName a été créé.");

            return Command::SUCCESS;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
