<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InnovationType extends Model
{
    /** @use HasFactory<\Database\Factories\InnovationTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relaciones
     */
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Innovation, $this>
     */
    public function innovations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Innovation::class);
    }
}
