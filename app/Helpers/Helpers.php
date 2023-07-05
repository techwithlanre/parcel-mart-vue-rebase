<?php

if (! function_exists('getCountry')) {
    function getCountry($key, $value) {
        return \App\Models\Country::where($key, $value)->first();
    }
}

if (! function_exists('getState')) {
    function getState($key, $value) {
        return \App\Models\State::where($key, $value)->first();
    }
}


if (! function_exists('getCity')) {
    function getCity($key, $value) {
        return \App\Models\City::where($key, $value)->first();
    }
}
