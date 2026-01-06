<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ResourceType
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ResourceType extends Model
{
    /** @use HasFactory<\Database\Factories\ResourceTypeFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Resource, $this>
     */
    public function resources(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Resource::class);
    }
}
