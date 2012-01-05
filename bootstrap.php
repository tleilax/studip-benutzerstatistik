<?php
    require_once 'vendor/trails/trails.php';
    require_once 'app/controllers/studip_controller.php';
    require_once 'vendor/mobile_device_detect.php';

    if (!class_exists('BenutzerStatistik_Helper')) {
        require 'classes/BenutzerStatistik_Helper.class.php';
    }
    if (!class_exists($class_name)) {
        require 'classes/BenutzerStatistik_Summarizer.class.php';
    }

    if (!function_exists('array_pluck')) {
        function array_pluck ($array, $key) {
            return array_map(create_function('$item', 'return isset($item["'.$key.'"]) ? $item["'.$key.'"] : null;'), $array);
        }
    }

    if (!function_exists('numberformat')) {
        function numberformat($number, $precision = 0, $decimal_separator = ',', $thousands_separator = '.') {
            return number_format($number, $precision, $decimal_separator, $thousands_separator);
        }
    }
    if (!function_exists('percent')) {
        function percent($total, $part, $decimals = 1, $amplifier = 100, $postfix = '%') {
            return numberformat($total ? $amplifier * $part / $total : 0, $decimals).$postfix;
        }
    }

