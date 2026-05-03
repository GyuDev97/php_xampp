<?php
require_once __DIR__ . '/../lib/community.php';
init_app();
maybe_run_auto_activity();
json_response(fetch_posts());
