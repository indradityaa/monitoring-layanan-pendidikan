@php
    $currentUser = auth()->user();
    $role = $currentUser?->role;

    $navigationByRole = [
        'admin' => [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'active' => 'admin.dashboard',
            ],
            [
                'label' => 'Kegiatan',
                'route' => 'admin.activities.index',
                'active' => 'admin.activities.*',
            ],
            [
                'label' => 'Jadwal',
                'route' => 'admin.schedules.index',
                'active' => 'admin.schedules.*',
            ],
            [
                'label' => 'Penugasan',
                'route' => 'admin.assignments.index',
                'active' => 'admin.assignments.*',
            ],
            [
                'label' => 'Laporan',
                'route' => 'admin.reports.index',
                'active' => 'admin.reports.*',
            ],
            [
                'label' => 'Jenis Kegiatan',
                'route' => 'admin.master.activity-types.index',
                'active' => 'admin.master.activity-types.*',
            ],
            [
                'label' => 'Lokasi',
                'route' => 'admin.master.locations.index',
                'active' => 'admin.master.locations.*',
            ],
        ],

        'petugas' => [
            [
                'label' => 'Dashboard',
                'route' => 'petugas.dashboard',
                'active' => 'petugas.dashboard',
            ],
            ['label' => 'Jadwal Saya', 'route' => null],
            ['label' => 'Kegiatan Saya', 'route' => null],
            ['label' => 'Pelaksanaan', 'route' => null],
            ['label' => 'Peserta', 'route' => null],
            [
                'label' => 'Laporan Kegiatan',
                'route' => 'petugas.reports.index',
                'active' => 'petugas.reports.*',
            ],
        ],

        'pimpinan' => [
            [
                'label' => 'Dashboard',
                'route' => 'pimpinan.dashboard',
                'active' => 'pimpinan.dashboard',
            ],
            ['label' => 'Monitoring Kegiatan', 'route' => null],
            ['label' => 'Jadwal Kegiatan', 'route' => null],
            ['label' => 'Laporan', 'route' => null],
        ],
    ];

    $navItems = $navigationByRole[$role] ?? [];
@endphp

<aside class="app-sidebar">

    <div class="app-sidebar-brand">
        <h1 class="app-sidebar-brand-title">
            Monitoring
        </h1>

        <p class="app-sidebar-brand-subtitle">
            Layanan Pendidikan
        </p>
    </div>

    <nav class="app-sidebar-nav">
        <p class="app-sidebar-section-label">
            MENU UTAMA
        </p>

        @forelse ($navItems as $item)

            @if (
                    !empty($item['route']) &&
                    \Illuminate\Support\Facades\Route::has($item['route'])
                )

                @php
                    $isActive = request()->routeIs(
                        $item['active'] ?? $item['route']
                    );
                @endphp

                <a href="{{ route($item['route']) }}" @if ($isActive) aria-current="page" @endif
                    class="app-nav-link {{ $isActive ? 'app-nav-link-active' : '' }}">
                    <span>{{ $item['label'] }}</span>
                </a>

            @else

                <div class="app-nav-disabled" aria-disabled="true" title="Halaman belum tersedia">
                    <span>{{ $item['label'] }}</span>
                    <span class="app-nav-soon">Segera</span>
                </div>

            @endif

        @empty

            <p class="app-sidebar-brand-subtitle">
                Menu belum tersedia.
            </p>

        @endforelse
    </nav>

    <div class="app-sidebar-footer">
        <div class="app-sidebar-user-name">
            {{ $currentUser?->name ?? 'Pengguna' }}
        </div>

        <div class="app-sidebar-user-role">
            {{ ucfirst($role ?? 'unknown') }}
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit" class="app-logout-button">
                Keluar
            </button>
        </form>
    </div>

</aside>