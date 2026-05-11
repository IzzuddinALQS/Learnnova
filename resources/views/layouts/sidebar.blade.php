<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="E-Learning Logo"
             class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">E-Learning</span>
    </a>

    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('dist/img/user2-160x160.jpg') }}"
                     class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name ?? 'Guest' }}</a>
                @auth
                <small style="color:#adb5bd">
                    {{ auth()->user()->roles->first()->name ?? '' }}
                </small>
                @endauth
            </div>
        </div>

        <!-- Sidebar Search -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search"
                       placeholder="Cari menu..." aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column"
                data-widget="treeview" role="menu" data-accordion="false">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- MASTER DATA — super_admin & akademik --}}
                @if(auth()->check() && (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('akademik')))
                <li class="nav-header">MASTER DATA</li>

<<<<<<< HEAD
                <!-- Syllabus -->
                <li class="nav-item">
                    <a href="{{ url('/syllabus') }}" class="nav-link {{ request()->is('syllabus*') ? 'active' : '' }}">
=======
                <li class="nav-item">
                    <a href="{{ url('/syllabus') }}"
                       class="nav-link {{ request()->is('syllabus*') ? 'active' : '' }}">
>>>>>>> 5d8d10b622e562f93524fce1b6e38614ab17d922
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>Syllabus</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('courses.index') }}"
                       class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>Kelas</p>
                    </a>
                </li>
                @endif

                {{-- KELAS SAYA — pengajar & pelajar --}}
                @if(auth()->check() && (auth()->user()->hasRole('pengajar') || auth()->user()->hasRole('pelajar')))
                <li class="nav-header">KELAS SAYA</li>

                <li class="nav-item">
                    <a href="{{ route('courses.index') }}"
                       class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-{{ auth()->user()->hasRole('pengajar') ? 'chalkboard' : 'book-reader' }}"></i>
                        <p>{{ auth()->user()->hasRole('pengajar') ? 'Kelas yang Diajar' : 'Kelas yang Diikuti' }}</p>
                    </a>
                </li>
                @endif

                {{-- PEMBELAJARAN --}}
                <li class="nav-header">PEMBELAJARAN</li>

                @if(auth()->check() && auth()->user()->hasPermission('materials.view'))
                <li class="nav-item">
                    <a href="{{ route('courses.index') }}"
                       class="nav-link {{ request()->is('courses/*/materials*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>Materi</p>
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a href="{{ url('/assignments') }}"
                       class="nav-link {{ request()->is('assignments*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tasks"></i>
                        <p>Tugas</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/quizzes') }}"
                       class="nav-link {{ request()->is('quizzes*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>Quiz</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/schedules') }}"
                       class="nav-link {{ request()->is('schedules*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Jadwal & Absensi</p>
                    </a>
                </li>

                {{-- KOMUNITAS --}}
                <li class="nav-header">KOMUNITAS</li>

                @if(auth()->check() && auth()->user()->hasPermission('forum.view'))
                <li class="nav-item">
                    <a href="{{ route('forum.global') }}"
                       class="nav-link {{ request()->is('forum*') || request()->is('courses/*/forum*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-comments"></i>
                        <p>Forum Diskusi</p>
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a href="{{ url('/announcements') }}"
                       class="nav-link {{ request()->is('announcements*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bullhorn"></i>
                        <p>Pengumuman</p>
                    </a>
                </li>

                {{-- PERSONAL --}}
                <li class="nav-header">PERSONAL</li>

                <li class="nav-item">
                    <a href="{{ url('/certificates') }}"
                       class="nav-link {{ request()->is('certificates*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-certificate"></i>
                        <p>Sertifikat Saya</p>
                    </a>
                </li>

                {{-- ADMINISTRASI — super_admin --}}
                @if(auth()->check() && auth()->user()->hasRole('super_admin'))
                <li class="nav-header">ADMINISTRASI</li>

                <li class="nav-item">
                    <a href="{{ route('users.index') }}"
                       class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Manajemen User</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('roles.index') }}"
                       class="nav-link {{ request()->is('roles*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Roles & Permission</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ url('/activity-logs') }}"
                       class="nav-link {{ request()->is('activity-logs*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Activity Log</p>
                    </a>
                </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
