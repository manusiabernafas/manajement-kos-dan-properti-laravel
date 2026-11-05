<header class="navbar">
    <div class="navbar-left">
        <button type="button" class="navbar-menu-btn" data-sidebar-mobile-toggle>
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="breadcrumb">
            <a href="{{ route('dashboard') }}" class="breadcrumb-item">
                <i class="fa-solid fa-house"></i>
            </a>
            @foreach($breadcrumbs as $crumb)
                <span class="breadcrumb-sep">/</span>
                @if(!$loop->last && !empty($crumb['url']))
                    <a href="{{ $crumb['url'] }}" class="breadcrumb-item">{{ $crumb['label'] }}</a>
                @else
                    <span class="breadcrumb-item current">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
            @if(empty($breadcrumbs))
                <span class="breadcrumb-item current">{{ $title }}</span>
            @endif
        </div>
    </div>

    <div class="navbar-right">
        @unless(auth()->user()->hasRole('Tenant'))
            <form action="{{ route('search') }}" method="GET" class="navbar-search">
                <span class="icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="q" placeholder="Cari penghuni, kamar, invoice..." value="{{ request('q') }}" autocomplete="off">
            </form>
        @endunless

        <div class="theme-switcher">
            <button type="button" data-theme-btn="light" title="Light"><i class="fa-solid fa-sun"></i></button>
            <button type="button" data-theme-btn="dark" title="Dark"><i class="fa-solid fa-moon"></i></button>
            <button type="button" data-theme-btn="system" title="System"><i class="fa-solid fa-desktop"></i></button>
        </div>

        <div class="dropdown">
            <button type="button" class="icon-btn" data-dropdown-toggle>
                <i class="fa-regular fa-bell"></i>
                @if($unreadCount > 0)
                    <span class="badge-dot"></span>
                @endif
            </button>
            <div class="dropdown-menu" style="width: 320px;">
                <div class="dropdown-header" style="display:flex;justify-content:space-between;align-items:center;">
                    <span>Notifikasi</span>
                    @if($unreadCount > 0)
                        <form action="{{ route('notifications.read-all') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-ghost btn-sm" style="padding:2px 6px;">Tandai semua dibaca</button>
                        </form>
                    @endif
                </div>
                @forelse($notifications as $notif)
                    <a href="{{ route('notifications.show', $notif->id) }}" class="notif-item {{ $notif->read_at ? '' : 'unread' }}">
                        <div class="notif-icon"><i class="fa-solid {{ $notif->data['icon'] ?? 'fa-circle-info' }}"></i></div>
                        <div class="notif-body">
                            <div class="notif-title">{{ $notif->data['title'] ?? 'Notifikasi' }}</div>
                            <div class="notif-desc">{{ $notif->data['message'] ?? '' }}</div>
                            <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                @empty
                    <div style="padding:24px 12px;text-align:center;color:var(--color-muted);font-size:12.5px;">Belum ada notifikasi</div>
                @endforelse
            </div>
        </div>

        <div class="dropdown">
            <div class="navbar-profile" data-dropdown-toggle>
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                <div class="navbar-profile-info">
                    <span class="navbar-profile-name">{{ auth()->user()->name }}</span>
                    <span class="navbar-profile-role">{{ auth()->user()->getRoleNames()->first() ?? '-' }}</span>
                </div>
                <i class="fa-solid fa-chevron-down text-xs text-muted"></i>
            </div>
            <div class="dropdown-menu">
                <a href="{{ route('profile.edit') }}" class="dropdown-item"><i class="fa-regular fa-user"></i> Profil Saya</a>
                <a href="{{ route('profile.password') }}" class="dropdown-item"><i class="fa-solid fa-key"></i> Ganti Password</a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item danger"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</button>
                </form>
            </div>
        </div>
    </div>
</header>
