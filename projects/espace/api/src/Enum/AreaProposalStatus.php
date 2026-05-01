<?php

namespace App\Enum;

enum AreaProposalStatus: string
{
    case PendingValidation = 'pending_validation';
    case AwaitingModeration = 'awaiting_moderation';
    case Active = 'active';
    case Rejected = 'rejected';
    case Unavailable = 'unavailable';
}
