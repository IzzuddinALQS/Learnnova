<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    {{-- Kiri: toggle sidebar + breadcrumb app --}}
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link font-weight-bold">
                <i class="fas fa-graduation-cap mr-1 text-primary"></i>E-Learning
            </a>
        </li>
    </ul>

    {{-- Kanan: notifikasi + user dropdown + fullscreen + logout --}}
    <ul class="navbar-nav ml-auto">

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown" id="notification-dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false" title="Notifikasi">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge" id="notification-badge" style="display: none; font-size: 0.6rem; padding: 2px 4px;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="min-width: 320px; max-width: 400px; border-radius: 8px;">
                <span class="dropdown-item dropdown-header" id="notification-dropdown-header">0 Notifikasi</span>
                <div class="dropdown-divider"></div>
                <div id="notification-items-container" style="max-height: 280px; overflow-y: auto;">
                    <div class="text-center p-3 text-muted" id="notification-empty-state">
                        <i class="far fa-bell-slash mb-2" style="font-size: 1.5rem;"></i>
                        <p class="mb-0 text-sm">Tidak ada notifikasi baru</p>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <a href="javascript:void(0)" class="dropdown-item dropdown-footer text-center text-primary font-weight-bold" id="mark-all-read-btn">
                    Tandai semua telah dibaca
                </a>
            </div>
        </li>

        {{-- Fullscreen --}}
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Fullscreen">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        {{-- User dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
               data-toggle="dropdown" role="button" style="gap:8px">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                         class="rounded-circle"
                         style="width:28px;height:28px;object-fit:cover;flex-shrink:0;"
                         alt="User Avatar">
                @else
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                         style="width:28px;height:28px;font-size:.8rem;flex-shrink:0;
                                background:#3490dc;font-weight:600">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <span class="d-none d-md-inline">{{ auth()->user()->name ?? 'User' }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-item-text">
                    <div class="font-weight-bold">{{ auth()->user()->name ?? '-' }}</div>
                    <small class="text-muted">{{ auth()->user()->email ?? '' }}</small>
                    <div class="mt-1">
                        <span class="badge badge-primary">
                            {{ auth()->user()->roles->first()->name ?? 'no role' }}
                        </span>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </button>
                </form>
            </div>
        </li>

    </ul>
</nav>
