<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dasbor') - {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset(\App\Models\Setting::get('web_favicon', 'favicon.ico')) }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Tailwind CSS & JS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- PWA Configuration -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#10b981">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="FutsalHub">
    <link rel="apple-touch-icon" href="{{ asset('images/web_logo_1780410241.webp') }}">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('serviceworker.js') }}")
                    .then(reg => {
                        console.log('PWA Service Worker registered successfully.');
                        initPushSubscription(reg);
                    })
                    .catch(err => console.error('PWA Service Worker registration failed.', err));
            });
        }

        function initPushSubscription(reg) {
            if (!('PushManager' in window)) {
                console.warn('Push messaging is not supported in this browser.');
                return;
            }

            Notification.requestPermission().then(permission => {
                if (permission !== 'granted') {
                    console.log('Notification permission was not granted.');
                    return;
                }

                reg.pushManager.getSubscription()
                    .then(subscription => {
                        if (subscription) {
                            sendSubscriptionToBackend(subscription);
                        } else {
                            const publicKey = "{{ config('services.vapid.public_key') }}";
                            if (!publicKey) {
                                console.error('VAPID Public Key is missing.');
                                return;
                            }
                            
                            const subscribeOptions = {
                                userVisibleOnly: true,
                                applicationServerKey: urlBase64ToUint8Array(publicKey)
                            };

                            reg.pushManager.subscribe(subscribeOptions)
                                .then(newSubscription => {
                                    console.log('New push subscription created.');
                                    sendSubscriptionToBackend(newSubscription);
                                })
                                .catch(err => console.error('Failed to subscribe to push notifications.', err));
                        }
                    });
            });
        }

        function sendSubscriptionToBackend(subscription) {
            const key = subscription.getKey('p256dh');
            const token = subscription.getKey('auth');
            const contentEncoding = (PushManager.supportedContentEncodings || ['aes128gcm'])[0];

            fetch('/v1/push-subscriptions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    endpoint: subscription.endpoint,
                    keys: {
                        p256dh: btoa(String.fromCharCode.apply(null, new Uint8Array(key))),
                        auth: btoa(String.fromCharCode.apply(null, new Uint8Array(token)))
                    },
                    content_encoding: contentEncoding
                })
            })
            .then(res => res.json())
            .then(data => console.log('Subscription synced with server:', data.message))
            .catch(err => console.error('Failed to sync subscription with server.', err));
        }

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding)
                .replace(/\-/g, '+')
                .replace(/_/g, '/');

            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);

            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
    </script>
    @yield('styles')
</head>

<body
    class="min-h-screen flex flex-col lg:flex-row relative overflow-x-hidden bg-slate-50 lg:h-screen lg:overflow-hidden"
    data-is-settings-route="{{ (request()->routeIs('settings.*') || request()->routeIs('superadmin.settings.*')) ? 'true' : 'false' }}">
    <!-- Background lights wrapper to prevent horizontal overflow -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute w-[400px] h-[400px] bg-emerald-500/[0.02] rounded-full blur-[100px] top-10 left-10"></div>
        <div class="absolute w-[400px] h-[400px] bg-teal-500/[0.02] rounded-full blur-[100px] bottom-10 right-10"></div>
    </div>

    <!-- Mobile Sidebar Toggle -->
    <div
        class="lg:hidden flex items-center justify-between px-6 py-3.5 bg-white border-b border-slate-200 relative z-30">
        <div class="flex items-center gap-2.5 min-w-0">
            <img src="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}"
                class="w-8 h-8 object-contain rounded-lg bg-white border border-slate-100 p-0.5 shrink-0" alt="Logo">
            <span class="font-extrabold text-sm text-slate-900 tracking-tight truncate max-w-[185px]">
                {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}
            </span>
        </div>
        <button id="mobileMenuBtn" class="text-slate-800 hover:text-emerald-600 text-xl focus:outline-none p-1">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <!-- Sidebar Layout -->
    <aside id="sidebar"
        class="sidebar-white w-60 flex-col fixed lg:sticky top-0 bottom-0 left-0 z-40 flex h-screen transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out lg:flex">
        <!-- Logo -->
        <div
            class="h-16 md:h-20 px-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50/50 to-white shrink-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <img src="{{ asset(\App\Models\Setting::get('web_logo', 'images/logo.png')) }}"
                    class="w-8 h-8 object-contain shrink-0 rounded-lg" alt="Logo">
                <div class="min-w-0">
                    <div class="flex items-center gap-1">
                        <span class="font-extrabold text-sm text-slate-900 tracking-tight">
                            {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}
                        </span>
                    </div>
                    <div class="text-[8px] text-slate-400 uppercase tracking-widest font-extrabold mt-0.5">Sistem
                        Manajemen Tim</div>
                </div>
            </div>
            <!-- Close Button (Mobile & Tablet) -->
            <button id="closeSidebarBtn"
                class="lg:hidden text-slate-450 hover:text-red-500 text-lg focus:outline-none p-1 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- User Role Widget -->
        <div
            class="p-3 mx-3 my-4 bg-white rounded-2xl border border-slate-150 shadow-[0_4px_12px_rgba(0,0,0,0.02)] flex items-center gap-3 relative overflow-hidden group">
            <div
                class="absolute inset-0 bg-gradient-to-r from-emerald-500/[0.01] to-teal-500/[0.01] opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            </div>
            <div class="relative shrink-0">
                @if(Auth::user()->avatar && file_exists(public_path(Auth::user()->avatar)))
                    <img src="{{ asset(Auth::user()->avatar) }}"
                        class="w-8 h-8 rounded-full object-cover border border-slate-200 shadow-sm" alt="Avatar">
                @else
                    <div
                        class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-500/10 to-teal-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-700 text-xs font-extrabold shadow-inner">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
                <span
                    class="w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0 shadow-sm animate-pulse"></span>
            </div>
            <div class="overflow-hidden flex-1 relative z-10">
                <div class="font-extrabold text-xs text-slate-800 truncate leading-snug">{{ Auth::user()->name }}</div>
                <div class="flex items-center gap-1.5 mt-0.5">
                    @php
                        $roleColors = [
                            'superadmin' => 'bg-red-50 border-red-100 text-red-600',
                            'management' => 'bg-teal-50 border-teal-100 text-teal-600',
                            'coach' => 'bg-yellow-50 border-yellow-100 text-yellow-600',
                            'player' => 'bg-blue-50 border-blue-100 text-blue-600',
                        ];
                        $roleLabel = [
                            'superadmin' => 'Superadmin',
                            'management' => 'Manager',
                            'coach' => 'Pelatih',
                            'player' => 'Pemain',
                        ];
                        $roleClass = $roleColors[Auth::user()->role->name] ?? 'bg-slate-100 text-slate-500 border-slate-200';
                        $roleName = $roleLabel[Auth::user()->role->name] ?? 'User';
                    @endphp
                    <span class="px-1.5 py-0.5 text-[8px] font-bold rounded border {{ $roleClass }} leading-none">
                        {{ $roleName }}
                    </span>
                    @if(Auth::user()->team)
                        <span class="text-[8px] text-slate-400 font-bold truncate max-w-[65px]"
                            title="{{ Auth::user()->team->name }}">
                            {{ Auth::user()->team->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-3 space-y-4 overflow-y-auto pt-2">
            @if(Auth::user()->isSuperAdmin())
                <div class="space-y-1">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest px-3 block mb-1">Menu
                        Utama</span>
                    <a href="{{ route('superadmin.dashboard') }}"
                        class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('superadmin.dashboard')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-650 hover:text-emerald-600 hover:bg-slate-50 @endif">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i
                                class="fa-solid fa-chart-line text-sm shrink-0 @if(request()->routeIs('superadmin.dashboard')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                            <span class="truncate">Monitor Platform</span>
                        </div>
                        <i
                            class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </a>
                </div>

                <div class="space-y-1">
                    <span
                        class="text-[8px] font-black text-slate-400 uppercase tracking-widest px-3 block mb-1">Administrasi
                        Platform</span>
                    <a href="{{ route('superadmin.teams') }}"
                        class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('superadmin.teams*')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-650 hover:text-emerald-600 hover:bg-slate-50 @endif">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i
                                class="fa-solid fa-shield-halved text-sm shrink-0 @if(request()->routeIs('superadmin.teams*')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                            <span class="truncate">Kelola Tenant (Tim)</span>
                        </div>
                        <i
                            class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </a>

                    <a href="{{ route('superadmin.users') }}"
                        class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('superadmin.users*')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-650 hover:text-emerald-600 hover:bg-slate-50 @endif">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i
                                class="fa-solid fa-users-gear text-sm shrink-0 @if(request()->routeIs('superadmin.users*')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                            <span class="truncate">Kelola Pengguna</span>
                        </div>
                        <i
                            class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </a>

                    <a href="{{ route('superadmin.payments.index') }}"
                        class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('superadmin.payments*')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-655 hover:text-emerald-600 hover:bg-slate-50 @endif">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i
                                class="fa-solid fa-receipt text-sm shrink-0 @if(request()->routeIs('superadmin.payments*')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                            <span class="truncate">Monitor Transaksi</span>
                        </div>
                        <i
                            class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </a>
                </div>
            @else
                <!-- Multi-Tenant Team routes -->
                <div class="space-y-1">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest px-3 block mb-1">Dasbor &
                        Informasi</span>
                    <a href="{{ route('dashboard') }}"
                        class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('dashboard')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-650 hover:text-emerald-600 hover:bg-slate-50 @endif">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i
                                class="fa-solid fa-house-chimney text-sm shrink-0 @if(request()->routeIs('dashboard')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                            <span class="truncate">Dasbor Tim</span>
                        </div>
                        <i
                            class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity pb-0.5"></i>
                    </a>

                    <a href="{{ route('announcements.index') }}"
                        class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('announcements*')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-650 hover:text-emerald-600 hover:bg-slate-50 @endif">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i
                                class="fa-solid fa-bullhorn text-sm shrink-0 @if(request()->routeIs('announcements*')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                            <span class="truncate">Papan Pengumuman</span>
                        </div>
                        <i
                            class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity pb-0.5"></i>
                    </a>
                </div>

                <div class="space-y-1">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest px-3 block mb-1">Taktik &
                        Hasil</span>
                    @if(Auth::user()->isCoach() && Auth::user()->team && Auth::user()->team->isPremium())
                    <a href="{{ route('tactics.index') }}"
                        class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('tactics*')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-655 hover:text-emerald-600 hover:bg-slate-50 @endif">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i
                                class="fa-solid fa-palette text-sm shrink-0 @if(request()->routeIs('tactics*')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                            <span class="truncate">Tactical Board</span>
                        </div>
                        <i
                            class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity pb-0.5"></i>
                    </a>
                    @endif

                    <a href="{{ route('matches.index') }}"
                        class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('matches*')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-655 hover:text-emerald-600 hover:bg-slate-50 @endif">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i
                                class="fa-solid fa-trophy text-sm shrink-0 @if(request()->routeIs('matches*')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                            <span class="truncate">Hasil & Statistik</span>
                        </div>
                        <i
                            class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity pb-0.5"></i>
                    </a>
                </div>

                <div class="space-y-1">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest px-3 block mb-1">Operasional
                        Klub</span>
                    <a href="{{ route('schedules.index') }}"
                        class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('schedules*')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-655 hover:text-emerald-600 hover:bg-slate-50 @endif">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i
                                class="fa-solid fa-calendar-days text-sm shrink-0 @if(request()->routeIs('schedules*')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                            <span class="truncate">Jadwal & Absensi</span>
                        </div>
                        <i
                            class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity pb-0.5"></i>
                    </a>

                    <a href="{{ route('tasks.index') }}"
                        class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('tasks*')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-655 hover:text-emerald-600 hover:bg-slate-50 @endif">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i
                                class="fa-solid fa-list-check text-sm shrink-0 @if(request()->routeIs('tasks*')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                            <span class="truncate">Tugas Pemain</span>
                        </div>
                        <i
                            class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity pb-0.5"></i>
                    </a>

                    @if(Auth::user()->isManagement())
                        <a href="{{ route('players.index') }}"
                            class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('players*')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-655 hover:text-emerald-600 hover:bg-slate-50 @endif">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <i
                                    class="fa-solid fa-user-group text-sm shrink-0 @if(request()->routeIs('players*')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                                <span class="truncate">Manajemen Pemain</span>
                            </div>
                            <i
                                class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity pb-0.5"></i>
                        </a>

                        <a href="{{ route('coaches.index') }}"
                            class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('coaches*')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-655 hover:text-emerald-600 hover:bg-slate-50 @endif">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <i
                                    class="fa-solid fa-user-tie text-sm shrink-0 @if(request()->routeIs('coaches*')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                                <span class="truncate">Manajemen Pelatih</span>
                            </div>
                            <i
                                class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity pb-0.5"></i>
                        </a>
                    @endif

                    @if(!Auth::user()->isPlayer())
                        <a href="{{ route('finances.index') }}"
                            class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('finances*')) bg-emerald-50 border border-emerald-100/50 text-emerald-700 font-extrabold shadow-sm @else text-slate-655 hover:text-emerald-600 hover:bg-slate-50 @endif">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <i
                                    class="fa-solid fa-wallet text-sm shrink-0 @if(request()->routeIs('finances*')) text-emerald-600 @else text-slate-400 group-hover:text-emerald-500 @endif"></i>
                                <span class="truncate">Kas Keuangan</span>
                            </div>
                            <i
                                class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity pb-0.5"></i>
                        </a>
                    @endif
                </div>

            @endif

            <!-- Settings Menu -->
            <div class="space-y-1 pt-2 border-t border-slate-100/70">
                <button type="button" onclick="toggleSettingsMenu()"
                    class="w-full group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 text-slate-655 hover:text-emerald-600 hover:bg-slate-50 font-bold focus:outline-none">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <i class="fa-solid fa-gear text-sm text-slate-400 group-hover:text-emerald-500"></i>
                        <span>Pengaturan</span>
                    </div>
                    <i id="settingsToggleIcon"
                        class="fa-solid fa-chevron-right text-[8px] transition-transform duration-200"></i>
                </button>
                <div id="settingsSubmenu" class="hidden pl-6 space-y-1 mt-1">
                    @if(Auth::user()->isSuperAdmin())
                        <a href="{{ route('superadmin.settings.profile') }}"
                            class="group flex items-center justify-between px-3 py-1.5 rounded-lg text-[11px] transition-all duration-200 @if(request()->routeIs('superadmin.settings.profile')) text-emerald-700 font-extrabold @else text-slate-550 hover:text-emerald-600 @endif">
                            <span>Pengaturan Profile</span>
                        </a>
                        <a href="{{ route('superadmin.settings.website') }}"
                            class="group flex items-center justify-between px-3 py-1.5 rounded-lg text-[11px] transition-all duration-200 @if(request()->routeIs('superadmin.settings.website')) text-emerald-700 font-extrabold @else text-slate-550 hover:text-emerald-600 @endif">
                            <span>Pengaturan Website</span>
                        </a>
                        <a href="{{ route('superadmin.settings.landing') }}"
                            class="group flex items-center justify-between px-3 py-1.5 rounded-lg text-[11px] transition-all duration-200 @if(request()->routeIs('superadmin.settings.landing')) text-emerald-700 font-extrabold @else text-slate-550 hover:text-emerald-600 @endif">
                            <span>Pengaturan Landing Page</span>
                        </a>
                    @else
                        <a href="{{ route('settings.profile') }}"
                            class="group flex items-center justify-between px-3 py-1.5 rounded-lg text-[11px] transition-all duration-200 @if(request()->routeIs('settings.profile')) text-emerald-700 font-extrabold @else text-slate-550 hover:text-emerald-600 @endif">
                            <span>Pengaturan Profile</span>
                        </a>
                        @if(Auth::user()->isManagement())
                            <a href="{{ route('settings.team') }}"
                                class="group flex items-center justify-between px-3 py-1.5 rounded-lg text-[11px] transition-all duration-200 @if(request()->routeIs('settings.team')) text-emerald-700 font-extrabold @else text-slate-550 hover:text-emerald-600 @endif">
                                <span>Pengaturan Tim</span>
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </nav>

        <!-- Bottom Fixed Area (Services & Logout) -->
        <div class="p-3 border-t border-slate-100 space-y-2.5 bg-gradient-to-b from-white to-slate-50/50 shrink-0">
            @if(Auth::user()->isManagement())
                <div class="space-y-1">
                    <a href="{{ route('subscription.upgrade') }}"
                        class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all text-xs duration-200 @if(request()->routeIs('subscription*')) bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-extrabold shadow-md shadow-emerald-500/10 @else text-slate-655 hover:text-emerald-600 hover:bg-slate-50 @endif">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i
                                class="fa-solid fa-gem text-sm shrink-0 @if(request()->routeIs('subscription*')) text-white @else text-emerald-500 group-hover:scale-110 transition-transform @endif"></i>
                            <span class="truncate font-bold">Upgrade Premium</span>
                        </div>
                        @if(Auth::user()->team && Auth::user()->team->isPremium())
                            <span
                                class="text-[7px] font-bold bg-white text-emerald-600 px-1 py-0.5 rounded leading-none shrink-0 shadow-sm font-black">AKTIF</span>
                        @else
                            <i
                                class="fa-solid fa-chevron-right text-[8px] opacity-0 group-hover:opacity-100 transition-opacity pb-0.5"></i>
                        @endif
                    </a>
                </div>
            @endif

            <form action="{{ route('logout') }}" method="POST" class="confirm-logout">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-2.5 px-3 py-2 text-red-500 hover:text-red-650 hover:bg-red-50 rounded-lg transition-all text-xs font-bold focus:outline-none">
                    <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    <span>Keluar Aplikasi</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 max-w-full lg:h-screen lg:overflow-y-auto">
        <!-- Header Nav -->
        <header
            class="header-white h-[60px] min-h-[60px] shrink-0 px-6 md:px-8 flex items-center justify-between sticky top-0 z-30">
            <div>
                <h1 class="text-base md:text-xl font-extrabold text-slate-900 tracking-tight">
                    @yield('header_title', 'Halaman Utama')
                </h1>
                @if(!Auth::user()->isSuperAdmin() && Auth::user()->team)
                    <div class="text-[10px] md:text-xs text-slate-500 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-shield text-emerald-600 mr-1"></i>
                        <span>Tim Futsal: <strong>{{ Auth::user()->team->name }}</strong></span>
                    </div>
                @endif
            </div>

            <!-- Top Nav Utilities -->
            <div class="flex items-center gap-4">
                <!-- Current Time Info -->
                <div
                    class="hidden lg:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold shadow-sm">
                    <i class="fa-regular fa-clock text-emerald-600"></i>
                    <span id="currentTime">Indo Time</span>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-1 p-3.5 sm:p-5 md:p-6 relative">
            <!-- Flash Session Alerts -->
            @if(session('success'))
                <div
                    class="flash-alert mb-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs flex items-center gap-2.5 shadow-sm transition-all duration-500 overflow-hidden">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div
                    class="flash-alert mb-4 p-3.5 rounded-xl bg-red-50 border border-red-100 text-red-700 text-xs flex items-center gap-2.5 shadow-sm transition-all duration-500 overflow-hidden">
                    <i class="fa-solid fa-circle-exclamation text-sm"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @if(session('login_success_popup'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '<span style="font-size: 18px; font-weight: 800; color: #0f172a;"><i class="fa-solid fa-circle-check text-emerald-500 mr-2"></i>Login Berhasil!</span>',
                    html: `<p style="font-size: 13px; color: #475569; line-height: 1.5; margin: 5px 0 0;">{{ session('login_success_popup') }}</p>`,
                    confirmButtonText: 'Mulai Kelola',
                    confirmButtonColor: '#10b981',
                    customClass: {
                        popup: 'rounded-3xl',
                        confirmButton: 'rounded-xl text-xs uppercase tracking-wider font-bold py-3 px-6'
                    }
                });
            });
        </script>
    @endif

    @if(session('register_success_popup'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '<span style="font-size: 18px; font-weight: 800; color: #0f172a;"><i class="fa-solid fa-circle-check text-emerald-500 mr-2"></i>Pendaftaran Berhasil!</span>',
                    html: `<p style="font-size: 13px; color: #475569; line-height: 1.5; margin: 5px 0 0;">{{ session('register_success_popup') }}</p>`,
                    confirmButtonText: 'Masuk ke Dasbor',
                    confirmButtonColor: '#10b981',
                    customClass: {
                        popup: 'rounded-3xl',
                        confirmButton: 'rounded-xl text-xs uppercase tracking-wider font-bold py-3 px-6'
                    }
                });
            });
        </script>
    @endif

    @yield('scripts')
</body>

</html>