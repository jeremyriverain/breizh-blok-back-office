<?php

namespace App\Controller;

use Google\Cloud\Storage\StorageClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;


#[Route(
    '/test',
    condition: "'%kernel.environment%' in ['dev', 'test']"
)]
class TestController extends AbstractController
{
    #[Route(
        '/init-db',
        name: 'init_db',
    )]
    public function initDb(ParameterBagInterface $parameterBag): JsonResponse
    {
        try {
            (new Process(['/usr/bin/symfony', 'console', 'doctrine:database:drop', '--force', '--if-exists', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'doctrine:database:create', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'doctrine:migration:migrate', '--no-interaction', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['rm', '-rf', 'public/uploads'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'doctrine:fixtures:load', '--no-interaction', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            return new JsonResponse(['success' => true]);
        } catch (ProcessFailedException $exception) {
            return new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    #[Route(
        '/dump-db',
        name: 'dump_db',
    )]
    public function dumpDb(ParameterBagInterface $parameterBag): JsonResponse
    {
        try {
            (new Process(['mkdir', '-p', 'var/test/files'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['curl', 'http://db/dump'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['rm', '-rf', 'var/test/files/uploads'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['cp', '-r', 'public/uploads', 'var/test/files'], $parameterBag->get('kernel.project_dir')))->mustRun();
            return new JsonResponse(['success' => true]);
        } catch (ProcessFailedException $exception) {
            return new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    #[Route(
        '/load-db',
        name: 'load_db',
    )]
    public function loadDb(ParameterBagInterface $parameterBag): JsonResponse
    {
        try {
            (new Process(['curl', 'http://db/load'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['rm', '-rf', 'public/uploads'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['cp', '-r', 'var/test/files/uploads', 'public/uploads'], $parameterBag->get('kernel.project_dir')))->mustRun();
            return new JsonResponse(['success' => true]);
        } catch (ProcessFailedException $exception) {
            return new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    #[Route(
        '/cloud-storage',
        name: 'cloud_storage',
    )]
    public function cloudStorage(): JsonResponse
    {
        # Instantiates a client
        $storage = new StorageClient([
            'projectId' => $_ENV['GCLOUD_PROJECT_ID']
        ]);

        # The name for the new bucket
        $bucketName = Uuid::v7();

        # Creates the new bucket
        $bucket = $storage->createBucket($bucketName);

        return new JsonResponse(['success' => 'Bucket ' . $bucket->name() . ' created.']);
    }
}
