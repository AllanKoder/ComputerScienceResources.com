@servers(['local' => ['127.0.0.1'], 'server' => [getenv('DEPLOY_USER') . '@' . getenv('DEPLOY_HOST')]])

{{-- Add Caching, for production app --}}
@story('deploy', ['skipBackup' => false])
    prepare-frontend-locally
    @if(!$skipBackup)
        backup-database
    @endif
    update-code
    install-dependencies
    down
    perform-migration
    deploy-frontend
    up
@endstory

@task('down', ['on' => 'server'])
    set -e
    cd /var/www/ComputerScienceResources.com
    php artisan down
@endtask

@task('up', ['on' => 'server'])
    set -e
    cd /var/www/ComputerScienceResources.com
    php artisan up
@endtask

@task('backup-database', ['on' => 'server'])
    set -e
    cd /var/www/ComputerScienceResources.com
    php artisan backup:run --only-db
@endtask

@task('update-code', ['on' => 'server'])
    set -e
    cd /var/www/ComputerScienceResources.com
    git pull origin master
@endtask

@task('install-dependencies', ['on' => 'server'])
    set -e
    cd /var/www/ComputerScienceResources.com
    composer install --no-dev --optimize-autoloader
@endtask

@task('perform-migration', ['on' => 'server'])
    set -e
    cd /var/www/ComputerScienceResources.com
    php artisan migrate --force
@endtask

@task('prepare-frontend-locally', ['on' => 'local'])
    set -e
    echo "Building frontend locally..."
    npm ci
    npm run build
    echo "Frontend build complete. Ready to deploy."
@endtask

@task('deploy-frontend', ['on' => 'local'])
    set -e
    echo "Copying compiled assets to server..."
    scp -r {{ __DIR__ }}/public/build root@143.198.129.111:/var/www/ComputerScienceResources.com/public
@endtask
