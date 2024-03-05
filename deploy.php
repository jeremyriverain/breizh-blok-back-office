<?php

namespace Deployer;

require 'recipe/symfony.php';
require 'deploy_hosts.php';

set('application', 'breizh-blok-back-office');
set('repository', 'git@github.com:jeremyriverain/breizh-blok-back-office.git');
set('shared_dirs', ['var/log', 'public/uploads', 'public/media/cache', 'public/.well-known']);
set('branch', 'main');
set('shared_files', ['.env.prod.local']);
set('bin_dir', 'bin');
set('writable_dirs', ['var', 'public/uploads', 'public/media/cache']);
set('clear_paths', ['var/cache']);
set('deploy_path', '/var/www/boulders-topo');

task('deploy:vendors', function () {
    run('cd {{release_path}} && {{bin/composer}} install --verbose --no-progress --no-interaction --classmap-authoritative');
});

task('deploy:assets:install', function () {
    run('{{bin/php}} {{bin/console}} assets:install {{console_options}} --symlink {{release_path}}/public');
})->desc('Install bundle assets');

task('reload:php-fpm', function () {
    run('sudo /usr/sbin/service php8.2-fpm restart');
});

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:clear_paths',
    'deploy:shared',
    'deploy:vendors',
    'deploy:assets:install',
    'deploy:cache:clear',
    'deploy:cache:warmup',
    'deploy:writable',
    'database:migrate',
    'deploy:build-assets',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy the project');

task('update:release_path', function () {
    set('release_path', get('release_path') . '/symfony');
});

task('clean:repo', function () {
    run("shopt -s extglob && cd {{release_path}}/../ && export GLOBIGNORE=symfony && rm -rf *");
});

task('deploy:build-assets', function () {
    cd('{{release_path}}');
    run("npm install && npm run build");
});

after('deploy:update_code', 'update:release_path');
after('deploy:update_code', 'clean:repo');

after('deploy:failed', 'deploy:unlock');

after('deploy', 'reload:php-fpm');
