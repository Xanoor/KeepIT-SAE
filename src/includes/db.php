<?php

function create_connection() {
    if (!isset($GLOBALS['connect'])) {
        $GLOBALS['connect'] = mysqli_connect("localhost", "root", "", "keepit");
        mysqli_set_charset($GLOBALS['connect'], "utf8");

        // Check connection
        if (!$GLOBALS['connect']) {
            die("Database connection failed.");
        }
    }
}

create_connection();

// If the database connection is recreated, we need to re-identify the user logged-in.
// So we set the user login again in the @current_user MySQL session variable.
// variables starting with @ in MySQL are session variables
session_start();

if (isset($_SESSION['login'])) {
    $login_safe = mysqli_real_escape_string(
        $GLOBALS['connect'],
        $_SESSION['login']
    );

    mysqli_query(
        $GLOBALS['connect'],
        "SET @current_user = '$login_safe'"
    );
}