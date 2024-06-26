<?php

namespace App\Entity;

enum PaymentMethod: string
{
    case CARD = 'Card';
    case PAYPAL = 'Paypal';
}