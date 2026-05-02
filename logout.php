<?php
require_once __DIR__ . '/config/session.php';

// Destroy the session
session_unset();
session_destroy();

// Redirect to login
header("Location: " . base_url("/login.php"));
exit();
