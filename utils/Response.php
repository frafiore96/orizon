<?php
// utils/Response.php

class Response {
    
    public static function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    public static function success($data = null, $message = null, $statusCode = 200) {
        $response = ['success' => true];
        
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        self::json($response, $statusCode);
    }

    public static function error($message, $statusCode = 400, $errors = null) {
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        self::json($response, $statusCode);
    }

    public static function notFound($message = 'Resource not found') {
        self::error($message, 404);
    }

    public static function badRequest($message = 'Invalid request') {
        self::error($message, 400);
    }

    public static function created($data, $message = 'Resource created successfully') {
        self::success($data, $message, 201);
    }

    public static function noContent() {
        http_response_code(204);
        exit();
    }

    public static function methodNotAllowed() {
        self::error('HTTP method not allowed', 405);
    }
}
