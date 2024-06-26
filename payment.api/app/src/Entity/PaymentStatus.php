<?php

namespace App\Entity;

enum PaymentStatus: string
{
    case WAITING_FOR_PAYMENT = "Waiting";
    case SUCCEEDED = 'Succeeded';
    case FAILED = 'Failed';
    case CANCELED = 'Canceled';
}