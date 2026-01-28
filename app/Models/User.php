<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nim',
        'email',
        'password',
        'phone_number',
        'role', // 'admin' atau 'mahasiswa'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships (Relasi Antar Tabel)
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi: User bisa memiliki banyak laporan kehilangan
     */
    public function lostItems()
    {
        return $this->hasMany(LostItem::class);
    }

    /**
     * Relasi: User bisa memiliki banyak laporan penemuan
     */
    public function foundItems()
    {
        return $this->hasMany(FoundItem::class);
    }

    /**
     * Relasi: User bisa mengajukan banyak klaim barang
     */
    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods (Fungsi Bantuan)
    |--------------------------------------------------------------------------
    */

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}