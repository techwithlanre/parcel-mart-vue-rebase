<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class ShipmentDateTimeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date_selected = Carbon::createFromDate($value);
        if ($date_selected < Carbon::tomorrow()) {
            $fail('Shipment time must start from tomorrow');
        }


        if ($date_selected->format('H') < 9) {
            $fail('Shipment time must start from 9:00am');
        }

        if ($date_selected->format('H') > 15) {
            $fail('Shipment time should not be more than 3:00pm');
        }

        if ($date_selected > Carbon::now()->addDays(10)) {
            $fail('shipment date should not be more than 10 days from today');
        }
    }
}
