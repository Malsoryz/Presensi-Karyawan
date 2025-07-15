<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\File;


class PresensiTestController extends Controller
{

    public function index()
{
    $qrCode = QrCode::size(256)->generate(url('/presensi/scan'));

    // Ambil random background
    $imageFiles = File::files(public_path('image/background'));
    $bgPath = 'image/background/' . $imageFiles[array_rand($imageFiles)]->getFilename();

    return view('presensi.test', compact('qrCode', 'bgPath'));
}

}
