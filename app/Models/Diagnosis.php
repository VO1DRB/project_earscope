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
    ];

    public function consultation()
    {
        return $this->belongsTo(ConsultationRequest::class, 'consultation_request_id');
    }
}
