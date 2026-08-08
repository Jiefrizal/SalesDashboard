<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
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
        'role',
        'allowed_menus',
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
            'password'          => 'hashed',
            'allowed_menus'     => 'array',
        ];
    }

    /** Helper: check if user is super admin / admin user */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin' || $this->role === 'admin';
    }

    /** Helper: check if user is editor */
    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    /** Helper: check if user is viewer */
    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    /** Helper: check if user has permission to edit data */
    public function canEdit(): bool
    {
        return $this->isSuperAdmin() || $this->isEditor();
    }

    /** Helper: check if user has access to a specific menu */
    public function hasMenuAccess(string $menuKey): bool
    {
        if (is_null($this->allowed_menus)) {
            if ($this->role === 'super_admin') {
                return true;
            }
            return in_array($menuKey, ['dashboard', 'stu_unit', 'stok_unit', 'digital_marketing'], true);
        }

        return is_array($this->allowed_menus) && in_array($menuKey, $this->allowed_menus, true);
    }

    /** Helper to get default available menu list */
    public static function availableMenus(): array
    {
        return [
            'dashboard'         => 'DASHBOARD',
            'stu_unit'          => 'STU UNIT',
            'stok_unit'         => 'STOK UNIT',
            'digital_marketing' => 'SOSIAL MEDIA',
            'cabang'            => 'CABANG',
            'users'             => 'KELOLA USER',
        ];
    }
}
