<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompatibilityRule extends Model
{
    protected $fillable = [
        'compatibility_type_id', 'source_category_id', 'target_category_id',
        'source_spec_type_id', 'target_spec_type_id', 'operator', 'description',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(CompatibilityType::class, 'compatibility_type_id');
    }

    public function sourceCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'source_category_id');
    }

    public function targetCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'target_category_id');
    }

    public function sourceSpecType(): BelongsTo
    {
        return $this->belongsTo(SpecificationType::class, 'source_spec_type_id');
    }

    public function targetSpecType(): BelongsTo
    {
        return $this->belongsTo(SpecificationType::class, 'target_spec_type_id');
    }
}