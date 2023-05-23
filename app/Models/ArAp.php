<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArAp extends Model
{
    use HasUuid;

    public const TYPE_AP = 'ap';

    public const TYPE_AR = 'ar';

    protected $fillable = [
        'type',
        'date',
        'ar',
        'ap',
        'description',
        'document',
    ];

    protected $casts = [
        'ar' => 'int',
        'ap' => 'int',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
