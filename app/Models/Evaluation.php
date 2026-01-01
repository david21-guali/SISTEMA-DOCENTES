<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'evaluator_id',
        'innovation_score',
        'relevance_score',
        'results_score',
        'impact_score',
        'methodology_score',
        'final_score',
        'strengths',
        'weaknesses',
        'recommendations',
        'general_comments',
        'report_file',
        'status',
    ];

    /**
     * Relaciones
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function evaluator()
    {
        return $this->belongsTo(Profile::class, 'evaluator_id');
    }

    /**
     * Atributos computados
     */
    public function getAverageRubricScoreAttribute()
    {
        $scores = array_filter([
            $this->innovation_score,
            $this->relevance_score,
            $this->results_score,
            $this->impact_score,
            $this->methodology_score,
        ]);

        return count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : null;
    }

    public function getScoreColorAttribute()
    {
        if (!$this->final_score) return 'secondary';
        
        if ($this->final_score >= 8.5) return 'success';
        if ($this->final_score >= 7.0) return 'primary';
        if ($this->final_score >= 5.0) return 'warning';
        return 'danger';
    }

    public function getIndividualScoreClass($score)
    {
        if ($score >= 4) return 'success';
        if ($score >= 3) return 'warning';
        return 'danger';
    }

    /**
     * Scopes
     */
    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalizada');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'borrador');
    }
}
