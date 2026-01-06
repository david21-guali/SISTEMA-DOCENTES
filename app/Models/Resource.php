<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Resource
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property float $cost
 * @property int $resource_type_id
 * @property-read \App\Models\ResourceType|null $type
 * @property-read string|null $type_slug
 */
class Resource extends Model
{
    /** @use HasFactory<\Database\Factories\ResourceFactory> */
    use HasFactory;

    protected $fillable = ['name', 'resource_type_id', 'description', 'cost', 'file_path'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\ResourceType, $this>
     */
    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ResourceType::class, 'resource_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\App\Models\Project, $this>
     */
    public function projects(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Project::class)
                    ->withPivot('quantity', 'assigned_date', 'notes')
                    ->withTimestamps();
    }

    public function getTypeSlugAttribute(): ?string
    {
        return $this->type ? $this->type->slug : null;
    }
}
