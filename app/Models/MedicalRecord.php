<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
    use BelongsToClinic, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'patient_id',
        'appointment_id',
        'doctor_id',
        'visit_date',
        'chief_complaint',
        'medical_history',
        'examination',
        'diagnosis',
        'treatment_plan',
        'follow_up_at',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'datetime',
            'follow_up_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function diagnosticOrders(): HasMany
    {
        return $this->hasMany(DiagnosticOrder::class);
    }
}
