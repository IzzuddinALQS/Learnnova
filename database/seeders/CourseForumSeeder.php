<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseForumSeeder extends Seeder
{
    /**
     * User IDs sesuai UserSeeder:
     * 1 = Super Admin
     * 2 = Staf Akademik
     * 3 = Pengajar
     * 4 = Pelajar
     */
    public function run(): void
    {
        $now = now();

        // ── 1. Courses ────────────────────────────────────────────────────
        $courses = [
            [
                'id'            => 1,
                'instructor_id' => 3, // Pengajar
                'title'         => 'Pemrograman Web dengan Laravel',
                'description'   => 'Belajar membangun aplikasi web modern menggunakan framework Laravel dari dasar hingga mahir.',
                'status'        => 'published',
                'duration_weeks'=> 12,
                'published_at'  => $now,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 2,
                'instructor_id' => 3, // Pengajar
                'title'         => 'Desain UI/UX dengan Figma',
                'description'   => 'Pelajari prinsip desain antarmuka dan pengalaman pengguna menggunakan Figma.',
                'status'        => 'published',
                'duration_weeks'=> 8,
                'published_at'  => $now,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'id'            => 3,
                'instructor_id' => 3, // Pengajar
                'title'         => 'Data Science dengan Python',
                'description'   => 'Pengenalan analisis data, visualisasi, dan machine learning menggunakan Python.',
                'status'        => 'published',
                'duration_weeks'=> 16,
                'published_at'  => $now,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        DB::table('courses')->insert($courses);

        // ── 2. Forum Threads ──────────────────────────────────────────────
        $threads = [
            // Kelas 1 - Laravel
            [
                'id'         => 1,
                'course_id'  => 1,
                'user_id'    => 3, // Pengajar
                'title'      => '📌 Panduan Belajar Kelas Ini — Baca Dulu!',
                'content'    => "Selamat datang di kelas Pemrograman Web dengan Laravel!\n\nBerikut panduan belajar untuk kelas ini:\n\n1. Pastikan PHP 8.2 dan Composer sudah terinstall\n2. Ikuti materi secara berurutan\n3. Kerjakan tugas sebelum deadline\n4. Gunakan forum ini untuk bertanya\n\nSelamat belajar! 🚀",
                'is_pinned'  => 1,
                'is_locked'  => 0,
                'views'      => 142,
                'created_at' => $now->copy()->subDays(14),
                'updated_at' => $now->copy()->subDays(14),
            ],
            [
                'id'         => 2,
                'course_id'  => 1,
                'user_id'    => 4, // Pelajar
                'title'      => 'Error saat menjalankan php artisan migrate',
                'content'    => "Halo, saya mendapat error berikut saat menjalankan migrate:\n\nIlluminate\\Database\\QueryException: SQLSTATE[HY000] [2002] Connection refused\n\nSudah coba berbagai cara tapi masih error. Ada yang bisa bantu?",
                'is_pinned'  => 0,
                'is_locked'  => 0,
                'views'      => 87,
                'created_at' => $now->copy()->subDays(7),
                'updated_at' => $now->copy()->subDays(6),
            ],
            [
                'id'         => 3,
                'course_id'  => 1,
                'user_id'    => 4, // Pelajar
                'title'      => 'Perbedaan belongsTo dan hasMany di Eloquent?',
                'content'    => "Saya masih bingung kapan harus pakai belongsTo dan kapan pakai hasMany.\n\nBisa tolong dijelaskan dengan contoh nyata? Terima kasih.",
                'is_pinned'  => 0,
                'is_locked'  => 0,
                'views'      => 63,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(4),
            ],
            [
                'id'         => 4,
                'course_id'  => 1,
                'user_id'    => 1, // Super Admin
                'title'      => 'Tips & Trik Blade Templating',
                'content'    => "Sharing beberapa tips Blade yang berguna:\n\n1. Gunakan @include untuk komponen yang berulang\n2. @stack dan @push untuk inject script per halaman\n3. @auth dan @guest untuk conditional rendering\n4. @forelse lebih baik dari @foreach untuk handle empty state\n\nAda tips lain? Share di sini!",
                'is_pinned'  => 0,
                'is_locked'  => 0,
                'views'      => 45,
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subDays(2),
            ],

            // Kelas 2 - UI/UX
            [
                'id'         => 5,
                'course_id'  => 2,
                'user_id'    => 3, // Pengajar
                'title'      => '📌 Selamat Datang di Kelas UI/UX!',
                'content'    => "Halo semua!\n\nDi kelas ini kita akan belajar:\n- Prinsip dasar desain UI/UX\n- Wireframing dan prototyping\n- Design system\n- User testing\n\nSilakan perkenalkan diri di thread ini!",
                'is_pinned'  => 1,
                'is_locked'  => 0,
                'views'      => 98,
                'created_at' => $now->copy()->subDays(10),
                'updated_at' => $now->copy()->subDays(10),
            ],
            [
                'id'         => 6,
                'course_id'  => 2,
                'user_id'    => 4, // Pelajar
                'title'      => 'Rekomendasi plugin Figma untuk pemula?',
                'content'    => "Halo, ada yang bisa rekomendasikan plugin Figma yang wajib dipasang untuk pemula?\n\nSaya baru mulai belajar Figma dan agak overwhelmed dengan banyaknya pilihan plugin.",
                'is_pinned'  => 0,
                'is_locked'  => 0,
                'views'      => 34,
                'created_at' => $now->copy()->subDays(4),
                'updated_at' => $now->copy()->subDays(3),
            ],

            // Kelas 3 - Data Science
            [
                'id'         => 7,
                'course_id'  => 3,
                'user_id'    => 3, // Pengajar
                'title'      => '📌 Persiapan Environment Python',
                'content'    => "Sebelum mulai kelas, pastikan environment sudah siap:\n\n1. Install Python 3.10+\n2. Install Anaconda atau virtualenv\n3. Install library: pandas, numpy, matplotlib, scikit-learn\n4. Coba jalankan: python --version\n\nJika ada masalah instalasi, tanyakan di sini.",
                'is_pinned'  => 1,
                'is_locked'  => 0,
                'views'      => 76,
                'created_at' => $now->copy()->subDays(12),
                'updated_at' => $now->copy()->subDays(12),
            ],
            [
                'id'         => 8,
                'course_id'  => 3,
                'user_id'    => 4, // Pelajar
                'title'      => 'Perbedaan supervised dan unsupervised learning?',
                'content'    => "Bisa tolong jelaskan perbedaan supervised learning dan unsupervised learning?\n\nDan kapan kita harus menggunakan masing-masing pendekatan?",
                'is_pinned'  => 0,
                'is_locked'  => 0,
                'views'      => 52,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(1),
            ],
        ];

        DB::table('forum_threads')->insert($threads);

        // ── 3. Forum Posts (replies) ──────────────────────────────────────
        $posts = [
            // Thread 2: Error migrate — ada solusi
            [
                'id'          => 1,
                'thread_id'   => 2,
                'user_id'     => 3, // Pengajar
                'parent_id'   => null,
                'content'     => "Error itu biasanya karena MySQL/MariaDB belum berjalan.\n\nCoba langkah berikut:\n1. Pastikan service MySQL sudah running: sudo service mysql start\n2. Cek file .env, pastikan DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD sudah benar\n3. Coba ping database: php artisan db:show",
                'is_solution' => 1,
                'is_approved' => 1,
                'created_at'  => $now->copy()->subDays(6),
                'updated_at'  => $now->copy()->subDays(6),
            ],
            [
                'id'          => 2,
                'thread_id'   => 2,
                'user_id'     => 4, // Pelajar
                'parent_id'   => 1, // Reply ke post 1
                'content'     => "Berhasil! Ternyata MySQL saya memang belum running. Terima kasih banyak Pak!",
                'is_solution' => 0,
                'is_approved' => 1,
                'created_at'  => $now->copy()->subDays(6)->addHours(2),
                'updated_at'  => $now->copy()->subDays(6)->addHours(2),
            ],
            [
                'id'          => 3,
                'thread_id'   => 2,
                'user_id'     => 1, // Super Admin
                'parent_id'   => null,
                'content'     => "Tambahan: kalau pakai XAMPP, pastikan Apache dan MySQL sudah di-start dari XAMPP Control Panel ya.",
                'is_solution' => 0,
                'is_approved' => 1,
                'created_at'  => $now->copy()->subDays(5),
                'updated_at'  => $now->copy()->subDays(5),
            ],

            // Thread 3: belongsTo vs hasMany
            [
                'id'          => 4,
                'thread_id'   => 3,
                'user_id'     => 3, // Pengajar
                'parent_id'   => null,
                'content'     => "Penjelasan singkatnya:\n\n**hasMany** → dipakai di model \"induk\"\nContoh: User hasMany Post (satu user punya banyak post)\n\n**belongsTo** → dipakai di model \"anak\"\nContoh: Post belongsTo User (setiap post milik satu user)\n\nKeduanya selalu berpasangan. Kalau User hasMany Post, maka Post belongsTo User.",
                'is_solution' => 1,
                'is_approved' => 1,
                'created_at'  => $now->copy()->subDays(4),
                'updated_at'  => $now->copy()->subDays(4),
            ],
            [
                'id'          => 5,
                'thread_id'   => 3,
                'user_id'     => 4, // Pelajar
                'parent_id'   => 4, // Reply ke post 4
                'content'     => "Oh jadi intinya dilihat dari sisi mana kita mendefinisikan relasi ya? Sekarang lebih paham, terima kasih!",
                'is_solution' => 0,
                'is_approved' => 1,
                'created_at'  => $now->copy()->subDays(4)->addHours(1),
                'updated_at'  => $now->copy()->subDays(4)->addHours(1),
            ],

            // Thread 4: Tips Blade
            [
                'id'          => 6,
                'thread_id'   => 4,
                'user_id'     => 4, // Pelajar
                'parent_id'   => null,
                'content'     => "Tambahan dari saya: @component dan @slot juga sangat berguna untuk membuat komponen yang reusable dengan konten yang fleksibel!",
                'is_solution' => 0,
                'is_approved' => 1,
                'created_at'  => $now->copy()->subDays(2),
                'updated_at'  => $now->copy()->subDays(2),
            ],

            // Thread 6: Plugin Figma
            [
                'id'          => 7,
                'thread_id'   => 6,
                'user_id'     => 3, // Pengajar
                'parent_id'   => null,
                'content'     => "Plugin yang wajib untuk pemula:\n\n1. **Unsplash** — cari foto gratis langsung dari Figma\n2. **Iconify** — ribuan icon gratis\n3. **Lorem ipsum** — generate dummy text\n4. **Figma to HTML** — export ke HTML\n5. **Auto Layout** — sudah built-in, pelajari ini dulu!\n\nMulai dari yang built-in dulu sebelum install banyak plugin.",
                'is_solution' => 1,
                'is_approved' => 1,
                'created_at'  => $now->copy()->subDays(3),
                'updated_at'  => $now->copy()->subDays(3),
            ],

            // Thread 8: Supervised vs Unsupervised
            [
                'id'          => 8,
                'thread_id'   => 8,
                'user_id'     => 3, // Pengajar
                'parent_id'   => null,
                'content'     => "Perbedaan utamanya:\n\n**Supervised Learning**\n- Data training sudah berlabel (ada jawaban benarnya)\n- Contoh: klasifikasi email spam, prediksi harga rumah\n- Algoritma: Linear Regression, Decision Tree, SVM\n\n**Unsupervised Learning**\n- Data training tidak berlabel\n- Menemukan pola sendiri\n- Contoh: segmentasi pelanggan, deteksi anomali\n- Algoritma: K-Means, DBSCAN, PCA",
                'is_solution' => 1,
                'is_approved' => 1,
                'created_at'  => $now->copy()->subDays(1),
                'updated_at'  => $now->copy()->subDays(1),
            ],
            [
                'id'          => 9,
                'thread_id'   => 8,
                'user_id'     => 4, // Pelajar
                'parent_id'   => 8, // Reply ke post 8
                'content'     => "Sangat jelas penjelasannya! Berarti kalau saya mau buat sistem rekomendasi produk, lebih cocok pakai unsupervised ya?",
                'is_solution' => 0,
                'is_approved' => 1,
                'created_at'  => $now->copy()->subDays(1)->addHours(3),
                'updated_at'  => $now->copy()->subDays(1)->addHours(3),
            ],
            [
                'id'          => 10,
                'thread_id'   => 8,
                'user_id'     => 3, // Pengajar
                'parent_id'   => 9, // Reply ke post 9 (nested level 2)
                'content'     => "Tergantung datanya. Kalau punya data histori pembelian yang berlabel, bisa pakai collaborative filtering (supervised). Kalau tidak ada label, bisa pakai clustering (unsupervised). Keduanya valid untuk sistem rekomendasi!",
                'is_solution' => 0,
                'is_approved' => 1,
                'created_at'  => $now->copy()->subDays(1)->addHours(4),
                'updated_at'  => $now->copy()->subDays(1)->addHours(4),
            ],
        ];

        DB::table('forum_posts')->insert($posts);

        $this->command->info('✅ CourseForumSeeder: 3 courses, 8 threads, 10 posts berhasil dibuat.');
    }
}
