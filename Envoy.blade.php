@servers(['local' => ['127.0.0.1'], 'server' => ['root@143.198.129.111']])

{{-- Full deploy flow --}}
@story('deploy', ['skipBackup' => false, 'skipFrontend' => false])
    @if(!$skipFrontend)
        build-frontend
        push-frontend
    @endif
    @if(!$skipBackup)
        backup-database
    @endif
    update-code
    install-dependencies
    down
    perform-migration
    optimize-cache
    @if(!$skipFrontend)
        switch-frontend
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

@task('optimize-cache', ['on' => 'server'])
    set -e
    cd /var/www/ComputerScienceResources.com
    php artisan cache:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
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
    echo "Copying compiled frontend to server (staging to new_public)..."
    # ensure destination staging directory exists on server
    ssh root@143.198.129.111 'mkdir -p /var/www/ComputerScienceResources.com/new_public'
    # copy the whole public directory (including build assets) to the staging directory
    scp -r {{ __DIR__ }}/public root@143.198.129.111:/var/www/ComputerScienceResources.com/new_public
@endtask


@task('switch-frontend', ['on' => 'server'])
    set -e
    cd /var/www/ComputerScienceResources.com
    echo "Checking maintenance mode before switching frontend..."
    # only switch if application is in maintenance mode (artisan creates storage/framework/down)
    if [ -f storage/framework/down ]; then
        echo "Maintenance mode detected — performing frontend switch"
        if [ ! -d new_public ]; then
            echo "No new_public directory found, aborting frontend switch"
            exit 1
        fi

        if [ -d public ]; then
            timestamp=$(date +%s)
            echo "Backing up current public to public_old_$timestamp"
            mv public public_old_$timestamp
        fi

        echo "Promoting new_public to public"
        mv new_public public

        # Fix permissions if needed
        chown -R www-data:www-data public || true
        echo "Frontend switch complete"
    else
        echo "Application is not in maintenance mode — skipping frontend switch"
    fi
@endtask
