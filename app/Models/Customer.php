<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'logo',
        'ar_balance',
        'ap_balance',
        'ar_ap_balance',
    ];

    protected $casts = [
        'ar_balance' => 'float',
        'ap_balance' => 'float',
        'ar_ap_balance' => 'float',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function ArAps(): HasMany
    {
        return $this->hasMany(ArAp::class);
    }
}
