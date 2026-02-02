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