<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    public function run()
    {
        $feedbacks = [
            [
                'name' => 'Andi Pratama',
                'email' => 'andi.pratama@email.com',
                'message' => 'Website ini sangat membantu! Artikel tentang PHP sangat lengkap dan mudah dipahami. Terima kasih sudah berbagi ilmu yang bermanfaat.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
            ],
            [
                'name' => 'Sari Dewi',
                'email' => 'sari.dewi@gmail.com',
                'message' => 'Saya seorang pemula dalam programming dan artikel-artikel di sini sangat membantu saya memahami konsep dasar. Mohon buat lebih banyak tutorial untuk pemula ya!',
                'created_at' => date('Y-m-d H:i:s', strtotime('-8 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-8 days'))
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@company.com',
                'message' => 'Artikel tentang optimasi database MySQL sangat berguna untuk project saya. Performa aplikasi meningkat signifikan setelah menerapkan tips yang diberikan.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-6 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-6 days'))
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya.sari@developer.id',
                'message' => 'Konten tentang JavaScript ES6+ sangat up-to-date. Sebagai frontend developer, saya mendapat banyak insight baru. Keep up the good work!',
                'created_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
            ],
            [
                'name' => 'Rizki Maulana',
                'email' => 'rizki.maulana@tech.com',
                'message' => 'Tutorial CodeIgniter 4 API sangat detail dan mudah diikuti. Saya berhasil membuat API pertama saya menggunakan panduan ini. Terima kasih!',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'name' => 'Linda Wijaya',
                'email' => 'linda.wijaya@startup.co.id',
                'message' => 'Artikel tentang keamanan web sangat penting dan informatif. Sebagai startup, keamanan adalah prioritas utama kami. Artikel ini membantu kami memahami best practices.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'name' => 'Agus Setiawan',
                'email' => 'agus.setiawan@webdev.com',
                'message' => 'CSS Grid dan Flexbox tutorial sangat membantu dalam project responsive design saya. Penjelasannya clear dan contoh kodenya praktis.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'name' => 'Putri Maharani',
                'email' => 'putri.maharani@designer.id',
                'message' => 'Sebagai UI/UX designer yang belajar coding, artikel-artikel di sini sangat membantu saya memahami sisi teknis dari design yang saya buat.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-12 hours')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-12 hours'))
            ],
            [
                'name' => 'Doni Prasetyo',
                'email' => 'doni.prasetyo@devops.com',
                'message' => 'Tutorial Docker sangat berguna untuk setup development environment. Sekarang tim kami bisa setup project dengan cepat dan konsisten.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-6 hours')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-6 hours'))
            ],
            [
                'name' => 'Fitri Handayani',
                'email' => 'fitri.handayani@student.ac.id',
                'message' => 'Saya mahasiswa informatika dan website ini menjadi referensi utama untuk tugas-tugas kuliah. Materinya sangat lengkap dan mudah dipahami.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))
            ],
            [
                'name' => 'Hendra Gunawan',
                'email' => 'hendra.gunawan@freelancer.com',
                'message' => 'Sebagai freelance developer, artikel tentang Git workflow sangat membantu dalam kolaborasi dengan klien. Best practices yang diberikan sangat applicable.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ],
            [
                'name' => 'Nisa Rahmawati',
                'email' => 'nisa.rahmawati@bootcamp.id',
                'message' => 'Konten website ini sangat membantu dalam pembelajaran di bootcamp. Materi yang disajikan sesuai dengan kebutuhan industri saat ini.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
            ]
        ];

        // Insert feedbacks
        foreach ($feedbacks as $feedback) {
            $this->db->table('feedbacks')->insert($feedback);
        }

        echo "Inserted " . count($feedbacks) . " feedbacks successfully.\n";
    }
}