<header class="app-topbar">

    <div>
        <h2 class="app-page-title">
            @yield('page-title', 'Dashboard')
        </h2>

        <p class="app-page-subtitle">
            Sistem Informasi Monitoring dan Pelaporan
            Kegiatan Layanan Pendidikan
        </p>
    </div>

    <div class="app-user">
        <div class="app-avatar" aria-hidden="true">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>

        <div>
            <div class="app-user-name">
                {{ auth()->user()->name ?? 'Pengguna' }}
            </div>

            <div class="app-user-role">
                {{ ucfirst(auth()->user()->role ?? 'unknown') }}
            </div>
        </div>
    </div>

</header>