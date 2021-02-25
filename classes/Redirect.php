<?php

class Redirect
{

    public static function to($location = null)
    {
        if ($location) {
            header("Location: {$location}");
            exit();
        }
    }

    public static function re()
    {
        header("Refresh: 0");
        die();
    }

}
