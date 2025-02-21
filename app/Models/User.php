<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Bitrix\UserProfile;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Searchable;

    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bitrix_user_id',
        'bitrix_parent_id',
        'bitrix_webhook_token',
        'email',
        'user_name',
        'bitrix_active',
        'password',
        'access_token',
        'is_admin',
        'is_default_password',
        'is_active',
        'last_login',
        'last_ip',
        'status'
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

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }
    public function modules() {
        return $this->belongsToMany(Module::class, 'user_module_permission')
            ->withPivot('permission')
            ->withTimestamps();
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

}
