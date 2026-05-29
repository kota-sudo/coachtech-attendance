<?php

namespace App\Models;

use App\Enums\AttendanceCorrectionRequestStatus;
use Database\Factories\AttendanceCorrectionRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceCorrectionRequest extends Model
{
    /** @use HasFactory<AttendanceCorrectionRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'requested_clock_in',
        'requested_clock_out',
        'requested_note',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_clock_in' => 'datetime',
            'requested_clock_out' => 'datetime',
            'status' => AttendanceCorrectionRequestStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function correctionBreaks(): HasMany
    {
        return $this->hasMany(AttendanceCorrectionBreak::class);
    }

    public function isPending(): bool
    {
        return $this->status === AttendanceCorrectionRequestStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === AttendanceCorrectionRequestStatus::Approved;
    }
}
