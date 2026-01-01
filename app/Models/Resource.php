<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'resource_type_id', 'description', 'cost', 'file_path'];

    public function type()
    {
        return $this->belongsTo(ResourceType::class, 'resource_type_id');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)
                    ->withPivot('quantity', 'assigned_date', 'notes')
                    ->withTimestamps();
    }

    public function getTypeSlugAttribute()
    {
        return $this->type ? $this->type->slug : null;
    }
}
