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

if (! function_exists('upsErrorMessage')) {
    function upsErrorMessage($result) {
        if (array_key_exists($result['response']['errors'][0]['code'], config('messages'))) {
            return config('messages')[$result['response']['errors'][0]['code']];
        }

        return NULL;
    }
}
