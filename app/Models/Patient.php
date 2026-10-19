<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToClinic;
use App\Models\Concerns\AnonymizesName;

class Patient extends Model
{
    use AnonymizesName, BelongsToClinic, HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'name',
        'date_of_birth',
        'gender',
        'address',
        'phone',
        'national_id',
        'phone_encrypted',
        'phone_bindex',
        'national_id_encrypted',
        'national_id_bindex',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
