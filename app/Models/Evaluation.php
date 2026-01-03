<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Evaluation
 *
 * @property int $id
 * @property int $project_id
 * @property int $evaluator_id
 * @property float|null $innovation_score
 * @property float|null $relevance_score
 * @property float|null $results_score
 * @property float|null $impact_score
 * @property float|null $methodology_score
 * @property float|null $final_score
 * @property string|null $strengths
 * @property string|null $weaknesses
 * @property string|null $recommendations
 * @property string|null $general_comments
 * @property string|null $report_file
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Project $project
 * @property-read Profile $evaluator
 * @property-read float|null $average_rubric_score
 * @property-read string $score_color
 */
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
