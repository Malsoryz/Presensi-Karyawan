<?php

namespace App\Models;

use App\Models\Presensi;
use App\Models\Jabatan;
use App\Models\Tipe;
use App\Models\AdminNotification;
use App\Enums\StatusPresensi;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable //implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'birth_date',
        'gender',
        'phone_number',
        'departmen',
        'tanggal_masuk',
        'tanggal_masuk_sebagai_karyawan',
        'rekening_bank',
        'status_approved',
        'role',
        'jabatan_id',
        'tipe_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // public function isAdmin(): bool
    // {
    //     return $this->jabatan === 'admin';
    // }

    public function getStatus(): StatusPresensi
    {
        return StatusPresensi::tryFrom($this->status);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status_approved' => 'boolean',
        ];
    }

    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function notifications()
    {
        return $this->hasMany(AdminNotification::class, 'sender_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function tipe()
    {
        return $this->belongsTo(Tipe::class);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function isApproved(): bool
    {
        return (bool) $this->status_approved;
    }

    // public function canAccessPanel(Panel $panel): bool
    // {
    //     return $this->jabatan === 'admin';
    // }
}

