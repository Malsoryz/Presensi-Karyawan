<?php

namespace App\Support\Inspire;

class Motivation
{
    protected static $motivasi = [
        [
            "words" => "Kerja keras mengalahkan bakat ketika bakat tidak bekerja keras.",
            "author" => "Tim Notke"
        ],
        [
            "words" => "Jangan menunggu kesempatan, ciptakanlah kesempatan itu.",
            "author" => "George Bernard Shaw"
        ],
        [
            "words" => "Bekerjalah seakan-akan kamu tidak butuh uang.",
            "author" => "Mark Twain"
        ],
        [
            "words" => "Kesuksesan berawal dari kerja keras dan ketekunan.",
            "author" => "Soekarno"
        ],
        [
            "words" => "Kerja keras hari ini adalah fondasi kesuksesan di masa depan.",
            "author" => "Anonim"
        ],
        [
            "words" => "Bangkitlah setiap kali kamu jatuh.",
            "author" => "Nelson Mandela"
        ],
        [
            "words" => "Semangat adalah api dalam diri yang menggerakkan setiap langkah.",
            "author" => "Anonim"
        ],
        [
            "words" => "Sukses bukan akhir, gagal bukan berarti kehancuran: keberanian untuk terus adalah yang penting.",
            "author" => "Winston Churchill"
        ],
        [
            "words" => "Setiap kerja keras pasti menghasilkan, cepat atau lambat.",
            "author" => "Anonim"
        ],
        [
            "words" => "Jangan takut gagal, takutlah untuk tidak mencoba.",
            "author" => "Roy T. Bennett"
        ],
        [
            "words" => "Kerja keras adalah harga yang harus dibayar untuk mencapai mimpi.",
            "author" => "Vince Lombardi"
        ],
        [
            "words" => "Sukses adalah kombinasi dari semangat, kerja keras, dan doa.",
            "author" => "Anonim"
        ],
        [
            "words" => "Keberhasilan tidak datang kepada orang yang menunggu, tapi kepada mereka yang berusaha.",
            "author" => "Thomas Edison"
        ],
        [
            "words" => "Jadilah produktif, bukan hanya sibuk.",
            "author" => "Tim Ferriss"
        ],
        [
            "words" => "Jangan menyerah, karena hal-hal besar butuh waktu.",
            "author" => "Jack Ma"
        ],
        [
            "words" => "Kerja keras adalah investasi terbaik dalam hidup.",
            "author" => "Anonim"
        ],
        [
            "words" => "Motivasi adalah awal, kebiasaanlah yang membawa hasil.",
            "author" => "Jim Ryun"
        ],
        [
            "words" => "Jika kamu lelah, istirahatlah. Tapi jangan berhenti.",
            "author" => "Anonim"
        ],
        [
            "words" => "Disiplin adalah jembatan antara tujuan dan pencapaian.",
            "author" => "Jim Rohn"
        ],
        [
            "words" => "Kerja dengan cinta menghasilkan keajaiban.",
            "author" => "Kahlil Gibran"
        ],
        [
            "words" => "Berikan yang terbaik dari dirimu, tidak peduli siapa yang menonton.",
            "author" => "Steve Jobs"
        ],
        [
            "words" => "Fokuslah pada tujuan, bukan hambatan.",
            "author" => "Zig Ziglar"
        ],
        [
            "words" => "Tidak ada rahasia untuk sukses. Itu hasil dari persiapan, kerja keras, dan belajar dari kegagalan.",
            "author" => "Colin Powell"
        ],
        [
            "words" => "Kamu tidak harus hebat untuk memulai, tapi kamu harus memulai untuk menjadi hebat.",
            "author" => "Zig Ziglar"
        ],
        [
            "words" => "Kerja cerdas mengalahkan kerja keras, tapi kerja keras tetap penting.",
            "author" => "Elon Musk"
        ]
    ];

    public static function quote()
    {
        return self::$motivasi[array_rand(self::$motivasi)];
    }

    public static function quotes()
    {
        return self::$motivasi;
    }
}