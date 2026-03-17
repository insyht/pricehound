<?php

namespace App\Helpers;

use App\Enums\PriceRuleTypes;
use App\Models\Price;
use Money\Currency;
use Money\Money;

class NotificationHelper
{
    public function shouldSendPriceNotification(?Price $oldPrice, Price $newPrice): bool
    {
        $rules = $newPrice->rules();
        foreach ($rules as $rule) {
            switch ($rule->type) {
                case PriceRuleTypes::BELOW_PRICE->value:
                    $ruleValue = $this->convertToMoney($rule->value, $newPrice->price->getCurrency());
                    if ($newPrice->price->greaterThanOrEqual($ruleValue)) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::ABOVE_PRICE->value:
                    $ruleValue = $this->convertToMoney($rule->value, $newPrice->price->getCurrency());
                    if ($newPrice->price->lessThanOrEqual($ruleValue)) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::DECREASE_PERCENTAGE->value:
                    if ($oldPrice === null) {
                        return false;
                    }
                    $divided = bcdiv($newPrice->price->getAmount(), $oldPrice->price->getAmount(), 4);
                    $percentage = bcmul('100', bcsub('1', $divided, 4), 0);
                    if ($percentage < $rule->value) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::INCREASE_PERCENTAGE->value:
                    if ($oldPrice === null) {
                        return false;
                    }
                    $divided = bcdiv($newPrice->price->getAmount(), $oldPrice->price->getAmount(), 4);
                    $percentage = bcmul('100', bcsub($divided, '1', 4), 0);
                    if ($percentage < $rule->value) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::DECREASE_AMOUNT->value:
                    if ($oldPrice === null) {
                        return false;
                    }
                    /** @var Money $difference */
                    $difference = $oldPrice->price->subtract($newPrice->price);
                    /** @var Money $ruleValue */
                    $ruleValue = $this->convertToMoney($rule->value, $oldPrice->price->getCurrency());
                    if ($difference->lessThan($ruleValue)) {
                        return false;
                    }
                    break;
                case PriceRuleTypes::INCREASE_AMOUNT->value:
                    if ($oldPrice === null) {
                        return false;
                    }
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

    public function getTriggeredRules(?Price $oldPrice, Price $newPrice): array
    {
        $triggeredRules = [];
        $rules = $newPrice->rules();
        foreach ($rules as $rule) {
            switch ($rule->type) {
                case PriceRuleTypes::BELOW_PRICE->value:
                    $ruleValue = $this->convertToMoney($rule->value, $newPrice->price->getCurrency());
                    if ($newPrice->price->lessThan($ruleValue)) {
                        $triggeredRules[] = sprintf(
                            '%s (%s %s)',
                            PriceRuleTypes::BELOW_PRICE->value,
                            $newPrice->price->getCurrency(),
                            $ruleValue->getAmount() / 100
                        );
                    }
                    break;
                case PriceRuleTypes::ABOVE_PRICE->value:
                    $ruleValue = $this->convertToMoney($rule->value, $newPrice->price->getCurrency());
                    if ($newPrice->price->greaterThan($ruleValue)) {
                        $triggeredRules[] = sprintf(
                            '%s (%s %s)',
                            PriceRuleTypes::ABOVE_PRICE->value,
                            $newPrice->price->getCurrency(),
                            $ruleValue->getAmount() / 100
                        );
                    }
                    break;
                case PriceRuleTypes::DECREASE_PERCENTAGE->value:
                    if ($oldPrice === null) {
                        break;
                    }
                    $divided = bcdiv($newPrice->price->getAmount(), $oldPrice->price->getAmount(), 4);
                    $percentage = bcmul('100', bcsub('1', $divided, 4), 0);
                    if ($percentage >= $rule->value) {
                        $triggeredRules[] = sprintf(
                            '%s (%s%%)',
                            PriceRuleTypes::DECREASE_PERCENTAGE->value,
                            $rule->value
                        );
                    }
                    break;
                case PriceRuleTypes::INCREASE_PERCENTAGE->value:
                    if ($oldPrice === null) {
                        break;
                    }
                    $divided = bcdiv($newPrice->price->getAmount(), $oldPrice->price->getAmount(), 4);
                    $percentage = bcmul('100', bcsub($divided, '1', 4), 0);
                    if ($percentage >= $rule->value) {
                        $triggeredRules[] = sprintf(
                            '%s (%s%%)',
                            PriceRuleTypes::INCREASE_PERCENTAGE->value,
                            $rule->value
                        );
                    }
                    break;
                case PriceRuleTypes::DECREASE_AMOUNT->value:
                    if ($oldPrice === null) {
                        break;
                    }
                    /** @var Money $difference */
                    $difference = $oldPrice->price->subtract($newPrice->price);
                    /** @var Money $ruleValue */
                    $ruleValue = $this->convertToMoney($rule->value, $oldPrice->price->getCurrency());
                    if ($difference->greaterThanOrEqual($ruleValue)) {
                        $triggeredRules[] = sprintf(
                            '%s (%s %s)',
                            PriceRuleTypes::DECREASE_AMOUNT->value,
                            $newPrice->price->getCurrency(),
                            $ruleValue->getAmount() / 100
                        );
                    }
                    break;
                case PriceRuleTypes::INCREASE_AMOUNT->value:
                    if ($oldPrice === null) {
                        break;
                    }
                    /** @var Money $difference */
                    $difference = $newPrice->price->subtract($oldPrice->price);
                    /** @var Money $ruleValue */
                    $ruleValue = $this->convertToMoney($rule->value, $oldPrice->price->getCurrency());
                    if ($difference->greaterThanOrEqual($ruleValue)) {
                        $triggeredRules[] = sprintf(
                            '%s (%s %s)',
                            PriceRuleTypes::INCREASE_AMOUNT->value,
                            $newPrice->price->getCurrency(),
                            $ruleValue->getAmount() / 100
                        );
                    }
                    break;
                default:
                    break;
            }
        }

        return $triggeredRules;
    }

    private function convertToMoney($value, Currency $currency): Money
    {
        return new Money($value, $currency);
    }
}
