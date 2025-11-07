<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    /**
     * Get repair requests reported by this user
     */
    public function reportedRequests(): HasMany
    {
        return $this->hasMany(RepairRequest::class, 'reported_by');
    }

    /**
     * Get repair requests approved by this user
     */
    public function approvedRequests(): HasMany
    {
        return $this->hasMany(RepairRequest::class, 'approved_by');
    }

    /**
     * Get technician assignments for this user
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(TechnicianAssignment::class, 'technician_id');
    }

    /**
     * Get assignments created by this user
     */
    public function createdAssignments(): HasMany
    {
        return $this->hasMany(TechnicianAssignment::class, 'assigned_by');
    }

    /**
     * Check if technician is available (not assigned to any active task)
     */
    public function isAvailable(): bool
    {
        return !$this->assignedTasks()
            ->whereIn('status', ['assigned', 'in_progress'])
            ->exists();
    }
}
