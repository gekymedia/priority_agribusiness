<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'email',
        'password',
        'role',
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

    /**
     * Determine if the user has a given role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Get the farms for the user.
     */
    public function farms()
    {
        return $this->hasMany(Farm::class);
    }

    /**
     * Get the houses for the user.
     */
    public function houses()
    {
        return $this->hasManyThrough(House::class, Farm::class);
    }

    /**
     * Get the fields for the user.
     */
    public function fields()
    {
        return $this->hasManyThrough(Field::class, Farm::class);
    }

    /**
     * Get the egg productions for the user.
     */
    public function eggProductions()
    {
        return $this->hasManyThrough(EggProduction::class, Farm::class);
    }

    /**
     * Get the egg sales for the user.
     */
    public function eggSales()
    {
        return $this->hasManyThrough(EggSale::class, Farm::class);
    }

    /**
     * Get the bird sales for the user.
     */
    public function birdSales()
    {
        return $this->hasManyThrough(BirdSale::class, Farm::class);
    }

    /**
     * Get the poultry expenses for the user.
     */
    public function poultryExpenses()
    {
        return $this->hasManyThrough(PoultryExpense::class, Farm::class);
    }

    /**
     * Get the crop input expenses for the user.
     */
    public function cropInputExpenses()
    {
        return $this->hasManyThrough(CropInputExpense::class, Farm::class);
    }

    /**
     * Get the harvests for the user.
     */
    public function harvests()
    {
        return $this->hasManyThrough(Harvest::class, Farm::class);
    }
}