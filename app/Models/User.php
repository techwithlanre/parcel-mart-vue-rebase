<?php

namespace App\Models;

use Bavix\Wallet\Traits\CanConfirm;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Interfaces\Wallet;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements Wallet, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasWallet, HasRoles, CanConfirm;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'country_id',
        'currency_id',
        'user_type',
        'business_name',
        'credit_limit',
        'ref_code',
        'ref_by_id',
        'point',
        'firebase_token',
        'dob',
        'gender',
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
    ];

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function getRoleAttribute()
    {
        return $this->roles()->first()->name;
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public static function getRetentionRate($period)
    {
        $start = Carbon::now()->sub($period);
        $end = Carbon::now();

        $usersAtStart = User::where('created_at', '<=', $start)->count();
        $usersAtEnd = User::where('created_at', '<=', $end)->count();

        return ($usersAtEnd / $usersAtStart) * 100;
    }
}
