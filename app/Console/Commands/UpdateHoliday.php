<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\HariLibur;
use App\Models\HariKerja;
use Carbon\Carbon;

class UpdateHoliday extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'holiday:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memperbarui data hari kerja.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = 'https://api-harilibur.vercel.app/api';
        $getYear = now()->year;

        $response = Http::get($url, ['year' => $getYear])->json();

        $holidaylist = array_filter($response, function ($res) {
            return $res['is_national_holiday'] === true;
        });

        $this->info('Deleting all holiday list.');

        HariLibur::truncate();

        $this->info('Adding holiday list this year');

        foreach ($holidaylist as $holiday) {
            HariLibur::create([
                'nama' => $holiday['holiday_name'],
                'tanggal' => $holiday['holiday_date'],
            ]);
        }

        $this->info('Successfully add new holiday list.');

        $this->info('Deleting all workday list.');

        HariKerja::truncate();

        $getHolidayLists = HariLibur::whereYear('tanggal', $getYear)
            ->pluck('tanggal')
            ->map(fn($t) => Carbon::parse($t)->toDateString())
            ->toArray();

        for ($month=1; $month <= 12; $month++) { 
            $totalDay = Carbon::create($getYear, $month)->daysInMonth;
            $hariMinggu = 0;
            $hariLibur = 0;

            for ($day=0; $day <= $totalDay; $day++) { 
                $tanggal = Carbon::create($getYear, $month, $day);
                $strTanggal = $tanggal->toDateString();

                if ($tanggal->isSunday()) {
                    $hariMinggu++;
                }

                if (in_array($strTanggal, $getHolidayLists)) {
                    $hariLibur++;
                }
            }

            $totalLibur = $hariMinggu + $hariLibur;
            $totalKerja = $totalDay - $totalLibur;
            $bulanNama = Carbon::createFromDate(null, $month, null)->translatedFormat('F');

            $this->info($bulanNama);
            $this->info("Total day: $totalDay");
            $this->info("Total sunday: $hariMinggu");
            $this->info("Total national holiday: $hariLibur");
            $this->info("Total holiday: $totalLibur");
            $this->info("Total workday: $totalKerja");

            HariKerja::insert([
                'bulan' => $month,
                'total_hari' => $totalDay,
                'total_hari_minggu' => $hariMinggu,
                'total_hari_libur_nasional' => $hariLibur,
                'total_hari_libur' => $totalLibur,
                'total_hari_kerja' => $totalKerja
            ]);
        }
        $this->info('Successfully add new workday list.');
    }
}
