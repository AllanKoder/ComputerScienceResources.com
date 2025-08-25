@servers(['local' => ['127.0.0.1'], 'server' => ['root@143.198.129.111']])

{{-- Full deploy flow --}}
@story('deploy', ['skipBackup' => false, 'skipFrontend' => false])
    @if(!$skipFrontend)
        build-frontend
    @endif
    @if(!$skipBackup)
        backup-database
    @endif
    update-code
    install-dependencies
    down
    perform-migration
    @if(!$skipFrontend)
        push-frontend
    @endif
    up
@endstory

{{-- ===== Backend Tasks ===== --}}
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


{{-- ===== Frontend Tasks ===== --}}
@task('build-frontend', ['on' => 'local'])
    set -e
    echo "Building frontend locally..."
    npm ci
    npm run build
@endtask

@task('push-frontend', ['on' => 'local'])
    set -e
    echo "Copying compiled assets to server..."
    scp -r {{ __DIR__ }}/public/build root@143.198.129.111:/var/www/ComputerScienceResources.com/public
@endtask
