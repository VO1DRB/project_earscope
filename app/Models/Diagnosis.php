<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ConsultationRequest;

class Diagnosis extends Model
{
    protected $fillable = [
        'consultation_request_id',
        'diagnosis_result',
        'notes',
        'is_verified'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
    ];

    public function consultationRequest()
    {
        return $this->belongsTo(ConsultationRequest::class);
    }

    public function images()
    {
        return $this->hasMany(DiagnosisImage::class);
    }
}
