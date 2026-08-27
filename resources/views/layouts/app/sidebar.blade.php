<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <flux:sidebar.nav>
            {{-- PLATFORM GROUP --}}
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'petugas' || auth()->user()->role === 'owner')
                    <flux:sidebar.item icon="arrows-right-left" :href="route('request.approval')"
                        :current="request()->routeIs(['request.approval', 'asset-loans.index'])" wire:navigate>
                        Sirkulasi
                    </flux:sidebar.item>
                @endif

                @if(in_array(auth()->user()->role, ['siswa', 'guru', 'staff']))
                    <flux:sidebar.item icon="plus-circle" :href="route('user.request')"
                        :current="request()->routeIs('user.request')" wire:navigate>
                        {{ __('Buat Permintaan') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clock" :href="route('request.history')"
                        :current="request()->routeIs('request.history')" wire:navigate>
                        {{ __('Riwayat Permintaan') }}
                    </flux:sidebar.item>
                @endif

                {{-- MENU BARU: PENGAJUAN & PEMELIHARAAN (Bisa diakses semua user/guru/staff/admin) --}}
                <flux:sidebar.item icon="clipboard-document-list" :href="route('sarpras.requests')"
                    :current="request()->routeIs('sarpras.requests')" wire:navigate>
                    Pengajuan & Perbaikan
                </flux:sidebar.item>
            </flux:sidebar.group>

            {{-- MANAJEMEN INVENTARIS --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'petugas' || auth()->user()->role === 'owner')
                <flux:sidebar.group :heading="__('Sarana Prasarana')" class="grid mt-4">
                    {{-- BARANG HABIS PAKAI (BHP) --}}
                    <flux:sidebar.item icon="archive-box" :href="route('items.index')"
                        :current="request()->routeIs(['items.*', 'items-in.*', 'report.*'])" wire:navigate>
                        Barang Habis Pakai
                    </flux:sidebar.item>

                    {{-- BARANG ASET --}}
                    <flux:sidebar.item icon="rectangle-stack" :href="route('asset-master.index')"
                        :current="request()->routeIs(['asset-master.*', 'asset-registration.*', 'categories.*', 'rooms.*', 'asset-report'])"
                        wire:navigate>
                        Manajemen Aset
                    </flux:sidebar.item>
                </flux:sidebar.group>

                {{-- DATA MASTER (Admin Only) --}}
                @if(auth()->user()->role === 'admin')
                    <flux:sidebar.group :heading="__('Sistem')" class="grid mt-4">
                        <flux:sidebar.item icon="users" :href="route('users.index')"
                            :current="request()->routeIs(['users.*', 'students.*', 'staff.*', 'classes.*', 'prodis.*'])"
                            wire:navigate>
                            Data Master
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            @endif

            {{-- MODUL ABSENSI (Terbuka untuk Admin, Kesiswaan, Walikelas, Piket) --}}
            @if(in_array(auth()->user()->role, ['admin', 'kesiswaan', 'walikelas', 'piket']))
                <flux:sidebar.group :heading="__('Absensi')" class="grid mt-4">
                    {{-- Navigasi Landing Page Dinamis Menghindari Akses 403 --}}
                    @php
                        $attendanceRoute = 'attendance.monitor';
                        if (auth()->user()->role === 'walikelas')
                            $attendanceRoute = 'attendance.class';
                        if (auth()->user()->role === 'piket')
                            $attendanceRoute = 'attendance.piket';
                    @endphp

                    <flux:sidebar.item icon="finger-print" :href="route($attendanceRoute)"
                        :current="request()->routeIs(['attendance.*'])" wire:navigate>
                        Manajemen Absensi
                    </flux:sidebar.item>

                    <!-- <flux:sidebar.item icon="computer-desktop" :href="route('attendance.gateway')" target="_blank">
                                                    Buka Kios Absen
                                                </flux:sidebar.item> -->
                </flux:sidebar.group>
            @endif
        </flux:sidebar.nav>

        <flux:spacer />
        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <flux:header class="bg-white lg:bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        {{-- NAVBAR MOBILE --}}
        <flux:navbar class="lg:hidden w-full">
            <flux:sidebar.toggle icon="bars-2" inset="left" />
            <flux:spacer />
            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
                <flux:menu>
                    <div class="px-2 py-1.5 text-sm italic">
                        <p>{{ auth()->user()->email }}</p>
                    </div>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Settings</flux:menu.item>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle">Log out
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:navbar>

        @if(request()->routeIs(['dashboard.absen', 'dashboard', 'dashboard-asset']))
            <flux:navbar scrollable class="-mb-px px-4 lg:px-8">
                <flux:navbar.item icon="chart-bar" :href="route('dashboard.absen')"
                    :current="request()->routeIs('dashboard.absen')" wire:navigate>
                    Dashboard Absensi
                </flux:navbar.item>

                <flux:navbar.item icon="archive-box" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    Dashboard BHP
                </flux:navbar.item>
                <flux:navbar.item icon="rectangle-stack" :href="route('dashboard-asset')"
                    :current="request()->routeIs('dashboard-asset')" wire:navigate>
                    Dashboard Aset
                </flux:navbar.item>
            </flux:navbar>
        @endif

        {{-- SUB-NAVBAR: SIRKULASI --}}
        @if(request()->routeIs(['request.approval', 'asset-loans.index']))
            <flux:navbar scrollable class="-mb-px px-4 lg:px-8">
                <flux:navbar.item icon="check-badge" :href="route('request.approval')"
                    :current="request()->routeIs('request.approval')" wire:navigate>Persetujuan BHP

                    @php $count = \App\Models\RequestModel::where('status', 'pending')->count(); @endphp
                    @if($count > 0)
                        <flux:badge color="red" size="sm" class="ml-auto" inset="right">{{ $count }}</flux:badge>
                    @endif
                </flux:navbar.item>
                <flux:navbar.item icon="rectangle-stack" :href="route('asset-loans.index')"
                    :current="request()->routeIs('asset-loans.index')" wire:navigate>Peminjaman Aset</flux:navbar.item>
            </flux:navbar>
        @endif

        {{-- SUB-NAVBAR: PENGAJUAN & PEMELIHARAAN ASET & LAPORAN --}}
        @if(request()->routeIs(['sarpras.requests', 'sarpras.reports']))
            <flux:navbar scrollable class="-mb-px px-4 lg:px-8">
                <flux:navbar.item icon="clipboard-document-list" :href="route('sarpras.requests')"
                    :current="request()->routeIs('sarpras.requests')" wire:navigate>
                    Daftar Pengajuan & Perbaikan
                </flux:navbar.item>

                @if(in_array(auth()->user()->role, ['admin', 'petugas', 'owner']))
                    <flux:navbar.item icon="document-chart-bar" :href="route('sarpras.reports')"
                        :current="request()->routeIs('sarpras.reports')" wire:navigate>
                        Laporan Pengajuan
                    </flux:navbar.item>
                @endif
            </flux:navbar>
        @endif

        {{-- SUB-NAVBAR: BARANG HABIS PAKAI (BHP) --}}
        @if(request()->routeIs(['items.*', 'items-in.*', 'report.*']))
            <flux:navbar scrollable class="-mb-px px-4 lg:px-8">
                <flux:navbar.item icon="cube" :href="route('items.index')" :current="request()->routeIs('items.index')"
                    wire:navigate>Kelola BHP</flux:navbar.item>
                <flux:navbar.item icon="inbox-arrow-down" :href="route('items-in.index')"
                    :current="request()->routeIs('items-in.index')" wire:navigate>Barang Masuk</flux:navbar.item>
                <flux:navbar.item icon="chart-pie" :href="route('report')" :current="request()->routeIs('report')"
                    wire:navigate>Laporan BHP</flux:navbar.item>
            </flux:navbar>
        @endif

        {{-- SUB-NAVBAR: BARANG ASET --}}
        @if(request()->routeIs(['asset-master.*', 'asset-registration.*', 'categories.*', 'rooms.*', 'asset-report', 'admin.assets', 'assets.timeline.*']))
            <flux:navbar scrollable class="-mb-px px-4 lg:px-8">
                <flux:navbar.item icon="rectangle-stack" :href="route('asset-master.index')"
                    :current="request()->routeIs('asset-master.index')" wire:navigate>Katalog Aset</flux:navbar.item>
                <flux:navbar.item icon="tag" :href="route('categories.index')"
                    :current="request()->routeIs('categories.index')" wire:navigate>Kategori</flux:navbar.item>
                <flux:navbar.item icon="building-office-2" :href="route('rooms.index')"
                    :current="request()->routeIs('rooms.index')" wire:navigate>Ruangan</flux:navbar.item>
                <flux:navbar.item icon="document-plus" :href="route('asset-registration.index')"
                    :current="request()->routeIs('asset-registration.index')" wire:navigate>Registrasi Unit
                </flux:navbar.item>
                <flux:navbar.item icon="chart-bar" :href="route('asset-report')"
                    :current="request()->routeIs('asset-report')" wire:navigate>Rekap Aset</flux:navbar.item>
                <flux:navbar.item icon="computer-desktop" :href="route('admin.assets')"
                    :current="request()->routeIs('admin.assets')" wire:navigate>Monitoring Unit</flux:navbar.item>
                <flux:navbar.item icon="clock" :href="route('assets.timeline.index')"
                    :current="request()->routeIs('assets.timeline.*')" wire:navigate>Riwayat Siklus Aset</flux:navbar.item>
            </flux:navbar>
        @endif

        {{-- SUB-NAVBAR: DATA MASTER --}}
        @if(request()->routeIs(['users.*', 'students.*', 'staff.*', 'classes.*', 'prodis.*', 'schedules.*', 'holiday.*']))
            <flux:navbar scrollable class="-mb-px px-4 lg:px-8">
                <flux:navbar.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.index')"
                    wire:navigate>User Login</flux:navbar.item>
                <flux:navbar.item icon="user-plus" :href="route('staff.index')" :current="request()->routeIs('staff.index')"
                    wire:navigate>Guru & Staff</flux:navbar.item>
                <flux:navbar.item icon="user-group" :href="route('students.index')"
                    :current="request()->routeIs('students.index')" wire:navigate>Siswa</flux:navbar.item>
                <flux:navbar.item icon="academic-cap" :href="route('classes.index')"
                    :current="request()->routeIs('classes.index')" wire:navigate>Kelas</flux:navbar.item>
                <flux:navbar.item icon="briefcase" :href="route('prodis.index')"
                    :current="request()->routeIs('prodis.index')" wire:navigate>Prodi</flux:navbar.item>
                <flux:navbar.item icon="calendar-days" :href="route('schedules.index')"
                    :current="request()->routeIs('schedules.w*')" wire:navigate>
                    Jadwal Sekolah
                </flux:navbar.item>
                <flux:navbar.item icon="calendar-days" :href="route('holiday.index')"
                    :current="request()->routeIs('holiday.index')" wire:navigate>
                    Hari Libur
                </flux:navbar.item>
            </flux:navbar>
        @endif

        {{-- SUB-NAVBAR: ABSENSI (SEKARANG SUDAH DI SINI) --}}
        @if(request()->routeIs(['attendance.*']))
            <flux:navbar scrollable class="-mb-px px-4 lg:px-8">
                {{-- Akses Kesiswaan & Admin --}}
                @if(in_array(auth()->user()->role, ['admin', 'kesiswaan']))
                    <flux:navbar.item icon="chart-bar" :href="route('attendance.monitor')"
                        :current="request()->routeIs('attendance.monitor')" wire:navigate>
                        Monitoring Real-time
                    </flux:navbar.item>
                    <flux:navbar.item icon="document-text" :href="route('attendance.report')"
                        :current="request()->routeIs('attendance.report')" wire:navigate>
                        Rekap Absensi
                    </flux:navbar.item>
                @endif

                {{-- Akses Wali Kelas --}}
                @if(auth()->user()->role === 'walikelas' || auth()->user()->role === 'admin')
                    <flux:navbar.item icon="users" :href="route('attendance.class')"
                        :current="request()->routeIs('attendance.class')" wire:navigate>
                        Absensi Kelas
                    </flux:navbar.item>

                    {{-- TAMBAHKAN MENU INI --}}
                    <flux:navbar.item icon="document-chart-bar" :href="route('attendance.recap.class')"
                        :current="request()->routeIs('attendance.recap.class')" wire:navigate>
                        Rekap Absensi Kelas
                    </flux:navbar.item>
                @endif

                {{-- Akses Petugas Piket --}}
                @if(auth()->user()->role === 'piket' || auth()->user()->role === 'admin')
                    <flux:navbar.item icon="pencil-square" :href="route('attendance.piket')"
                        :current="request()->routeIs('attendance.piket')" wire:navigate>
                        Input Manual/Piket
                    </flux:navbar.item>
                @endif
            </flux:navbar>
        @endif
    </flux:header>

    {{ $slot }}

    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist

    @fluxScripts
    <script src="{{ asset('js/printer.js') }}?v={{ time() }}"></script>
</body>

</html>