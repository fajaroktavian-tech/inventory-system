<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky collapsible="mobile"
        class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        {{-- Gunakan satu flux:sidebar.nav saja --}}
        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid">
                @if(in_array(auth()->user()->role, ['admin', 'petugas', 'owner', 'siswa', 'guru', 'staff']))
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                        wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                @endif

                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'petugas' || auth()->user()->role === 'owner')
                    <flux:sidebar.item icon="check-badge" :href="route('request.approval')"
                        :current="request()->routeIs('request.approval')" wire:navigate>
                        Persetujuan
                        @php
                            $count = \App\Models\RequestModel::where('status', 'pending')->count();
                        @endphp
                        @if($count > 0)
                            <flux:badge color="red" size="sm" class="ml-auto">{{ $count }}</flux:badge>
                        @endif
                    </flux:sidebar.item>
                @endif

                {{-- MENU BARU: Buat Permintaan (Untuk Siswa & Guru yang Login) --}}
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

                {{-- Kios Permintaan: Shortcut untuk Admin/Petugas --}}
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'petugas')
                    <flux:sidebar.item icon="identification" :href="route('rfid.request')"
                        :current="request()->routeIs('rfid.request')" wire:navigate>
                        {{ __('Kios RFID (TU)') }}
                    </flux:sidebar.item>
                @endif
            </flux:sidebar.group>

            {{-- Group Master Data --}}
            @if(auth()->user()->role === 'admin' || auth()->user()->role === 'petugas' || auth()->user()->role === 'owner')
                <flux:sidebar.group :heading="__('Master Data')" class="grid mt-4">
                    @if(auth()->user()->role === 'admin')
                        <flux:sidebar.item icon="users" :href="route('users.index')"
                            :current="request()->routeIs('users.index')" wire:navigate>
                            {{ __('Kelola Pengguna') }}
                        </flux:sidebar.item>
                    @endif

                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'petugas')
                        <flux:sidebar.item icon="tag" :href="route('categories.index')"
                            :current="request()->routeIs('categories.index')" wire:navigate>
                            {{ __('Kelola Kategori') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="cube" :href="route('items.index')" :current="request()->routeIs('items.index')"
                            wire:navigate>
                            {{ __('Kelola Barang') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="inbox-arrow-down" :href="route('items-in.index')"
                            :current="request()->routeIs('items-in.index')" wire:navigate>
                            {{ __('Kelola Barang Masuk') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="academic-cap" :href="route('classes.index')"
                            :current="request()->routeIs('classes.index')" wire:navigate>
                            {{ __('Kelola Kelas') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="building-office-2" :href="route('rooms.index')"
                            :current="request()->routeIs('rooms.index')" wire:navigate>
                            {{ __('Kelola Ruangan') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="users" :href="route('students.index')"
                            :current="request()->routeIs('students.index')" wire:navigate>
                            {{ __('Kelola Siswa') }}
                        </flux:sidebar.item>
                    @endif

                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'owner'|| auth()->user()->role === 'petugas')
                        <flux:sidebar.item icon="chart-pie" :href="route('report')" :current="request()->routeIs('report')"
                            wire:navigate>
                            Laporan
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
            @endif
        </flux:sidebar.nav>

        <flux:spacer />

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:spacer />
        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
            <flux:menu>
                <div class="px-2 py-1.5 text-sm">
                    <p class="font-bold">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-zinc-500">{{ auth()->user()->email }}</p>
                </div>
                <flux:menu.separator />
                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>Settings</flux:menu.item>
                <flux:menu.separator />
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        Log out
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>