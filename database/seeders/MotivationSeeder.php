<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Support\Inspire\Motivation;

class MotivationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('quotes')->insert([
            [
                "motivation" => "Kerja keras mengalahkan bakat ketika bakat tidak bekerja keras.",
                "author" => "Tim Notke"
            ],
            [
                "motivation" => "Jangan menunggu kesempatan, ciptakanlah kesempatan itu.",
                "author" => "George Bernard Shaw"
            ],
            [
                "motivation" => "Bekerjalah seakan-akan kamu tidak butuh uang.",
                "author" => "Mark Twain"
            ],
            [
                "motivation" => "Kesuksesan berawal dari kerja keras dan ketekunan.",
                "author" => "Soekarno"
            ],
            [
                "motivation" => "Kerja keras hari ini adalah fondasi kesuksesan di masa depan.",
                "author" => "Anonim"
            ],
            [
                "motivation" => "Bangkitlah setiap kali kamu jatuh.",
                "author" => "Nelson Mandela"
            ],
            [
                "motivation" => "Semangat adalah api dalam diri yang menggerakkan setiap langkah.",
                "author" => "Anonim"
            ],
            [
                "motivation" => "Sukses bukan akhir, gagal bukan berarti kehancuran: keberanian untuk terus adalah yang penting.",
                "author" => "Winston Churchill"
            ],
            [
                "motivation" => "Setiap kerja keras pasti menghasilkan, cepat atau lambat.",
                "author" => "Anonim"
            ],
            [
                "motivation" => "Jangan takut gagal, takutlah untuk tidak mencoba.",
                "author" => "Roy T. Bennett"
            ],
            [
                "motivation" => "Kerja keras adalah harga yang harus dibayar untuk mencapai mimpi.",
                "author" => "Vince Lombardi"
            ],
            [
                "motivation" => "Sukses adalah kombinasi dari semangat, kerja keras, dan doa.",
                "author" => "Anonim"
            ],
            [
                "motivation" => "Keberhasilan tidak datang kepada orang yang menunggu, tapi kepada mereka yang berusaha.",
                "author" => "Thomas Edison"
            ],
            [
                "motivation" => "Jadilah produktif, bukan hanya sibuk.",
                "author" => "Tim Ferriss"
            ],
            [
                "motivation" => "Jangan menyerah, karena hal-hal besar butuh waktu.",
                "author" => "Jack Ma"
            ],
            [
                "motivation" => "Kerja keras adalah investasi terbaik dalam hidup.",
                "author" => "Anonim"
            ],
            [
                "motivation" => "Motivasi adalah awal, kebiasaanlah yang membawa hasil.",
                "author" => "Jim Ryun"
            ],
            [
                "motivation" => "Jika kamu lelah, istirahatlah. Tapi jangan berhenti.",
                "author" => "Anonim"
            ],
            [
                "motivation" => "Disiplin adalah jembatan antara tujuan dan pencapaian.",
                "author" => "Jim Rohn"
            ],
            [
                "motivation" => "Kerja dengan cinta menghasilkan keajaiban.",
                "author" => "Kahlil Gibran"
            ],
            [
                "motivation" => "Berikan yang terbaik dari dirimu, tidak peduli siapa yang menonton.",
                "author" => "Steve Jobs"
            ],
            [
                "motivation" => "Fokuslah pada tujuan, bukan hambatan.",
                "author" => "Zig Ziglar"
            ],
            [
                "motivation" => "Tidak ada rahasia untuk sukses. Itu hasil dari persiapan, kerja keras, dan belajar dari kegagalan.",
                "author" => "Colin Powell"
            ],
            [
                "motivation" => "Kamu tidak harus hebat untuk memulai, tapi kamu harus memulai untuk menjadi hebat.",
                "author" => "Zig Ziglar"
            ],
            [
                "motivation" => "Kerja cerdas mengalahkan kerja keras, tapi kerja keras tetap penting.",
                "author" => "Elon Musk"
            ]
        ]);
    }
}
