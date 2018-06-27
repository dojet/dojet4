<?php
/**
 * DAssert
 *
 * @author liyan
 * @since  2014
 */
class DAssert {

    /**
     * 封装断言
     *
     * @param bool $condition
     * @param string $message
     */
    public static function assert($condition, $message = null) {
        if ($condition) {
            return ;
        }

        $backtrace = debug_backtrace();
        Trace::verbose(sprintf("assert failed. %s\tbacktrace: %s", $message, json_encode($backtrace)));
        Trace::debug(sprintf("assert failed. %s", $message));
        die();
    }

    public static function assertNumeric($var) {
        $args = func_get_args();
        array_walk($args, function($var) {
            DAssert::assert(is_numeric($var), 'nan, '.var_export($var, true));
        });
    }

    /**
     * 数字数组断言
     *
     * @param mix $array
     */
    public static function assertNumericArray($array) {
        DAssert::assertArray($array);
        array_walk($array, function($var) {
            DAssert::assertNumeric($var);
        });
    }

    /**
     * 非空数字数组断言
     *
     * @param mix $array
     */
    public static function assertNotEmptyNumericArray($array) {
        DAssert::assert(!empty($array), 'array should not be empty');
        DAssert::assertNumericArray($array);
    }

    public static function assertArray($var, $message = null) {
        DAssert::assert(is_array($var), $message ?? 'not an array');
    }

    public static function assertKeyExists($key, $array, $message = null) {
        DAssert::assert(array_key_exists($key, $array), $message ?? 'key not exists');
    }

    public static function assertFileExists($filename, $message = null) {
        DAssert::assert(file_exists($filename), $message ?? "$filename not exists");
    }

    public static function assertNotFalse($condition, $message = null) {
        DAssert::assert(false !== $condition, $message ?? "value can not be false");
    }

    public static function assertNotNull($condition, $message = null) {
        DAssert::assert(null !== $condition, $message ?? "value can not be null");
    }

    public static function assertString($var, $message = null) {
        DAssert::assert(is_string($var), $message ?? 'not a string');
    }

    public static function assertCallable($var, $message = null) {
        DAssert::assert(is_callable($var), $message ?? "it's not callable");
    }

}
