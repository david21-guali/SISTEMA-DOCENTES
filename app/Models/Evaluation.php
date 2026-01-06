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
    /** @use HasFactory<\Database\Factories\EvaluationFactory> */
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
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Project, $this>
     */
    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Profile, $this>
     */
    public function evaluator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Profile::class, 'evaluator_id');
    }

    /**
     * Atributos computados
     */
    public function getAverageRubricScoreAttribute(): ?float
    {
        $scores = array_filter([
            $this->innovation_score,
            $this->relevance_score,
            $this->results_score,
            $this->impact_score,
            $this->methodology_score,
        ]);

        return count($scores) > 0 ? (float) round(array_sum($scores) / count($scores), 2) : null;
    }

    public function getScoreColorAttribute(): string
    {
        if (!$this->final_score) return 'secondary';
        
        if ($this->final_score >= 8.5) return 'success';
        if ($this->final_score >= 7.0) return 'primary';
        if ($this->final_score >= 5.0) return 'warning';
        return 'danger';
    }

    public function getIndividualScoreClass(float $score): string
    {
        if ($score >= 4) return 'success';
        if ($score >= 3) return 'warning';
        return 'danger';
    }

    /**
     * Scopes
     */
    /**
     * @param \Illuminate\Database\Eloquent\Builder<Evaluation> $query
     * @return \Illuminate\Database\Eloquent\Builder<Evaluation>
     */
    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalizada');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder<Evaluation> $query
     * @return \Illuminate\Database\Eloquent\Builder<Evaluation>
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'borrador');
    }
}
