<?php

namespace App\Controller;

use App\Services\NeonDatabaseClient;
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
        '/init-db/{newDatabaseBranchName}',
        name: 'init_db',
    )]
    public function initDb(
        ParameterBagInterface $parameterBag,
        NeonDatabaseClient $neonDatabaseClient,
        string $newDatabaseBranchName,
    ): JsonResponse {
        try {
            $mainBranch = $neonDatabaseClient->getBranch('main');
            if ($mainBranch == null) {
                throw new \Exception('main branch should exist');
            }
            $baseBranch = $neonDatabaseClient->getBranch($newDatabaseBranchName) ?? $neonDatabaseClient->createBranch(fromBranch: $mainBranch, branchName: $newDatabaseBranchName);
            $connectionUri = $neonDatabaseClient->getConnectionUri($baseBranch->id);
            if ($connectionUri == null) {
                throw new \Exception('connection URI is not found');
            }
            $this->updateDatabaseConnectionUri(parameterBag: $parameterBag, connectionUri: $connectionUri);

            (new Process(['/usr/bin/symfony', 'console', 'app:remove-assets', 'privateLocalStorage', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'app:remove-assets', 'picturesStorage', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'doctrine:database:drop', '--force', '--if-exists', '--env=test'], $parameterBag->get('kernel.project_dir')))->run();
            (new Process(['/usr/bin/symfony', 'console', 'doctrine:database:create', '--env=test'], $parameterBag->get('kernel.project_dir')))->run();
            (new Process(['/usr/bin/symfony', 'console', 'doctrine:migration:migrate', '--no-interaction', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'doctrine:fixtures:load', '--no-interaction', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();

            $copyBranch = $neonDatabaseClient->getBranch($newDatabaseBranchName . "_copy") ?? $neonDatabaseClient->createBranch(fromBranch: $baseBranch, branchName: $newDatabaseBranchName . "_copy");
            $connectionUri = $neonDatabaseClient->getConnectionUri($copyBranch->id);
            if ($connectionUri == null) {
                throw new \Exception('connection URI is not found');
            }
            $this->updateDatabaseConnectionUri(parameterBag: $parameterBag, connectionUri: $connectionUri);

            return new JsonResponse([
                'success' => true,
            ]);
        } catch (ProcessFailedException $exception) {
            return new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }
    }


    #[Route(
        '/clean-db/{databaseBranchName}',
        name: 'clean_db',
    )]
    public function cleanDb(
        NeonDatabaseClient $neonDatabaseClient,
        string $databaseBranchName,
    ): JsonResponse {
        try {
            if ($databaseBranchName !== 'main') {
                $branch = $neonDatabaseClient->getBranch(name: $databaseBranchName . '_copy');
                if ($branch != null) {
                    $neonDatabaseClient->deleteBranch($branch->id);
                }

                $branch = $neonDatabaseClient->getBranch(name: $databaseBranchName);
                if ($branch != null) {
                    $neonDatabaseClient->deleteBranch($branch->id);
                }
            }

            return new JsonResponse(['success' => true]);
        } catch (ProcessFailedException $exception) {
            return new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    #[Route(
        '/load-db/{newDatabaseBranchName}',
        name: 'load_db',
    )]
    public function loadDb(
        ParameterBagInterface $parameterBag,
        NeonDatabaseClient $neonDatabaseClient,
        string $newDatabaseBranchName,
    ): JsonResponse {
        try {
            $branch = $neonDatabaseClient->getBranch($newDatabaseBranchName . '_copy');
            if ($branch == null) {
                throw new \Exception("branch $branch is not found");
            }
            $sourceBranch = $neonDatabaseClient->getBranch($newDatabaseBranchName);
            if ($sourceBranch == null) {
                throw new \Exception("source branch $sourceBranch is not found");
            }
            $neonDatabaseClient->restoreBranch(branch: $branch, sourceBranch: $sourceBranch);
            (new Process(['/usr/bin/symfony', 'console', 'app:remove-assets', 'picturesStorage', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            (new Process(['/usr/bin/symfony', 'console', 'app:copy-assets', '--from=privateLocalStorage', '--to=picturesStorage', '--env=test'], $parameterBag->get('kernel.project_dir')))->mustRun();
            return new JsonResponse(['success' => true]);
        } catch (ProcessFailedException $exception) {
            return new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    private function updateDatabaseConnectionUri(ParameterBagInterface $parameterBag, string $connectionUri): void
    {
        $testEnvLocalPath =  $parameterBag->get('kernel.project_dir') . '/.env.test.local';
        $content = file_get_contents($testEnvLocalPath);

        if (!$content) {
            throw new \Exception("content of $testEnvLocalPath is not found");
        }
        $newEnv = preg_replace('/DATABASE_URL="([^"]*)"/', 'DATABASE_URL="' . $connectionUri . '"', $content);
        file_put_contents($testEnvLocalPath, $newEnv);
    }
}
