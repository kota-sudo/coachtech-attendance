<?php

namespace App\Enums;

enum AttendanceCorrectionRequestStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
}
