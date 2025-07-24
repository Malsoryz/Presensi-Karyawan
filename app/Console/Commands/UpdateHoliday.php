<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\HariLibur;
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
    protected $description = 'Memperbarui data hari libur.';

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
                'bulan' => Carbon::parse($holiday['holiday_date'])->month,
            ]);
        }

        $this->info('Successfully add new holiday list.');
    }
}
