<?php

namespace App\Enums;

enum PriceRuleTypes: string
{
    case BELOW_PRICE = 'below_price';
    case ABOVE_PRICE = 'above_price';
    case DECREASE_PERCENTAGE = 'decrease_percentage';
    case INCREASE_PERCENTAGE = 'increase_percentage';
    case DECREASE_AMOUNT = 'decrease_amount';
    case INCREASE_AMOUNT = 'increase_amount';
}
