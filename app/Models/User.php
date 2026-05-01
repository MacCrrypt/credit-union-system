<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'status',
        'must_change_password',
        'password_changed_at',
        'last_login_at',
        'status_changed_at',
        'status_changed_by',
        'status_reason',
        'branch_id',
        'created_by'
    ];

    public function isCentralAdmin(): bool
    {
        return $this->role === 'central_admin';
    }

    public function isBranchAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['central_admin', 'admin']);
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdUsers()
    {
        // Used for admin/staff accountability and dashboard counts.
        return $this->hasMany(User::class, 'created_by');
    }

    public function statusChangedBy()
    {
        return $this->belongsTo(User::class, 'status_changed_by');
    }

    public function membersCreated()
    {
        // Member ownership is tracked through the user who created the record.
        return $this->hasMany(Member::class, 'created_by');
    }

    public function canResetPasswordFor(User $target): bool
    {
        // Central admin resets branch admins; branch admins reset only their own staff.
        if ($this->isCentralAdmin()) {
            return $target->isBranchAdmin();
        }

        if ($this->isBranchAdmin()) {
            return $target->isStaff()
                && $target->branch_id === $this->branch_id;
        }

        return false;
    }

    public function canManageAccountStatusFor(User $target): bool
    {
        // Self-disable is blocked to avoid leaving a branch/admin session without an operator.
        if ($this->id === $target->id) {
            return false;
        }

        if ($this->isCentralAdmin()) {
            return ! $target->isCentralAdmin();
        }

        if ($this->isBranchAdmin()) {
            return $target->isStaff()
                && $target->branch_id === $this->branch_id;
        }

        return false;
    }

    public function canDisableUser(User $target): bool
    {
        return $this->canManageAccountStatusFor($target) && $target->isActive();
    }

    public function canEnableUser(User $target): bool
    {
        return $this->canManageAccountStatusFor($target) && $target->isDisabled();
    }

    public function canCreateMembers(): bool
    {
        // Member creation remains a staff-only operational task.
        return $this->isStaff();
    }

    public function canViewMember(Member $member): bool
    {
        if ($this->isCentralAdmin()) {
            // Central admin oversees operations through logs and summaries, not by opening member cards.
            return false;
        }

        // Staff and branch admins share visibility within their own branch so
        // branch teams can look up signature cards created by colleagues.
        return $this->branch_id !== null
            && $member->branchIdForAccess() === $this->branch_id;
    }

    public function canManageMemberCards(): bool
    {
        // Branch admins maintain cards, review mistakes, and handle controlled deletion.
        return $this->isBranchAdmin();
    }

    public function canManageMember(Member $member): bool
    {
        return $this->canManageMemberCards()
            && $this->branch_id !== null
            && $member->branchIdForAccess() === $this->branch_id;
    }

    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }

    public function isDisabled(): bool
    {
        return $this->status === 'disabled';
    }

    public function mustChangePassword(): bool
    {
        return (bool) $this->must_change_password;
    }


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
            'must_change_password' => 'boolean',
            'password_changed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'status_changed_at' => 'datetime',
        ];
    }
}
