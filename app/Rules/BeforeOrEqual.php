<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class BeforeOrEqual implements Rule
{
    protected $later_date;
    protected $later_date_name;


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($later_date, $later_date_name)
    {
        $this->later_date = $later_date;
        $this->later_date_name = $later_date_name;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $before = Carbon::parse($value);
        $after = Carbon::parse($this->later_date);
        return $before->lte($after);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The :attribute must be before or on the same date as {$this->later_date_name}.";
    }
}
