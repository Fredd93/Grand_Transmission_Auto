<?php
class ResponseHelper {
    public static function sendJson($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function sendError($message, $statusCode = 400) {
        self::sendJson(['error' => $message], $statusCode);
    }
}
