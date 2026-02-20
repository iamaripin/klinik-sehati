<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
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
        'username', 
        'title',
        'suffix',
        'user_dob',
        'gender',
        'role', 
        'status',
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
        ];
    }

    protected $casts = [
        'user_dob' => 'date',
    ];

    /**
     * Auto hash password ketika di-set
     */
    public function setPasswordAttribute($value)
    {
        if ($value && strlen($value) < 60) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    /**
     * Mendapatkan nama lengkap untuk tampilan di dashboard
     */
    public function getFullNameAttribute()
    {
        $t = $this->title ? $this->title . '. ' : '';
        $s = $this->suffix ? ', ' . $this->suffix : '';
        return $t . $this->first_name . ' ' . $this->last_name . $s;
    }

    /**
     * Mengecek role
     */
    public function isRole($roles)
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }
}

