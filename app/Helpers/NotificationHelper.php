<?php

namespace App\Helpers;

use App\Enums\PriceRuleTypes;
use App\Models\Price;
use Money\Currency;
use Money\Money;

class NotificationHelper
{
    public function shouldSendPriceNotification(Price $oldPrice, Price $newPrice): bool
    {
        $rules = $newPrice->rules();
        foreach ($rules as $rule) {
            switch ($rule->type) {
                case PriceRuleTypes::BELOW_PRICE->value:
                    $ruleValue = $this->convertToMoney($rule->value, $oldPrice->price->getCurrency());
                    if ($newPrice->price->greaterThanOrEqual($ruleValue)) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::ABOVE_PRICE->value:
                    $ruleValue = $this->convertToMoney($rule->value, $oldPrice->price->getCurrency());
                    if ($newPrice->price->lessThanOrEqual($ruleValue)) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::DECREASE_PERCENTAGE->value:
                    $divided = bcdiv($newPrice->price->getAmount(), $oldPrice->price->getAmount(), 4);
                    $percentage = bcmul('100', bcsub('1', $divided, 4), 0);
                    if ($percentage < $rule->value) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::INCREASE_PERCENTAGE->value:
                    $divided = bcdiv($newPrice->price->getAmount(), $oldPrice->price->getAmount(), 4);
                    $percentage = bcmul('100', bcsub($divided, '1', 4), 0);
                    if ($percentage < $rule->value) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::DECREASE_AMOUNT->value:
                    /** @var Money $difference */
                    $difference = $oldPrice->price->subtract($newPrice->price);
                    /** @var Money $ruleValue */
                    $ruleValue = $this->convertToMoney($rule->value, $oldPrice->price->getCurrency());
                    if ($difference->lessThan($ruleValue)) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::INCREASE_AMOUNT->value:
                    /** @var Money $difference */
                    $difference = $newPrice->price->subtract($oldPrice->price);
                    /** @var Money $ruleValue */
                    $ruleValue = $this->convertToMoney($rule->value, $oldPrice->price->getCurrency());
                    if ($difference->lessThan($ruleValue)) {
                        return false;
                    }
                    break;
                default:
                    break;
            }
        }

        return true;
    }

    private function convertToMoney($value, Currency $currency): Money
    {
        return new Money($value, $currency);
    }
}
