<?php
/**
 * Trace
 *
 * @author liyan
 * @since  2009
 */
class Trace {

    const DEBUG = 0x1;
    const NOTICE= 0x2;
    const WARN  = 0x4;
    const FATAL = 0x8;
    const VERBOSE = 0x10;

    const TRACE_ALL = 0xffff;
    const TRACE_OFF = 0x0;

    private static $level = self::TRACE_OFF;
    private static $path = null;

    private static $writers = [];

    public static function init($config = []) {
        self::$level = $config['level'] ?? self::TRACE_OFF;
        self::$path = $config['path'] ?? sys_get_temp_dir();
    }

    private static function traceWriter($type) {
        if (($type & self::$level) !== $type) {
            return null;
        }

        if (isset(self::$writers[$type])) {
            return self::$writers[$type];
        }

        static $requestId = null;
        if (is_null($requestId)) {
            $requestId = crc32(serialize([uniqid(), microtime()]));
        }
        $fileFullName = sprintf("%s/%s", self::$path, self::logFileName($type));

        $writer = function() use ($fileFullName, $requestId) {
            $fp = fopen($fileFullName, 'a');
            while (true) {
                $content = sprintf("%s %ld %d %s %s\n",
                        date("y-m-d H:i:s"), posix_getpid(), $requestId, getUserClientIp(),
                        yield
                    );
                fwrite($fp, $content);
            }
            fclose($fp);
        };

        self::$writers[$type] = $writer();

        return self::$writers[$type];
    }

    private static function logFileName($type) {
        $names = [
            self::DEBUG => 'debug',
            self::FATAL => 'fatal',
            self::WARN => 'notice',
            self::NOTICE => 'notice',
            self::VERBOSE => 'verbose',
        ];
        return sprintf("dojet.%s.%s.log", $names[$type] ?? 'unknown', date("Ymd"));
    }

    private static function send($type, $message, $file, $line) {
        if (null !== $file) {
            $content = sprintf("%s\t[%s, %d]", $message, $file, $line);
        } else {
            $content = sprintf("%s", $message);
        }
        $writer = self::traceWriter($type);
        if (!($writer instanceof Generator)) {
            // illegal writer, todo...
            return;
        }
        $writer->send($content);
    }

    public static function debug($message, $file = null, $line = null) {
        self::send(Trace::DEBUG, $message, $file, $line);
    }

    public static function warn($message, $file = null, $line = null) {
        self::send(Trace::WARN, $message, $file, $line);
    }

    public static function notice($message, $file = null, $line = null) {
        self::send(Trace::NOTICE, $message, $file, $line);
    }

    public static function fatal($message, $file = null, $line = null) {
        self::send(Trace::FATAL, $message, $file, $line);
    }

    public static function verbose($message, $file = null, $line = null) {
        self::send(Trace::VERBOSE, $message, $file, $line);
    }

}
