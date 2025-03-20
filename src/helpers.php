<?php

if (!function_exists('number')) {
    /**
     * Get the Number instance
     *
     * @return \ChrisHenrique\LaravelUtils\Number
     */
    // function number()
    // {
    //     return app(\ChrisHenrique\LaravelUtils\Number::class);
    // }
    function number($number)
    {
        return new \ChrisHenrique\LaravelUtils\Number($number);
    }
}
