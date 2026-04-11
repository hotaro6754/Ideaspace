<?php
/**
 * Health Check Endpoint
 * Responds with 200 OK for Railway health checks
 * Does not require database connection
 */

// Simple response for health checks
header("Content-Type: text/plain");
http_response_code(200);
echo "OK";
exit(0);
?>
