<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class NeonDatabaseClient
{
    private string $neonApikey;
    private string $neonProjectId;
    private string $originalDatabaseUrl;

    public function __construct(private HttpClientInterface $client)
    {
        $this->neonApikey = $_ENV['NEON_API_KEY'];
        $this->neonProjectId = $_ENV['NEON_PROJECT_ID'];
        $this->originalDatabaseUrl = $_ENV['DATABASE_URL'];
    }

    public function getBranch(string $name): null | NeonBranch
    {
        $response = $this->client->request(
            'GET',
            "https://console.neon.tech/api/v2/projects/$this->neonProjectId/branches?search=$name",
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer $this->neonApikey",
                ],
            ]
        );
        $content = $response->toArray();
        if (array_key_exists('branches', $content) && is_array($content['branches']) && count($content['branches']) > 0) {
            $matchedBranches = array_filter($content['branches'], function ($branch) use ($name) {
                return $branch['name'] === $name;
            });

            if (empty($matchedBranches)) {
                throw new \Exception('No branch found with name: ' . $name);
            }

            if (count($matchedBranches) > 1) {
                throw new \Exception('Multiple branches found with the same name');
            }

            return NeonBranch::fromArray($matchedBranches[array_key_first($matchedBranches)]);
        }

        return null;
    }

    public function restoreBranch(NeonBranch $branch, NeonBranch $sourceBranch): NeonBranch
    {
        $response = $this->client->request(
            "POST",
            "https://console.neon.tech/api/v2/projects/$this->neonProjectId/branches/$branch->id/restore",
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer $this->neonApikey",
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'source_branch_id' => $sourceBranch->id
                ], JSON_THROW_ON_ERROR)
            ]
        );

        return NeonBranch::fromArray($response->toArray()['branch']);
    }

    public function createBranch(NeonBranch $fromBranch, string $branchName): NeonBranch
    {
        $response = $this->client->request(
            "POST",
            "https://console.neon.tech/api/v2/projects/$this->neonProjectId/branches",
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer $this->neonApikey",
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'branch' => [
                        'name' => $branchName,
                        'parent_id' => $fromBranch->id,
                    ],
                    "endpoints" => [
                        [
                            "type" => "read_write"
                        ]
                    ]
                ], JSON_THROW_ON_ERROR)
            ]
        );

        return NeonBranch::fromArray($response->toArray()['branch']);
    }

    public function deleteBranch(string $branchId): void
    {
        $this->client->request(
            "DELETE",
            "https://console.neon.tech/api/v2/projects/$this->neonProjectId/branches/$branchId",
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer $this->neonApikey",
                ],
            ]
        );
    }

    public function getConnectionUri(string $branchId): string | null
    {

        $parsedDatabaseUrl = parse_url($this->originalDatabaseUrl);
        if (!is_array($parsedDatabaseUrl)) {
            throw new \Exception('parsedDatabaseUrl should be an array');
        }

        $user = array_key_exists('user', $parsedDatabaseUrl) ? $parsedDatabaseUrl['user'] : null;
        $databaseName = array_key_exists('path', $parsedDatabaseUrl) ? str_replace('/', '', $parsedDatabaseUrl['path']) : null;

        if ($user === null || $databaseName === null) {
            throw new \Exception('user or databaseName should not be null');
        }

        $response = $this->client->request(
            'GET',
            "https://console.neon.tech/api/v2/projects/$this->neonProjectId//connection_uri?branch_id=$branchId&database_name=$databaseName&role_name=$user",
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer $this->neonApikey",
                ],
            ]
        );
        $content = $response->toArray();
        if (array_key_exists('uri', $content)) {
            return $content['uri'];
        }
        return null;
    }
}

class NeonBranch
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $default,
        public bool $protected,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    /** @phpstan-ignore-next-line */
    public static function fromArray(array $branch): NeonBranch
    {
        return new NeonBranch(
            id: $branch['id'],
            name: $branch['name'],
            default: $branch['default'],
            protected: $branch['protected'],
            createdAt: $branch['created_at'],
            updatedAt: $branch['updated_at'],
        );
    }
}
