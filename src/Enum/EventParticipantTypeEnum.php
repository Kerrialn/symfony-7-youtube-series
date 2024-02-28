<?php

namespace App\Enum;

enum EventParticipantTypeEnum : string
{
    case EVENT_ADMIN = 'admin';
    case EVENT_PARTICIPANT = 'participant';
    case EVENT_VIP = 'VIP';

}
