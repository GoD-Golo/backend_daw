<?php
function validateInput($data, $fields) {
    foreach ($fields as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(['message' => "Field '$field' is required"]);
            exit;
        }
    }
}
?>
