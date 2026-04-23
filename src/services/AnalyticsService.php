<?php
/**
 * AnalyticsService - Tinybird Integration for Real-time Insights
 */

class AnalyticsService {
    private static $token = ''; // Tinybird Auth Token
    private static $base_url = 'https://api.tinybird.co/v0/events?name=';

    public static function logEvent($name, $data) {
        if (empty(self::$token)) {
            // Local simulation for demo
            $log = date('Y-m-d H:i:s') . " | ANALYTICS EVENT [$name] | DATA: " . json_encode($data) . "\n";
            file_put_contents(dirname(__DIR__, 2) . '/logs/analytics.log', $log, FILE_APPEND);
            return true;
        }

        $ch = curl_init(self::$base_url . $name);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . self::$token,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
?>
