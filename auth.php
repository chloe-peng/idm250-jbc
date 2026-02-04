<?php 
    function check_api_key(array $env) {
        $valid_key = $env['X_API_KEY'];
        $provided_key = null;
        $headers = getallheaders();

        foreach ($headers as $header => $value) {
            if (strtolower($header) === 'x-api-key') {
                $provided_key = $value;
                break;
            }
        }

        if ($provided_key !== $valid_key) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized: Invalid API Key']);
            exit;
        }
    }
?>