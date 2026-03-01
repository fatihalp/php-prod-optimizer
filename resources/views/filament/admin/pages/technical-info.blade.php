<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Page Load Performance --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-clock" class="w-5 h-5 text-blue-500" />
                    Page Load Performance
                </div>
            </x-slot>
            {{-- Performance Formula Display --}}
            <div class="flex flex-wrap items-center justify-center gap-2 py-4">
                @php
                $phpTime = null;
                if (defined('LARAVEL_START')) {
                $phpTime = (microtime(true) - LARAVEL_START) * 1000;
                } elseif (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
                $phpTime = (microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000;
                }
                @endphp
                {{-- Backend --}}
                <div x-data="{ showBackendDetail: false }" class="relative">
                    <div @click="showBackendDetail = true"
                        class="flex items-center gap-1 px-3 py-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg cursor-pointer hover:bg-purple-200 dark:hover:bg-purple-900/50 transition"
                        title="Click for details">
                        <span class="text-purple-600 dark:text-purple-400 text-sm">🖥️</span>
                        <span class="text-sm text-purple-700 dark:text-purple-300">Backend</span>
                        <span class="font-bold text-purple-800 dark:text-purple-200">
                            @if($phpTime !== null){{ number_format($phpTime, 0) }}ms @else N/A @endif
                        </span>
                    </div>
                    {{-- Backend Detail Modal --}}
                    <div x-show="showBackendDetail" x-cloak @click.away="showBackendDetail = false"
                        @keydown.escape.window="showBackendDetail = false"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
                        <div @click.stop class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl max-w-md w-full">
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        🖥️ Backend Time Details
                                    </h3>
                                    <button @click="showBackendDetail = false"
                                        class="text-gray-400 hover:text-gray-600">
                                        <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                            <div class="p-4 space-y-3">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                    Backend time = Total PHP execution on server
                                </div>
                                <div class="space-y-2">
                                    <div
                                        class="flex justify-between items-center p-2 bg-purple-100 dark:bg-purple-900/30 rounded border border-purple-300 dark:border-purple-700">
                                        <span class="text-sm font-medium text-purple-700 dark:text-purple-300">🖥️ PHP
                                            Execution Time</span>
                                        <span class="font-mono text-sm font-bold text-purple-800 dark:text-purple-200">
                                            @if($phpTime !== null){{ number_format($phpTime, 0) }}ms @else N/A @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400 mt-3">
                                    Measured from LARAVEL_START to response generation.<br>
                                    Includes: Bootstrap, Routing, Middleware, Controller, Views, DB Queries.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="text-gray-400 text-xl">+</span>
                {{-- Network --}}
                <div x-data="{ showNetworkDetail: false }" class="relative">
                    <div @click="showNetworkDetail = true"
                        class="flex items-center gap-1 px-3 py-2 bg-green-100 dark:bg-green-900/30 rounded-lg cursor-pointer hover:bg-green-200 dark:hover:bg-green-900/50 transition"
                        title="Click for details">
                        <span class="text-green-600 dark:text-green-400 text-sm">🌐</span>
                        <span class="text-sm text-green-700 dark:text-green-300">Network</span>
                        <span class="font-bold text-green-800 dark:text-green-200" id="perf-network">...</span>
                    </div>
                    {{-- Network Detail Modal --}}
                    <div x-show="showNetworkDetail" x-cloak @click.away="showNetworkDetail = false"
                        @keydown.escape.window="showNetworkDetail = false"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
                        <div @click.stop class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl max-w-md w-full">
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        🌐 Network Time Details
                                    </h3>
                                    <button @click="showNetworkDetail = false"
                                        class="text-gray-400 hover:text-gray-600">
                                        <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                            <div class="p-4 space-y-3">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                    Network time = fetchStart → responseEnd
                                </div>
                                <div class="space-y-2" id="network-detail-content">
                                    <div
                                        class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">� Connection
                                            (DNS+TCP+TLS)</span>
                                        <span class="font-mono text-sm font-medium" id="net-connection">-</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">⏳ Server Wait
                                            (TTFB)</span>
                                        <span class="font-mono text-sm font-medium" id="net-waiting">-</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">� Content Download</span>
                                        <span class="font-mono text-sm font-medium" id="net-download">-</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center p-2 bg-green-100 dark:bg-green-900/30 rounded border border-green-300 dark:border-green-700">
                                        <span class="text-sm font-medium text-green-700 dark:text-green-300">🌐 Total
                                            Network</span>
                                        <span class="font-mono text-sm font-bold text-green-800 dark:text-green-200"
                                            id="net-total">-</span>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400 mt-3">
                                    Connection + Server Wait + Download = Total
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="text-gray-400 text-xl">+</span>
                {{-- Frontend --}}
                <div x-data="{ showFrontendDetail: false }" class="relative">
                    <div @click="showFrontendDetail = true"
                        class="flex items-center gap-1 px-3 py-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg cursor-pointer hover:bg-orange-200 dark:hover:bg-orange-900/50 transition"
                        title="Click for details">
                        <span class="text-orange-600 dark:text-orange-400 text-sm">🎨</span>
                        <span class="text-sm text-orange-700 dark:text-orange-300">Frontend</span>
                        <span class="font-bold text-orange-800 dark:text-orange-200" id="perf-frontend">...</span>
                    </div>
                    {{-- Frontend Detail Modal --}}
                    <div x-show="showFrontendDetail" x-cloak @click.away="showFrontendDetail = false"
                        @keydown.escape.window="showFrontendDetail = false"
                        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
                        <div @click.stop class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl max-w-md w-full">
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                        🎨 Frontend Time Details
                                    </h3>
                                    <button @click="showFrontendDetail = false"
                                        class="text-gray-400 hover:text-gray-600">
                                        <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                            <div class="p-4 space-y-3">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                                    Frontend time = Browser processing after response received
                                </div>
                                <div class="space-y-2" id="frontend-detail-content">
                                    <div
                                        class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">📄 DOM Interactive</span>
                                        <span class="font-mono text-sm font-medium" id="fe-dom-parse">-</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">� DOMContentLoaded
                                            Event</span>
                                        <span class="font-mono text-sm font-medium" id="fe-domready">-</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">🖼️ DOM Complete</span>
                                        <span class="font-mono text-sm font-medium" id="fe-resources">-</span>
                                    </div>
                                    <div
                                        class="flex justify-between items-center p-2 bg-orange-100 dark:bg-orange-900/30 rounded border border-orange-300 dark:border-orange-700">
                                        <span class="text-sm font-medium text-orange-700 dark:text-orange-300">🎨 Total
                                            Frontend</span>
                                        <span class="font-mono text-sm font-bold text-orange-800 dark:text-orange-200"
                                            id="fe-total">-</span>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400 mt-3">
                                    Measured from responseEnd to loadEventEnd.<br>
                                    Includes: DOM parsing, CSS, JS execution, images, fonts.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="text-gray-400 text-xl">=</span>
                {{-- Total --}}
                <div class="flex items-center gap-1 px-4 py-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg border-2 border-blue-300 dark:border-blue-700"
                    title="Total page load time">
                    <span class="text-blue-600 dark:text-blue-400 text-sm">⏱️</span>
                    <span class="text-sm text-blue-700 dark:text-blue-300 font-medium">Total</span>
                    <span class="font-bold text-lg text-blue-800 dark:text-blue-200" id="perf-total">...</span>
                </div>
            </div>
            {{-- Memory & Status Row --}}
            <div class="flex items-center justify-center gap-4 mt-2">
                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <span>💾</span>
                    <span>Memory:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ number_format(memory_get_peak_usage(true)
                        / 1024 / 1024, 0) }} MB</span>
                </div>
                <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>
                <div class="flex items-center gap-2 text-sm" id="perf-status-container">
                    <span>Status:</span>
                    <span id="perf-status" class="font-medium">Analyzing...</span>
                </div>
            </div>
        </x-filament::section>

        <script>
            document.addEventListener('DOMContentLoaded', function () {

                window.addEventListener('load', function () {
                    setTimeout(function () {
                        const timing = performance.getEntriesByType('navigation')[0];
                        if (!timing) return;

                        const networkTime = Math.round(timing.responseEnd - timing.fetchStart);
                        const frontendTime = Math.round(timing.loadEventEnd - timing.responseEnd);
                        const totalTime = Math.round(timing.loadEventEnd - timing.fetchStart);

                        const connectionTime = Math.round(timing.connectEnd - timing.fetchStart);
                        const serverWaitTime = Math.round(timing.responseStart - timing.connectEnd);
                        const downloadTime = Math.round(timing.responseEnd - timing.responseStart);

                        document.getElementById('net-connection').textContent = connectionTime + 'ms';
                        document.getElementById('net-waiting').textContent = serverWaitTime + 'ms';
                        document.getElementById('net-download').textContent = downloadTime + 'ms';
                        document.getElementById('net-total').textContent = networkTime + 'ms';

                        const networkEl = document.getElementById('perf-network');
                        if (networkEl) {
                            networkEl.innerHTML = networkTime + 'ms';
                        }

                        const frontendEl = document.getElementById('perf-frontend');
                        if (frontendEl) {
                            frontendEl.innerHTML = frontendTime + 'ms';
                        }

                        const domParseTime = Math.round(timing.domInteractive - timing.responseEnd);
                        const domContentLoaded = Math.round(timing.domContentLoadedEventEnd - timing.domContentLoadedEventStart);
                        const domComplete = Math.round(timing.domComplete - timing.domInteractive);
                        const loadEvent = Math.round(timing.loadEventEnd - timing.loadEventStart);

                        document.getElementById('fe-dom-parse').textContent = domParseTime + 'ms';
                        document.getElementById('fe-resources').textContent = domComplete + 'ms';
                        document.getElementById('fe-domready').textContent = domContentLoaded + 'ms';
                        document.getElementById('fe-total').textContent = frontendTime + 'ms';

                        const totalEl = document.getElementById('perf-total');
                        if (totalEl) {
                            totalEl.innerHTML = totalTime + 'ms';
                        }

                        const statusEl = document.getElementById('perf-status');
                        if (statusEl) {
                            if (totalTime > 3000) {
                                statusEl.innerHTML = '⚠️ Slow';
                                statusEl.classList.add('text-red-600');
                            } else if (totalTime > 1000) {
                                statusEl.innerHTML = '💡 Average';
                                statusEl.classList.add('text-yellow-600');
                            } else {
                                statusEl.innerHTML = '✅ Fast';
                                statusEl.classList.add('text-green-600');
                            }
                        }
                    }, 100);
                });
            });
        </script>

        {{-- Performance Recommendations --}}
        <div x-data="{ showModal: null }">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-rocket-launch" class="w-5 h-5 text-emerald-500" />
                        Performance Recommendations
                        <span class="text-xs text-gray-400 font-normal">(Click for details)</span>
                    </div>
                </x-slot>
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    @foreach($performanceRecommendations as $index => $rec)
                    <div @click="showModal = '{{ $rec['item'] }}'"
                        class="p-3 rounded-lg border cursor-pointer transition-all hover:scale-[1.02] hover:shadow-md @if($rec['type'] === 'success') bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800 @elseif($rec['type'] === 'error') bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800 @elseif($rec['type'] === 'warning') bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800 @else bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 @endif">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $rec['category'] }}</span>
                            <span class="text-sm">{!! $rec['status'] !!}</span>
                        </div>
                        <div class="font-medium text-gray-900 dark:text-white mt-1">{{ $rec['item'] }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $rec['tip'] }}</div>
                    </div>
                    @endforeach
                </div>
            </x-filament::section>

            {{-- JIT Compiler Modal --}}
            <div x-show="showModal === 'JIT Compiler'" x-cloak @click.away="showModal = null"
                @keydown.escape.window="showModal = null"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overflow-y-auto">
                <div @click.stop class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl max-w-2xl w-full my-8">
                    <div
                        class="sticky top-0 bg-white dark:bg-gray-900 p-6 pb-4 border-b border-gray-200 dark:border-gray-700 rounded-t-xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <x-filament::icon icon="heroicon-o-bolt" class="w-5 h-5 text-yellow-500" />
                                JIT Compiler Etkinleştirme
                            </h3>
                            <button @click="showModal = null" class="text-gray-400 hover:text-gray-600">
                                <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                    <div class="p-6 pt-4 max-h-[60vh] overflow-y-auto">
                        <div class="space-y-4 text-sm">
                            <div class="font-medium text-gray-900 dark:text-white">🚀 Laravel Octane + Swoole ile JIT
                                Installedmu</div>

                            <div class="space-y-3">
                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">1. Swoole Extension Installedmu</div>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block"># PECL ile</code>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block">pecl install swoole</code>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block mt-2"># Ubuntu/Debian</code>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block">sudo apt install php8.3-swoole</code>
                                </div>

                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">2. Octane Installedmu</div>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block">composer require laravel/octane</code>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block mt-1">php artisan octane:install --server=swoole</code>
                                </div>

                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">3. php.ini Ayarları (OPcache + JIT)</div>
                                    <pre
                                        class="text-xs font-mono text-amber-600 dark:text-amber-400 whitespace-pre-wrap">[opcache]
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=64
opcache.max_accelerated_files=30000
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.jit=1255
opcache.jit_buffer_size=128M</pre>
                                </div>

                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">4. Octane Başlat</div>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block">php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- OPcache Modal --}}
            <div x-show="showModal === 'OPcache'" x-cloak @click.away="showModal = null"
                @keydown.escape.window="showModal = null"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overflow-y-auto">
                <div @click.stop class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl max-w-2xl w-full my-8">
                    <div
                        class="sticky top-0 bg-white dark:bg-gray-900 p-6 pb-4 border-b border-gray-200 dark:border-gray-700 rounded-t-xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <x-filament::icon icon="heroicon-o-cpu-chip" class="w-5 h-5 text-blue-500" />
                                OPcache Etkinleştirme
                            </h3>
                            <button @click="showModal = null" class="text-gray-400 hover:text-gray-600">
                                <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                    <div class="p-6 pt-4 max-h-[60vh] overflow-y-auto">
                        <div class="space-y-4 text-sm">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <div class="font-medium text-blue-800 dark:text-blue-200 mb-2">📋 OPcache Nedir?</div>
                                <p class="text-blue-700 dark:text-blue-300">OPcache, PHP bytecode'unu önbelleğe alarak
                                    her istekte yeniden derleme ihtiyacını ortadan kaldırır. Production ortamları için
                                    şarttır.</p>
                            </div>

                            <div class="font-medium text-gray-900 dark:text-white">🛠️ Installedm Adımları</div>

                            <div class="space-y-3">
                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">1. OPcache Modülünü Etkinleştir</div>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block"># Ubuntu/Debian</code>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block">sudo phpenmod opcache</code>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block mt-2"># macOS (Homebrew)</code>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block">PHP kurulumunda varsayılan olarak gelir</code>
                                </div>

                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">2. php.ini Recommended Settings</div>
                                    <pre
                                        class="text-xs font-mono text-amber-600 dark:text-amber-400 whitespace-pre-wrap">[opcache]
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=64
opcache.max_accelerated_files=30000
opcache.validate_timestamps=0
opcache.revalidate_freq=0
opcache.save_comments=1
opcache.enable_file_override=1</pre>
                                </div>

                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">3. FrankenPHP ile Kullanım</div>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block">php artisan octane:start --server=frankenphp</code>
                                    <p class="text-xs text-gray-500 mt-1">FrankenPHP worker modunda çalışırken OPcache
                                        otomatik olarak en verimli şekilde kullanılır.</p>
                                </div>

                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">4. Önbelleği Temizle (Deploy sonrası)</div>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block">php artisan octane:reload</code>
                                </div>
                            </div>

                            <div class="p-4 bg-green-50 dark:bg-green-900/30 rounded-lg">
                                <div class="font-medium text-green-800 dark:text-green-200 mb-1">✅ Beklenen Performans
                                    Artışı</div>
                                <p class="text-xs text-green-700 dark:text-green-300">OPcache ile 2-3x, Octane ile
                                    birlikte 5-10x hız artışı beklenebilir.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Octane Modal --}}
            <div x-show="showModal === 'Octane'" x-cloak @click.away="showModal = null"
                @keydown.escape.window="showModal = null"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 overflow-y-auto">
                <div @click.stop class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl max-w-2xl w-full my-8">
                    <div
                        class="sticky top-0 bg-white dark:bg-gray-900 p-6 pb-4 border-b border-gray-200 dark:border-gray-700 rounded-t-xl">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                <x-filament::icon icon="heroicon-o-bolt" class="w-5 h-5 text-yellow-500" />
                                Laravel Octane + FrankenPHP
                            </h3>
                            <button @click="showModal = null" class="text-gray-400 hover:text-gray-600">
                                <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                    <div class="p-6 pt-4 max-h-[60vh] overflow-y-auto">
                        <div class="space-y-4 text-sm">
                            <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <div class="font-medium text-blue-800 dark:text-blue-200 mb-2">📋 Octane Nedir?</div>
                                <p class="text-blue-700 dark:text-blue-300">Laravel Octane, uygulamanızı yüksek
                                    performanslı sunucularda (FrankenPHP, Swoole, RoadRunner) çalıştırarak her istekte
                                    framework'ü yeniden başlatma ihtiyacını ortadan kaldırır.</p>
                            </div>

                            <div class="font-medium text-gray-900 dark:text-white">🚀 FrankenPHP ile Installedm</div>

                            <div class="space-y-3">
                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">1. Octane Installedmu</div>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block">composer require laravel/octane</code>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block mt-1">php artisan octane:install --server=frankenphp</code>
                                </div>

                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">2. FrankenPHP İndir (macOS)</div>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block">curl -L https://github.com/dunglas/frankenphp/releases/latest/download/frankenphp-mac-arm64 -o frankenphp</code>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block mt-1">chmod +x frankenphp && sudo mv frankenphp /usr/local/bin/</code>
                                </div>

                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">3. config/octane.php Ayarları</div>
                                    <pre
                                        class="text-xs font-mono text-amber-600 dark:text-amber-400 whitespace-pre-wrap">'server' => 'frankenphp',
'workers' => env('OCTANE_WORKERS', 'auto'),
'max_requests' => env('OCTANE_MAX_REQUESTS', 500),</pre>
                                </div>

                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">4. Development Başlat</div>
                                    <code
                                        class="text-xs font-mono text-green-600 dark:text-green-400 block">php artisan octane:start --watch</code>
                                </div>

                                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-xs text-gray-500 mb-1">5. Production (Supervisor)</div>
                                    <pre
                                        class="text-xs font-mono text-purple-600 dark:text-purple-400 whitespace-pre-wrap">[program:octane]
command=php /var/www/artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8000
user=www-data
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/octane.log</pre>
                                </div>
                            </div>

                            <div class="p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg">
                                <div class="font-medium text-yellow-800 dark:text-yellow-200 mb-1">⚠️ Dikkat Edilecekler
                                </div>
                                <ul
                                    class="text-xs text-yellow-700 dark:text-yellow-300 list-disc list-inside space-y-1">
                                    <li>Static property'ler request'ler arasında paylaşılır</li>
                                    <li>Global state kullanmaktan kaçının</li>
                                    <li>Deploy sonrası <code
                                            class="bg-yellow-200 dark:bg-yellow-800 px-1 rounded">php artisan octane:reload</code>
                                    </li>
                                </ul>
                            </div>

                            <div class="p-4 bg-green-50 dark:bg-green-900/30 rounded-lg">
                                <div class="font-medium text-green-800 dark:text-green-200 mb-1">✅ Beklenen Performans
                                </div>
                                <p class="text-xs text-green-700 dark:text-green-300">FrankenPHP ile 5-10x hız artışı,
                                    düşük gecikme, yüksek eşzamanlılık.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Generic Info Modal for other items --}}
            @foreach(['Config Cache', 'Route Cache', 'View Cache', 'Debug Mode', 'Queue Driver', 'Cache Driver',
            'Session Driver', 'Horizon'] as $item)
            <div x-show="showModal === '{{ $item }}'" x-cloak @click.away="showModal = null"
                @keydown.escape.window="showModal = null"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
                <div @click.stop class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl max-w-md w-full">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $item }}</h3>
                            <button @click="showModal = null" class="text-gray-400 hover:text-gray-600">
                                <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                            </button>
                        </div>
                        <div class="space-y-3 text-sm">
                            @switch($item)
                            @case('Config Cache')
                            <p class="text-gray-600 dark:text-gray-300">Caches configuration files.</p>
                            <code
                                class="block p-2 bg-gray-100 dark:bg-gray-800 rounded text-green-600 dark:text-green-400 font-mono text-xs">php artisan config:cache</code>
                            <p class="text-xs text-gray-500">Production için şarttır. Değişiklik sonrası yeniden
                                çalıştırın.</p>
                            @break
                            @case('Route Cache')
                            <p class="text-gray-600 dark:text-gray-300">Route tanımlarını önbelleğe alır.</p>
                            <code
                                class="block p-2 bg-gray-100 dark:bg-gray-800 rounded text-green-600 dark:text-green-400 font-mono text-xs">php artisan route:cache</code>
                            <p class="text-xs text-gray-500">Closure route'lar önbelleğe alınamaz.</p>
                            @break
                            @case('View Cache')
                            <p class="text-gray-600 dark:text-gray-300">Blade view'ları önceden derler.</p>
                            <code
                                class="block p-2 bg-gray-100 dark:bg-gray-800 rounded text-green-600 dark:text-green-400 font-mono text-xs">php artisan view:cache</code>
                            @break
                            @case('Debug Mode')
                            <p class="text-gray-600 dark:text-gray-300">Production'da debug mode kapalı olmalı.</p>
                            <code
                                class="block p-2 bg-gray-100 dark:bg-gray-800 rounded text-amber-600 dark:text-amber-400 font-mono text-xs">APP_DEBUG=false</code>
                            <p class="text-xs text-gray-500">.env dosyasında ayarlayın.</p>
                            @break
                            @case('Queue Driver')
                            <p class="text-gray-600 dark:text-gray-300">Production için Redis veya database kullanın.
                            </p>
                            <code
                                class="block p-2 bg-gray-100 dark:bg-gray-800 rounded text-amber-600 dark:text-amber-400 font-mono text-xs">QUEUE_CONNECTION=redis</code>
                            @break
                            @case('Cache Driver')
                            <p class="text-gray-600 dark:text-gray-300">Hız için Redis veya Memcached kullanın.</p>
                            <code
                                class="block p-2 bg-gray-100 dark:bg-gray-800 rounded text-amber-600 dark:text-amber-400 font-mono text-xs">CACHE_STORE=redis</code>
                            @break
                            @case('Session Driver')
                            <p class="text-gray-600 dark:text-gray-300">Ölçeklenebilirlik için Redis veya database.</p>
                            <code
                                class="block p-2 bg-gray-100 dark:bg-gray-800 rounded text-amber-600 dark:text-amber-400 font-mono text-xs">SESSION_DRIVER=redis</code>
                            @break
                            @case('Horizon')
                            <p class="text-gray-600 dark:text-gray-300">Redis kuyruk yönetimi için dashboard.</p>
                            <code
                                class="block p-2 bg-gray-100 dark:bg-gray-800 rounded text-green-600 dark:text-green-400 font-mono text-xs">composer require laravel/horizon</code>
                            <code
                                class="block p-2 bg-gray-100 dark:bg-gray-800 rounded text-green-600 dark:text-green-400 font-mono text-xs mt-1">php artisan horizon:install</code>
                            @break
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Laravel, Octane, OPcache, PHP.ini - 4 kutu yan yana --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Laravel Info --}}
            <x-filament::section class="h-full">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-cube" class="w-5 h-5 text-red-500" />
                        Laravel
                    </div>
                </x-slot>
                <div class="space-y-2">
                    @foreach($laravelInfo as $key => $value)
                    <div
                        class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ str_replace('_', ' ', $key)
                            }}</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{!! $value !!}</span>
                    </div>
                    @endforeach
                </div>
            </x-filament::section>

            {{-- Octane Status --}}
            <x-filament::section class="h-full">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-bolt" class="w-5 h-5 text-yellow-500" />
                        Octane
                    </div>
                </x-slot>
                <div class="space-y-2">
                    <div class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">Status</span>
                        <span class="text-sm font-medium">{!! $octaneInfo['status'] !!}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">Installed</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $octaneInfo['installed'] ?
                            'Evet' : 'Hayır' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">Çalışıyor</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $octaneInfo['running'] ?
                            'Evet' : 'Hayır' }}</span>
                    </div>
                    @if($octaneInfo['server'])
                    <div class="flex justify-between items-center py-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">Server</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $octaneInfo['server']
                            }}</span>
                    </div>
                    @endif
                </div>
            </x-filament::section>

            {{-- OPcache Status --}}
            <x-filament::section class="h-full">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-cpu-chip" class="w-5 h-5 text-blue-500" />
                        OPcache
                    </div>
                </x-slot>
                <div class="space-y-2">
                    <div class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">Status</span>
                        <span class="text-sm font-medium">{!! $opcacheInfo['status'] !!}</span>
                    </div>
                    @if($opcacheInfo['enabled'] && isset($opcacheInfo['memory_usage']))
                    <div class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">Used</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{
                            number_format(($opcacheInfo['memory_usage']['used_memory'] ?? 0) / 1024 / 1024, 1) }}
                            MB</span>
                    </div>
                    <div class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">Free</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{
                            number_format(($opcacheInfo['memory_usage']['free_memory'] ?? 0) / 1024 / 1024, 1) }}
                            MB</span>
                    </div>
                    @if(isset($opcacheInfo['statistics']))
                    @php
                    $hits = $opcacheInfo['statistics']['hits'] ?? 0;
                    $misses = $opcacheInfo['statistics']['misses'] ?? 0;
                    $total = $hits + $misses;
                    $hitRate = $total > 0 ? round(($hits / $total) * 100, 1) : 0;
                    @endphp
                    <div class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">Hit Rate</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $hitRate }}%</span>
                    </div>
                    @endif
                    @endif
                    @if($opcacheInfo['enabled'] && isset($opcacheInfo['jit']) && $opcacheInfo['jit'])
                    <div class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">JIT</span>
                        <span class="text-sm font-medium">{{ $opcacheInfo['jit']['enabled'] ? '✅' : '❌' }}</span>
                    </div>
                    @endif
                    @if($opcacheInfo['enabled'] && isset($opcacheInfo['validate_timestamps']))
                    <div class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase"
                            title="Dosya değişikliklerini kontrol et">validate_timestamps</span>
                        <span
                            class="text-sm font-medium {{ $opcacheInfo['validate_timestamps'] == '0' ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $opcacheInfo['validate_timestamps'] == '0' ? '0 (Ideal for prod)' :
                            $opcacheInfo['validate_timestamps'] }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase"
                            title="Dosya kontrolü sıklığı (saniye)">revalidate_freq</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{
                            $opcacheInfo['revalidate_freq'] ?? '2' }}s</span>
                    </div>
                    @endif
                </div>
            </x-filament::section>

            {{-- PHP.ini Recommendations --}}
            <x-filament::section class="h-full">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-5 h-5 text-orange-500" />
                        PHP.ini
                    </div>
                </x-slot>
                <div class="space-y-2">
                    @foreach($phpIniRecommendations as $rec)
                    <div class="py-1 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <div class="flex justify-between items-center">
                            <span
                                class="text-xs font-medium @if($rec['type'] === 'error') text-red-600 @elseif($rec['type'] === 'warning') text-yellow-600 @elseif($rec['type'] === 'success') text-green-600 @else text-blue-600 @endif">{{
                                $rec['setting'] }}</span>
                            @if($rec['current'] !== '-')
                            <span class="text-xs font-mono text-gray-500">{{ $rec['current'] }}</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($rec['message'], 40) }}</div>
                    </div>
                    @endforeach
                </div>
            </x-filament::section>
        </div>

        {{-- Octane Diagnostics --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-bolt" class="w-5 h-5 text-yellow-500" />
                    {{ __('Octane Diagnostics') }}
                    <span class="ml-2 px-2 py-0.5 text-xs rounded-full
                        @if($octaneDiagnostics['status'] === 'running') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                        @elseif($octaneDiagnostics['status'] === 'error') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                        @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                        @endif">
                        {{ $octaneDiagnostics['status_message'] }}
                    </span>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {{-- Config & ENV --}}
                <div class="space-y-4">
                    {{-- Config --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-cog-6-tooth" class="w-4 h-4 text-gray-500" />
                            {{ __('Configuration') }}
                        </div>
                        @if(!empty($octaneDiagnostics['config']))
                        <div class="space-y-2">
                            @foreach($octaneDiagnostics['config'] as $key => $value)
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500 dark:text-gray-400 uppercase">{{ $key }}</span>
                                <span class="font-mono text-gray-900 dark:text-white">{{ $value }}</span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-xs text-gray-500">{{ __('Configuration not found') }}</p>
                        @endif
                    </div>

                    {{-- ENV Values --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-document-text" class="w-4 h-4 text-gray-500" />
                            {{ __('Environment Variables') }}
                        </div>
                        @if(!empty($octaneDiagnostics['env']))
                        <div class="space-y-2">
                            @foreach($octaneDiagnostics['env'] as $key => $value)
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500 dark:text-gray-400 font-mono">{{ $key }}</span>
                                <span class="font-mono text-gray-900 dark:text-white">{{ $value }}</span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-xs text-gray-500">{{ __('ENV variables not found') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Checks --}}
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-sm font-medium text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-clipboard-document-check" class="w-4 h-4 text-gray-500" />
                        {{ __('Status Checks') }}
                    </div>
                    @if(!empty($octaneDiagnostics['checks']))
                    <div class="space-y-2">
                        @foreach($octaneDiagnostics['checks'] as $check)
                        <div class="flex items-start gap-2 p-2 rounded border
                                    @if($check['status'] === 'ok') bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800
                                    @elseif($check['status'] === 'error') bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800
                                    @elseif($check['status'] === 'warning') bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800
                                    @else bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800
                                    @endif">
                            <span class="text-sm">
                                @if($check['status'] === 'ok') ✅
                                @elseif($check['status'] === 'error') ❌
                                @elseif($check['status'] === 'warning') ⚠️
                                @else ℹ️
                                @endif
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-medium text-gray-900 dark:text-white">{{ $check['name'] }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $check['message'] }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-gray-500">{{ __('Check failed') }}</p>
                    @endif
                </div>

                {{-- Problems & Recommendations --}}
                <div class="space-y-4">
                    {{-- Problems --}}
                    @if(!empty($octaneDiagnostics['problems']))
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                        <div class="text-sm font-medium text-red-800 dark:text-red-200 mb-2 flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-exclamation-circle" class="w-4 h-4" />
                            {{ __('Detected Issues') }}
                        </div>
                        <ul class="space-y-1">
                            @foreach($octaneDiagnostics['problems'] as $problem)
                            <li class="text-xs text-red-700 dark:text-red-300 flex items-start gap-1">
                                <span>•</span>
                                <span>{{ $problem }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Recommendations --}}
                    @if(!empty($octaneDiagnostics['recommendations']))
                    <div
                        class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2 flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-light-bulb" class="w-4 h-4" />
                            {{ __('Recommended Actions') }}
                        </div>
                        <div class="space-y-2">
                            @foreach($octaneDiagnostics['recommendations'] as $rec)
                            @if(str_starts_with($rec, 'http'))
                            <a href="{{ $rec }}" target="_blank"
                                class="block text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                🔗 {{ $rec }}
                            </a>
                            @else
                            <code
                                class="block text-xs font-mono bg-blue-100 dark:bg-blue-800/50 text-blue-800 dark:text-blue-200 px-2 py-1 rounded">
                                            {{ $rec }}
                                        </code>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Success message if no issues --}}
                    @if(empty($octaneDiagnostics['problems']) && $octaneDiagnostics['status'] === 'running')
                    <div
                        class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="text-sm font-medium text-green-800 dark:text-green-200 flex items-center gap-2">
                            <x-filament::icon icon="heroicon-o-check-circle" class="w-5 h-5" />
                            Octane is running properly!
                        </div>
                        <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                            Your application is running on the high-performance Octane server.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </x-filament::section>

        {{-- PHP Info --}}
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-code-bracket" class="w-5 h-5 text-purple-500" />
                    {{ __('PHP Info') }}
                </div>
            </x-slot>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Version') }}</div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $phpInfo['version'] }}</div>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('SAPI') }}</div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $phpInfo['sapi'] }}</div>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('OS') }}</div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $phpInfo['os'] }}</div>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Memory') }}</div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $phpInfo['memory_limit'] }}
                    </div>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Exec Time') }}</div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{
                        $phpInfo['max_execution_time'] }}</div>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Post Size') }}</div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $phpInfo['post_max_size'] }}
                    </div>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Upload') }}</div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{
                        $phpInfo['upload_max_filesize'] }}</div>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('Timezone') }}</div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">{{ $phpInfo['date_timezone'] ?:
                        'N/A' }}</div>
                </div>
                <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('PCRE JIT') }}</div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">{!! $phpInfo['pcre_jit'] !!}
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-2">{{ __('Extensions') }} ({{
                    count($phpInfo['extensions']) }})</div>
                <div class="flex flex-wrap gap-1">
                    @foreach($phpInfo['extensions'] as $ext)
                    <span
                        class="px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded">{{
                        $ext }}</span>
                    @endforeach
                </div>
            </div>
        </x-filament::section>

        {{-- Server, Email, Veritabanı, Cache - 4 yan yana --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Server Info --}}
            <x-filament::section class="h-full">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-server" class="w-5 h-5 text-green-500" />
                        {{ __('Server') }}
                    </div>
                </x-slot>
                <div class="space-y-2">
                    @foreach($serverInfo as $key => $value)
                    <div
                        class="flex justify-between items-start py-1 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __(str_replace('_', ' ',
                            $key)) }}</span>
                        <span
                            class="text-xs font-medium text-gray-900 dark:text-white text-right max-w-[120px] truncate"
                            title="{{ $value }}">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            </x-filament::section>

            {{-- Email Ayarları --}}
            <x-filament::section class="h-full">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-envelope" class="w-5 h-5 text-pink-500" />
                        {{ __('Email') }}
                    </div>
                </x-slot>
                <div class="space-y-2">
                    @foreach($emailSettings as $key => $value)
                    <div
                        class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __(str_replace('_', ' ',
                            $key)) }}</span>
                        <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $value ?? __('None')
                            }}</span>
                    </div>
                    @endforeach
                </div>
            </x-filament::section>

            {{-- Database Info --}}
            <x-filament::section class="h-full">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-circle-stack" class="w-5 h-5 text-cyan-500" />
                        {{ __('Database') }}
                    </div>
                </x-slot>
                <div class="space-y-2">
                    @foreach($databaseInfo as $key => $value)
                    <div
                        class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __(str_replace('_', ' ',
                            $key)) }}</span>
                        <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            </x-filament::section>

            {{-- Cache & Session Info --}}
            <x-filament::section class="h-full">
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-archive-box" class="w-5 h-5 text-indigo-500" />
                        {{ __('Cache') }}
                    </div>
                </x-slot>
                <div class="space-y-2">
                    @foreach($cacheInfo as $key => $value)
                    <div
                        class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700 last:border-0">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __(str_replace('_', ' ',
                            $key)) }}</span>
                        <span class="text-xs font-medium text-gray-900 dark:text-white">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>