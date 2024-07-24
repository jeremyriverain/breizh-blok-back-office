<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Attribute\Route;

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
            (new Process(['/usr/bin/symfony', 'console', 'app:remove-assets', 'privateLocalStorage', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'app:remove-assets', 'picturesStorage', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'doctrine:database:drop', '--force', '--if-exists', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'doctrine:database:create', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'doctrine:migration:migrate', '--no-interaction', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
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
            (new Process(['curl', 'http://db/dump'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'app:copy-assets', '--from=picturesStorage', '--to=privateLocalStorage', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
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
            (new Process(['/usr/bin/symfony', 'console', 'app:remove-assets', 'picturesStorage', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'app:copy-assets', '--from=privateLocalStorage', '--to=picturesStorage', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            return new JsonResponse(['success' => true]);
        } catch (ProcessFailedException $exception) {
            return new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }
    }
}
