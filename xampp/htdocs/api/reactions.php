<?php
require_once __DIR__ . '/../lib/community.php';
init_app();
$postId = (int) ($_GET['post_id'] ?? 0);
if ($postId <= 0) {
    fail_json('post_id required', 400);
}
$body = read_json_body();
json_response(add_reaction($postId, (string) ($body['kind'] ?? '')));
