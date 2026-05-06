<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ asset('dist/img/AdminLTELogo.png') }}" alt="E-Learning Logo"
             class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">E-Learning</span>
    </a>

    <div class="sidebar">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name ?? 'Guest' }}</a>
            </div>
        </div>

        <!-- Sidebar Search -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Cari menu..." aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">AKADEMIK</li>

                <!-- Silabus -->
                <li class="nav-item">
                    <a href="{{ url('/syllabus') }}" class="nav-link {{ request()->is('syllabus*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>Silabus</p>
                    </a>
                </li>

                <!-- Kelas -->
                <li class="nav-item {{ request()->is('courses*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('courses*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>
                            Kelas
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/courses') }}" class="nav-link {{ request()->is('courses*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Semua Kelas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/courses/create') }}" class="nav-link {{ request()->is('courses/create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tambah Kelas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/enrollments') }}" class="nav-link {{ request()->is('enrollments*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pendaftaran</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Materi -->
                <li class="nav-item {{ request()->is('materials*', 'modules*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('materials*', 'modules*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>
                            Materi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/modules') }}" class="nav-link {{ request()->is('modules*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Modul</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/materials') }}" class="nav-link {{ request()->is('materials*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Materi Pembelajaran</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-header">AKTIVITAS</li>

                <!-- Tugas -->
                <li class="nav-item {{ request()->is('assignments*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('assignments*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tasks"></i>
                        <p>
                            Tugas
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/assignments') }}" class="nav-link {{ request()->is('assignments') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daftar Tugas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/assignments/submissions') }}" class="nav-link {{ request()->is('assignments/submissions*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Pengumpulan</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Quiz -->
                <li class="nav-item {{ request()->is('quizzes*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('quizzes*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>
                            Quiz
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/quizzes') }}" class="nav-link {{ request()->is('quizzes') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Daftar Quiz</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/quizzes/results') }}" class="nav-link {{ request()->is('quizzes/results*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Hasil Quiz</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Jadwal & Absensi -->
                <li class="nav-item {{ request()->is('schedules*', 'attendances*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('schedules*', 'attendances*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>
                            Jadwal & Absensi
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('/schedules') }}" class="nav-link {{ request()->is('schedules*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Jadwal</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/attendances') }}" class="nav-link {{ request()->is('attendances*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Absensi</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Forum -->
                @if(auth()->check() && auth()->user()->hasPermission('forum.view'))
                <li class="nav-item">
                    <a href="{{ route('courses.index') }}" class="nav-link {{ request()->is('courses/*/forum*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-comments"></i>
                        <p>Forum Diskusi</p>
                    </a>
                </li>
                @endif

                <li class="nav-header">INFORMASI</li>

                <!-- Pengumuman -->
                <li class="nav-item">
                    <a href="{{ url('/announcements') }}" class="nav-link {{ request()->is('announcements*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bullhorn"></i>
                        <p>Pengumuman</p>
                    </a>
                </li>

                <!-- Sertifikat -->
                <li class="nav-item">
                    <a href="{{ url('/certificates') }}" class="nav-link {{ request()->is('certificates*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-certificate"></i>
                        <p>Sertifikat</p>
                    </a>
                </li>

                <li class="nav-header">ADMINISTRASI</li>

                <!-- Manajemen User -->
                <li class="nav-item">
                    <a href="{{ url('/users') }}" class="nav-link {{ request()->is('users*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Manajemen User</p>
                    </a>
                </li>

                <!-- Roles & Permission -->
                <li class="nav-item">
                    <a href="{{ url('/roles') }}" class="nav-link {{ request()->is('roles*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-shield"></i>
                        <p>Roles & Permission</p>
                    </a>
                </li>
                <!-- Activity Log -->
                <li class="nav-item">
                    <a href="{{ url('/activity-logs') }}" class="nav-link {{ request()->is('activity-logs*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-history"></i>
                        <p>Activity Log</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
