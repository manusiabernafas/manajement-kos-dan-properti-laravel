<aside class="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <span class="sidebar-brand-icon"><i class="fa-solid fa-building-columns"></i></span>
        <span class="sidebar-brand-text">KostManager</span>
    </a>

    <nav class="sidebar-scroll">
        @foreach($menu as $section)
            <div class="sidebar-section">
                @if($section['title'])
                    <div class="sidebar-section-title">{{ $section['title'] }}</div>
                @endif

                @foreach($section['items'] as $item)
                    @php
                        $params = $item['params'] ?? [];
                        $isActive = request()->routeIs($item['route']) && (empty($params) || collect($params)->every(fn($v, $k) => request($k) == $v));
                    @endphp
                    <a href="{{ route($item['route'], $params) }}" class="sidebar-link {{ $isActive ? 'active' : '' }}">
                        <span class="sidebar-link-icon"><i class="{{ $item['icon'] }}"></i></span>
                        <span class="sidebar-link-text">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        @endforeach
    </nav>

    <div class="sidebar-toggle-collapse">
        <button type="button" data-sidebar-collapse-toggle>
            <i class="fa-solid fa-angles-left"></i>
        </button>
    </div>
</aside>
