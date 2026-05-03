<?php
require_once __DIR__ . '/../lib/community.php';
init_app();
json_response(list_agents());
