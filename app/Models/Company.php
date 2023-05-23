<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'logo',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}
