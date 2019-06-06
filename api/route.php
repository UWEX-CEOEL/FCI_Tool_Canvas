<?php

require_once('../config.php');

// Make PHP paths pretty .../install => install.php
$router = new Tsugi\Util\FileRouter();
$file = $router->fileCheck();
if ( $file ) {
    require_once($file);
    return;
}

// Add 404 Handling
http_response_code(404);
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();
echo("<h2>Page not found.</h2>\n");
$OUTPUT->footer();
