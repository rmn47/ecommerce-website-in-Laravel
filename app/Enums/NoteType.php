<?php

namespace App\Enums;

enum NoteType: string
{
    case Refund = 'refund';
    case Warranty = 'warranty';
    case Shipping = 'shipping';
    case Delivery = 'delivery';
}
