<?php

namespace App\Helpers;

use App\Enums\PriceRuleTypes;
use App\Models\Price;

class NotificationHelper
{
    public function shouldSendPriceNotification(Price $oldPrice, Price $newPrice): bool
    {
        $rules = $newPrice->rules();
        foreach ($rules as $rule) {
            switch ($rule->type) {
                case PriceRuleTypes::BELOW_PRICE->value:
                    if ($newPrice->price >= $rule->value) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::ABOVE_PRICE->value:
                    if ($newPrice->price <= $rule->value) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::DECREASE_PERCENTAGE->value:
                    // todo
                    break;
                case PriceRuleTypes::INCREASE_PERCENTAGE->value:
                    // todo
                    break;
                case PriceRuleTypes::DECREASE_AMOUNT->value:
                    if (($oldPrice->price - $newPrice->price) < $rule->value) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::INCREASE_AMOUNT->value:
                    if (($newPrice->price - $oldPrice->price) < $rule->value) {
                        return false;
                    }
                    break;
                default:
                    break;
            }
        }

        return true;
    }
}
