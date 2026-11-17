<?php

require_once 'db.php';

require_post();

$_SESSION = [];

session_destroy();

respond(true, 'Logout successful');