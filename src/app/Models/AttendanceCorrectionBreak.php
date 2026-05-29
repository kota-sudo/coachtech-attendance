<?php

namespace App\Models;

use Database\Factories\AttendanceCorrectionBreakFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceCorrectionBreak extends Model
{
    /** @use HasFactory<AttendanceCorrectionBreakFactory> */
    use HasFactory;

    protected $fillable = [
        'attendance_correction_request_id',
        'break_start',
        'break_end',
    ];

    protected function casts(): array
    {
        return [
            'break_start' => 'datetime',
            'break_end' => 'datetime',
        ];
    }

    public function correctionRequest(): BelongsTo
    {
        return $this->belongsTo(AttendanceCorrectionRequest::class, 'attendance_correction_request_id');
    }
}
