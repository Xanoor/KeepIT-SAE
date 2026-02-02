<?php
require_once '../includes/db.php';

session_start();

if (isset($_POST["submit"], $_POST["login"], $_POST["password"])) {
    $login = $_POST["login"];
    $password = $_POST["password"];

    if (empty($login) || empty($password)) {
        header("location: ../pages/login.php?error=1"); // Missing fields
        exit();
    }

    $request_user = "SELECT * FROM users WHERE login=?";
    $request_prepare = mysqli_prepare($GLOBALS['connect'], $request_user);
    mysqli_stmt_bind_param($request_prepare, "s", $login);
    mysqli_stmt_execute($request_prepare);

    $res = mysqli_stmt_get_result($request_prepare);

    var_dump($res);

    if (mysqli_num_rows($res) === 1) {
        $user = mysqli_fetch_assoc($res);

        var_dump($user);
        
        if (password_verify($password, $user["password_hash"])) {

            // Setup login session
            $_SESSION["login"] = $user["login"];
            $_SESSION["name"] = $user["first_name"] . " " . $user["last_name"];
            $_SESSION["role"] = $user["role"];
            $_SESSION['last_activity'] = time();

            // Store last activity
            $request_set_last_activity = "UPDATE users SET last_login_at = NOW() WHERE login = ?";
            $stmt_set_last_activity = mysqli_prepare($GLOBALS['connect'], $request_set_last_activity);
            mysqli_stmt_bind_param($stmt_set_last_activity, "s", $login);
            mysqli_stmt_execute($stmt_set_last_activity);

            header("location: ../pages/index.php");
            exit();
        }
    }

    header("location: ../pages/login.php?error=2"); // Wrong credentials
} else {
    header("location: ../pages/login.php?error=1"); // Missing fields
}

exit();