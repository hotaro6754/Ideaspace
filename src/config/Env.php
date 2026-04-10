<?php
/**
 * Environment Configuration Loader
 * Loads environment variables from .env file
 * File: /src/config/Env.php
 */

class Env {
    private static $variables = [];
    private static $loaded = false;

    /**
     * Load environment variables from .env file
     */
    public static function load($filepath = null) {
        if (self::$loaded) {
            return;
        }

        if ($filepath === null) {
            $filepath = dirname(__DIR__, 2) . '/.env';
        }

        // Also load from system environment variables first
        if (function_exists('putenv')) {
            // System env vars take precedence
        }

        // Then load from .env file if it exists
        if (file_exists($filepath)) {
            $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                // Parse KEY=VALUE
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);

                    // Remove quotes if present
                    if ((strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) ||
                        (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1)) {
                        $value = substr($value, 1, -1);
                    }

                    self::$variables[$key] = $value;

                    // Also set in putenv for getenv()
                    if (!getenv($key)) {
                        putenv($key . '=' . $value);
                    }
                }
            }
        }

        self::$loaded = true;
    }

    /**
     * Get an environment variable
     *
     * @param string $key The variable key
     * @param mixed $default The default value if not found
     * @return mixed
     */
    public static function get($key, $default = null) {
        // First try getenv (system environment variables)
        $value = getenv($key);

        if ($value !== false) {
            return $value;
        }

        // Then try loaded variables
        if (isset(self::$variables[$key])) {
            return self::$variables[$key];
        }

        // Return default
        return $default;
    }

    /**
     * Check if environment variable exists
     *
     * @param string $key
     * @return bool
     */
    public static function has($key) {
        return getenv($key) !== false || isset(self::$variables[$key]);
    }

    /**
     * Get a required environment variable or throw exception
     *
     * @param string $key
     * @return string
     * @throws Exception
     */
    public static function required($key) {
        $value = self::get($key);

        if ($value === null) {
            throw new Exception("Required environment variable not found: $key");
        }

        return $value;
    }
}

// Auto-load on include
Env::load();
?>
