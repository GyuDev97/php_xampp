<?php
require_once __DIR__ . '/../lib/community.php';
init_app();
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    fail_json('post id required', 400);
}
json_response(fetch_post($id));
