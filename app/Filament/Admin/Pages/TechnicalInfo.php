<?php

namespace Modules\SystemInfo\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class TechnicalInfo extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 99;

    protected static ?string $slug = 'technical-info';

    public static function getNavigationGroup(): ?string
    {
        return __('System Admin');
    }

    public static function getNavigationLabel(): string
    {
        return __('Technical Info');
    }

    public function getTitle(): string|Htmlable
    {
        return __('Technical Info');
    }

    protected string $view = 'systeminfo::filament.admin.pages.technical-info';

    public function getViewData(): array
    {
        return [
            'phpInfo' => $this->getPhpInfo(),
            'octaneInfo' => $this->getOctaneInfo(),
            'octaneDiagnostics' => $this->getOctaneDiagnostics(),
            'opcacheInfo' => $this->getOpcacheInfo(),
            'phpIniRecommendations' => $this->getPhpIniRecommendations(),
            'performanceRecommendations' => $this->getPerformanceRecommendations(),
            'serverInfo' => $this->getServerInfo(),
            'emailSettings' => $this->getEmailSettings(),
            'laravelInfo' => $this->getLaravelInfo(),
            'databaseInfo' => $this->getDatabaseInfo(),
            'cacheInfo' => $this->getCacheInfo(),
        ];
    }

    protected function getPhpInfo(): array
    {

        $pcreJit = ini_get('pcre.jit') ? '✅ ' . __('Active') : '❌ ' . __('Disabled');
        if (defined('PCRE_JIT_SUPPORT') && !PCRE_JIT_SUPPORT) {
            $pcreJit = '❌ ' . __('Not Supported');
        }

        return [
            'version' => PHP_VERSION,
            'sapi' => php_sapi_name(),
            'os' => PHP_OS,
            'max_execution_time' => ini_get('max_execution_time') . 's',
            'memory_limit' => ini_get('memory_limit'),
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'date_timezone' => ini_get('date.timezone'),
            'pcre_jit' => $pcreJit,
            'extensions' => get_loaded_extensions(),
        ];
    }

    protected function getOctaneInfo(): array
    {
        $isInstalled = class_exists(\Laravel\Octane\Octane::class);
        $isRunning = false;
        $server = null;

        if ($isInstalled) {

            $isRunning = isset($_SERVER['LARAVEL_OCTANE']) || app()->bound('octane');
            $server = config('octane.server', 'unknown');
        }

        return [
            'installed' => $isInstalled,
            'running' => $isRunning,
            'server' => $server,
            'status' => $isRunning ? '✅ ' . __('Active') : ($isInstalled ? '⚠️ ' . __('Installed but not running') : '❌ ' . __('Not installed')),
        ];
    }

    protected function getOctaneDiagnostics(): array
    {
        $diagnostics = [
            'installed' => false,
            'running' => false,
            'status' => 'not_installed',
            'status_message' => '❌ ' . __('Not installed'),
            'server_type' => null,
            'config' => [],
            'env' => [],
            'checks' => [],
            'problems' => [],
            'recommendations' => [],
        ];

        $diagnostics['installed'] = class_exists(\Laravel\Octane\Octane::class);

        if (!$diagnostics['installed']) {
            $diagnostics['recommendations'][] = 'composer require laravel/octane';
            $diagnostics['recommendations'][] = 'php artisan octane:install';

            return $diagnostics;
        }

        $diagnostics['running'] = isset($_SERVER['LARAVEL_OCTANE']) || app()->bound('octane');

        $diagnostics['server_type'] = config('octane.server', 'frankenphp');
        $diagnostics['config'] = [
            'server' => config('octane.server', 'frankenphp'),
            'https' => config('octane.https', false) ? 'Yes' : 'No',
            'max_execution_time' => config('octane.max_execution_time', 30) . 's',
            'garbage_threshold' => config('octane.garbage', 50) . ' MB',
        ];

        $actualPort = $_SERVER['SERVER_PORT'] ?? ($_SERVER['OCTANE_PORT'] ?? null);
        $actualServer = config('octane.server', 'swoole');

        $diagnostics['env'] = [
            'OCTANE_SERVER' => $actualServer,
            'OCTANE_PORT' => $actualPort ?: __('(unknown)'),
            'OCTANE_HTTPS' => config('octane.https', false) ? 'true' : 'false',
            'OCTANE_WORKERS' => $_SERVER['OCTANE_WORKERS'] ?? 'auto',
            'OCTANE_MAX_REQUESTS' => $_SERVER['OCTANE_MAX_REQUESTS'] ?? '500',
        ];

        $checks = [];

        $serverType = $diagnostics['server_type'];
        if ($serverType === 'frankenphp') {
            $frankenphpExists = $this->commandExists('frankenphp');
            $checks['frankenphp_binary'] = [
                'name' => __('FrankenPHP Binary'),
                'status' => $frankenphpExists ? 'ok' : 'error',
                'message' => $frankenphpExists ? __('System exists') : __('Not Found'),
            ];
            if (!$frankenphpExists) {
                $diagnostics['problems'][] = __('FrankenPHP binary not found');
                $diagnostics['recommendations'][] = __('Download FrankenPHP: :url', ['url' => 'https://frankenphp.dev']);
            }
        }
        elseif ($serverType === 'swoole') {
            $swooleLoaded = extension_loaded('swoole');
            $checks['swoole_extension'] = [
                'name' => __('Swoole Extension'),
                'status' => $swooleLoaded ? 'ok' : 'error',
                'message' => $swooleLoaded ? __('Installed') . ' (' . (phpversion('swoole') ?: 'version unknown') . ')' : __('Not installed'),
            ];
            if (!$swooleLoaded) {
                $diagnostics['problems'][] = __('Swoole PHP extension not installed');
                $diagnostics['recommendations'][] = 'pecl install swoole';
            }
        }
        elseif ($serverType === 'roadrunner') {
            $rrExists = $this->commandExists('rr');
            $checks['roadrunner_binary'] = [
                'name' => __('RoadRunner Binary'),
                'status' => $rrExists ? 'ok' : 'error',
                'message' => $rrExists ? __('System exists') : __('Not Found'),
            ];
            if (!$rrExists) {
                $diagnostics['problems'][] = 'RoadRunner binary not found';
                $diagnostics['recommendations'][] = 'vendor/bin/rr get-binary';
            }
        }

        $configExists = file_exists(config_path('octane.php'));
        $checks['config_file'] = [
            'name' => __('Config File'),
            'status' => $configExists ? 'ok' : 'warning',
            'message' => $configExists ? __('config/octane.php exists') : __('Not found, using defaults'),
        ];

        $checks['running_status'] = [
            'name' => __('Running Status'),
            'status' => $diagnostics['running'] ? 'ok' : 'warning',
            'message' => $diagnostics['running'] ? __('Octane is running') : __('Running with standard PHP'),
        ];

        $checks['env_marker'] = [
            'name' => __('LARAVEL_OCTANE Env'),
            'status' => isset($_SERVER['LARAVEL_OCTANE']) ? 'ok' : 'info',
            'message' => isset($_SERVER['LARAVEL_OCTANE']) ? __('Defined') : __('Not defined (Octane not running)'),
        ];

        $sapi = php_sapi_name();
        $isOctaneSapi = in_array($sapi, ['cli', 'cli-server', 'swoole', 'frankenphp']);
        $checks['php_sapi'] = [
            'name' => __('PHP SAPI'),
            'status' => 'info',
            'message' => $sapi,
        ];

        $memoryLimit = ini_get('memory_limit');
        $memoryBytes = $this->convertToBytes($memoryLimit);
        $memoryOk = $memoryBytes >= 256 * 1024 * 1024 || $memoryBytes === -1;
        $checks['memory_limit'] = [
            'name' => __('Memory Limit'),
            'status' => $memoryOk ? 'ok' : 'warning',
            'message' => $memoryLimit . ($memoryOk ? '' : ' (' . __('256M+ recommended for Octane') . ')'),
        ];

        $diagnostics['checks'] = $checks;

        if ($diagnostics['running']) {
            $diagnostics['status'] = 'running';
            $diagnostics['status_message'] = '✅ ' . __('Octane Active');
        }
        elseif (!empty($diagnostics['problems'])) {
            $diagnostics['status'] = 'error';
            $diagnostics['status_message'] = '❌ ' . __('Problems Found');
        }
        else {
            $diagnostics['status'] = 'not_running';
            $diagnostics['status_message'] = '⚠️ ' . __('Installed but not running');
            $diagnostics['recommendations'][] = 'php artisan octane:start --server=' . $diagnostics['server_type'];
        }

        return $diagnostics;
    }

    protected function commandExists(string $command): bool
    {
        $whereCommand = PHP_OS_FAMILY === 'Windows' ? 'where' : 'which';
        $result = @shell_exec("{$whereCommand} {$command} 2>/dev/null");

        return !empty(trim($result ?? ''));
    }

    protected function getOpcacheInfo(): array
    {
        $enabled = function_exists('opcache_get_status');
        $status = $enabled ? @opcache_get_status(false) : null;

        if (!$enabled) {
            return [
                'enabled' => false,
                'status' => '❌ ' . __('OPcache not loaded'),
            ];
        }

        if ($status === false) {
            return [
                'enabled' => false,
                'status' => '⚠️ ' . __('OPcache loaded but disabled'),
            ];
        }

        return [
            'enabled' => true,
            'status' => '✅ ' . __('Active'),
            'opcache_enabled' => $status['opcache_enabled'] ?? false,
            'cache_full' => $status['cache_full'] ?? false,
            'memory_usage' => $status['memory_usage'] ?? [],
            'statistics' => $status['opcache_statistics'] ?? [],
            'jit' => $status['jit'] ?? null,
            'validate_timestamps' => ini_get('opcache.validate_timestamps'),
            'revalidate_freq' => ini_get('opcache.revalidate_freq'),
        ];
    }

    protected function getPhpIniRecommendations(): array
    {
        $recommendations = [];

        $memoryLimitRaw = ini_get('memory_limit');
        $memoryLimit = $this->convertToBytes($memoryLimitRaw);
        if ($memoryLimit !== -1 && $memoryLimit < 256 * 1024 * 1024) {
            $recommendations[] = [
                'type' => 'warning',
                'setting' => 'memory_limit',
                'current' => $memoryLimitRaw,
                'recommended' => '256M ' . __('or more'),
                'message' => __('Memory limit is low, consider increasing for large operations.'),
            ];
        }

        $maxExecTime = (int)ini_get('max_execution_time');
        if ($maxExecTime > 0 && $maxExecTime < 30) {
            $recommendations[] = [
                'type' => 'warning',
                'setting' => 'max_execution_time',
                'current' => $maxExecTime . 's',
                'recommended' => '30s ' . __('or more'),
                'message' => __('Max execution time might be low.'),
            ];
        }

        $uploadMax = $this->convertToBytes(ini_get('upload_max_filesize'));
        if ($uploadMax < 10 * 1024 * 1024) {
            $recommendations[] = [
                'type' => 'info',
                'setting' => 'upload_max_filesize',
                'current' => ini_get('upload_max_filesize'),
                'recommended' => '10M ' . __('or more'),
                'message' => __('File upload limit might be low.'),
            ];
        }

        $postMax = $this->convertToBytes(ini_get('post_max_size'));
        if ($postMax < $uploadMax) {
            $recommendations[] = [
                'type' => 'error',
                'setting' => 'post_max_size',
                'current' => ini_get('post_max_size'),
                'recommended' => __('More than upload_max_filesize'),
                'message' => __('post_max_size is smaller than upload_max_filesize!'),
            ];
        }

        if (!function_exists('opcache_get_status') || @opcache_get_status(false) === false) {
            $recommendations[] = [
                'type' => 'warning',
                'setting' => 'opcache',
                'current' => __('Disabled'),
                'recommended' => __('Active'),
                'message' => __('OPcache is recommended for performance in production.'),
            ];
        }

        $realpathCacheSize = ini_get('realpath_cache_size');
        if ($this->convertToBytes($realpathCacheSize) < 4 * 1024 * 1024) {
            $recommendations[] = [
                'type' => 'info',
                'setting' => 'realpath_cache_size',
                'current' => $realpathCacheSize,
                'recommended' => '4M',
                'message' => __('Realpath cache can be increased.'),
            ];
        }

        if (empty($recommendations)) {
            $recommendations[] = [
                'type' => 'success',
                'setting' => '-',
                'current' => '-',
                'recommended' => '-',
                'message' => '✅ ' . __('All php.ini settings look good.'),
            ];
        }

        return $recommendations;
    }

    protected function getServerInfo(): array
    {
        return [
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? __('Unknown'),
            'server_name' => $_SERVER['SERVER_NAME'] ?? gethostname(),
            'server_addr' => $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname()),
            'document_root' => realpath($_SERVER['DOCUMENT_ROOT'] ?? base_path('public')) ?: base_path('public'),
            'server_protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1',
            'https' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? __('Yes') : __('No'),
            'disk_free_space' => $this->formatBytes(@disk_free_space(base_path())),
            'disk_total_space' => $this->formatBytes(@disk_total_space(base_path())),
            'load_average' => function_exists('sys_getloadavg') ? implode(', ', sys_getloadavg()) : 'N/A',
        ];
    }

    protected function getEmailSettings(): array
    {
        return [
            'driver' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'username' => config('mail.mailers.smtp.username') ? '****' . substr(config('mail.mailers.smtp.username'), -4) : __('None'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];
    }

    protected function getLaravelInfo(): array
    {
        return [
            'version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? __('On') . ' ⚠️' : __('Off') . ' ✅',
            'url' => config('app.url'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'key_set' => config('app.key') ? __('Yes') . ' ✅' : __('No') . ' ❌',
            'maintenance_mode' => app()->isDownForMaintenance() ? __('Yes') : __('No'),
        ];
    }

    protected function getDatabaseInfo(): array
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        return [
            'connection' => $connection,
            'driver' => $config['driver'] ?? __('Unknown'),
            'host' => $config['host'] ?? __('Unknown'),
            'port' => $config['port'] ?? __('Unknown'),
            'database' => $config['database'] ?? __('Unknown'),
            'username' => $config['username'] ?? __('Unknown'),
        ];
    }

    protected function getCacheInfo(): array
    {
        $driver = config('cache.default');

        return [
            'driver' => $driver,
            'prefix' => config('cache.prefix'),
            'session_driver' => config('session.driver'),
            'session_lifetime' => config('session.lifetime') . ' ' . __('minute'),
            'queue_driver' => config('queue.default'),
        ];
    }

    protected function getPerformanceRecommendations(): array
    {
        $recommendations = [];

        $configCached = file_exists(base_path('bootstrap/cache/config.php'));
        $recommendations[] = [
            'category' => 'Laravel',
            'item' => 'Config Cache',
            'status' => $configCached ? '✅ ' . __('Active') : '⚠️ ' . __('Disabled'),
            'type' => $configCached ? 'success' : 'warning',
            'tip' => $configCached ? __('Config cache is active.') : __('Run php artisan config:cache'),
        ];

        $routeCached = file_exists(base_path('bootstrap/cache/routes-v7.php'));
        $recommendations[] = [
            'category' => 'Laravel',
            'item' => 'Route Cache',
            'status' => $routeCached ? '✅ ' . __('Active') : '⚠️ ' . __('Disabled'),
            'type' => $routeCached ? 'success' : 'warning',
            'tip' => $routeCached ? __('Route cache is active.') : __('Run php artisan route:cache'),
        ];

        $viewCacheDir = storage_path('framework/views');
        $viewCount = is_dir($viewCacheDir) ? count(glob($viewCacheDir . '/*.php')) : 0;
        $recommendations[] = [
            'category' => 'Laravel',
            'item' => 'View Cache',
            'status' => $viewCount > 0 ? "✅ {$viewCount} view" : '⚠️ ' . __('Empty'),
            'type' => $viewCount > 0 ? 'success' : 'info',
            'tip' => __('Precompile with php artisan view:cache'),
        ];

        $opcacheEnabled = function_exists('opcache_get_status') && @opcache_get_status(false) !== false;
        $recommendations[] = [
            'category' => 'PHP',
            'item' => 'OPcache',
            'status' => $opcacheEnabled ? '✅ ' . __('Active') : '❌ ' . __('Disabled'),
            'type' => $opcacheEnabled ? 'success' : 'error',
            'tip' => $opcacheEnabled ? __('PHP bytecode cache is active.') : __('Enable OPcache for production.'),
        ];

        $jitEnabled = false;
        if ($opcacheEnabled) {
            $status = @opcache_get_status(false);
            $jitEnabled = isset($status['jit']['enabled']) && $status['jit']['enabled'];
        }
        $recommendations[] = [
            'category' => 'PHP',
            'item' => 'JIT Compiler',
            'status' => $jitEnabled ? '✅ ' . __('Active') : '⚠️ ' . __('Disabled'),
            'type' => $jitEnabled ? 'success' : 'info',
            'tip' => $jitEnabled ? __('JIT compiler is active.') : __('JIT can be enabled for PHP 8+.'),
        ];

        $debugOff = !config('app.debug');
        $recommendations[] = [
            'category' => 'Laravel',
            'item' => 'Debug Mode',
            'status' => $debugOff ? '✅ ' . __('Off') : '⚠️ ' . __('On'),
            'type' => $debugOff ? 'success' : 'warning',
            'tip' => $debugOff ? __('Debug mode is off.') : __('Set APP_DEBUG=false for production.'),
        ];

        $queueDriver = config('queue.default');
        $goodQueueDriver = in_array($queueDriver, ['redis', 'database', 'sqs']);
        $recommendations[] = [
            'category' => 'Laravel',
            'item' => 'Queue Driver',
            'status' => $goodQueueDriver ? "✅ {$queueDriver}" : "⚠️ {$queueDriver}",
            'type' => $goodQueueDriver ? 'success' : 'warning',
            'tip' => $goodQueueDriver ? __('Suitable queue driver.') : __('Use Redis or database.'),
        ];

        $cacheDriver = config('cache.default');
        $goodCacheDriver = in_array($cacheDriver, ['redis', 'memcached', 'file']);
        $recommendations[] = [
            'category' => 'Laravel',
            'item' => 'Cache Driver',
            'status' => $goodCacheDriver ? "✅ {$cacheDriver}" : "⚠️ {$cacheDriver}",
            'type' => $goodCacheDriver ? 'success' : 'warning',
            'tip' => $goodCacheDriver ? __('Suitable cache driver.') : __('Use Redis or memcached.'),
        ];

        $sessionDriver = config('session.driver');
        $goodSessionDriver = in_array($sessionDriver, ['redis', 'database', 'file']);
        $recommendations[] = [
            'category' => 'Laravel',
            'item' => 'Session Driver',
            'status' => $goodSessionDriver ? "✅ {$sessionDriver}" : "⚠️ {$sessionDriver}",
            'type' => $goodSessionDriver ? 'success' : 'warning',
            'tip' => $goodSessionDriver ? __('Suitable session driver.') : __('Use Redis or database.'),
        ];

        $octaneRunning = isset($_SERVER['LARAVEL_OCTANE']) || app()->bound('octane');
        $recommendations[] = [
            'category' => 'Laravel',
            'item' => 'Octane',
            'status' => $octaneRunning ? '✅ ' . __('Active') : '💡 ' . __('Not used'),
            'type' => $octaneRunning ? 'success' : 'info',
            'tip' => $octaneRunning ? __('Super fast with Octane!') : __('Consider Octane for high traffic.'),
        ];

        $horizonInstalled = class_exists(\Laravel\Horizon\Horizon::class);
        if ($horizonInstalled) {
            $recommendations[] = [
                'category' => 'Laravel',
                'item' => 'Horizon',
                'status' => '✅ ' . __('Installed'),
                'type' => 'success',
                'tip' => __('Redis queue management is active.'),
            ];
        }

        return $recommendations;
    }

    protected function convertToBytes(string $value): int
    {
        $value = trim($value);

        if ($value === '-1') {
            return -1;
        }

        $last = strtolower($value[strlen($value) - 1]);
        $value = (int)$value;

        switch ($last) {
            case 'g':
                $value *= 1024;

            case 'm':
                $value *= 1024;

            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    protected function formatBytes(int|false $bytes, int $precision = 2): string
    {
        if ($bytes === false) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}