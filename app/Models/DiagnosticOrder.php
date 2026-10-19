<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiagnosticOrder extends Model
{
    use BelongsToClinic, SoftDeletes;

    public const TYPES = ['lab', 'imaging'];
    public const STATUSES = ['ordered', 'in_progress', 'completed', 'cancelled'];
    public const VALID_TRANSITIONS = [
        'ordered' => ['in_progress', 'cancelled'],
        'in_progress' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    protected $fillable = [
        'clinic_id',
        'medical_record_id',
        'patient_id',
        'doctor_id',
        'type',
        'test_name',
        'instructions',
        'status',
        'result',
        'ordered_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function canTransitionTo(string $status): bool
    {
        return $this->status === $status
            || in_array($status, self::VALID_TRANSITIONS[$this->status] ?? [], true);
    }
}
