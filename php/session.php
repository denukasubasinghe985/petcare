<?php

require_once 'db.php';

respond(true, 'Session loaded', [

    'logged_in' => isset($_SESSION['user_id']),

    'name' => $_SESSION['name'] ?? '',

    'csrf' => csrf_token()

]);