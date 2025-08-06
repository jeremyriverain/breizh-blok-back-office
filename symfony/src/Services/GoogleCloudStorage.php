<?php

namespace App\Services;

use Google\Cloud\Storage\StorageClient;

class GoogleCloudStorage extends StorageClient
{
    public function __construct()
    {
        if (array_key_exists('GOOGLE_APPLICATION_CREDENTIALS', $_ENV)) {
            putenv('GOOGLE_APPLICATION_CREDENTIALS='.$_ENV['GOOGLE_APPLICATION_CREDENTIALS']);
        }

        parent::__construct([
            'projectId' => $_ENV['GCLOUD_PROJECT_ID'],
        ]);
    }
}
