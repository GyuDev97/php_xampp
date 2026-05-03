<?php
require_once __DIR__ . '/../lib/community.php';
init_app();
json_response(generate_ai_comment());
