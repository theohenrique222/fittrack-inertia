<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Casts\NullableGender;
use App\Enums\UserRole;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Appended;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'role', 'email_verified_at', 'trainer_id', 'must_reset_password', 'gender', 'birthdate', 'profile_photo_path'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token', 'profile_photo_path'])]
#[Appended(['profile_photo_url'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    protected static function booted(): void
    {
        static::saving(function (self $user) {
            if ($user->gender === '') {
                $user->gender = null;
            }
            if ($user->birthdate === '') {
                $user->birthdate = null;
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_reset_password' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'role' => UserRole::class,
            'gender' => NullableGender::class,
            'birthdate' => 'date',
        ];
    }

    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->profile_photo_path
            ? Storage::disk('public')->url($this->profile_photo_path)
            : null);
    }

    public function age(): ?int
    {
        if (! $this->birthdate) {
            return null;
        }

        return $this->birthdate->diffInYears(Carbon::today());
    }

    public function mustResetPassword(): bool
    {
        return $this->must_reset_password === true;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isPersonal(): bool
    {
        return $this->role === UserRole::PERSONAL;
    }

    public function isStudent(): bool
    {
        return $this->role === UserRole::CLIENT;
    }

    public function isSelf(): bool
    {
        return $this->role === UserRole::SELF;
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class, 'trainer_id');
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function workouts(): HasMany
    {
        return $this->hasMany(Workout::class, 'trainer_id');
    }
}
