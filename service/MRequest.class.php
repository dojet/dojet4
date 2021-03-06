<?php
/**
 * request
 *
 * @author liyan
 * @since 2012
 */
class MRequest {

    private static $_params;

    public static function get($key) {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }

    public static function post($key) {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }

    public static function allPost() {
        return $_POST;
    }

    public static function rawPost() {
        return $GLOBALS["HTTP_RAW_POST_DATA"];
    }

    public static function request($key) {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;
    }

    public static function cookie($key) {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
    }

    public static function server($key) {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    public static function header($key) {
        $header = strtoupper('HTTP_'.$key);
        return self::server($header);
    }

    public static function file($key) {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }

    public static function param($key, $value = null) {
        if (is_null($value)) {
            return isset(self::$_params[$key]) ? self::$_params[$key] : null;
        } else {
            self::$_params[$key] = $value;
        }
    }

    public static function allParams() {
        return self::$_params;
    }
}