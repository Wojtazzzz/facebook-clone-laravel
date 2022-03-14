<?php

if (!function_exists('frontend_path')) {
    function frontend_path(string $path) {

        if (!str_starts_with($path, '/')) {
            $path = "/$path";
        }

        return config('app.frontend_url') . $path;
    }
}