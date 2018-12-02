<?php
/**
 * Log
 *
 * @author Leey
 * @since  2018 12 2
 */
class Log {

    private static function write($message, $level) {
        $output = sprintf("%s [%s]: %s", date("Y-m-d H:i:s"), $level, $message);
        echo $output."\n";
    }

    public static function debug($message) {
        self::write($message, "DEBUG");
    }

    public static function die($message) {
        self::write($message, "DIE");
        die();
    }

}