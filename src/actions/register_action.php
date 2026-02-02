

<?php 
require_once '../includes/functions.php';
require_once '../includes/db.php';

session_start();

if (isset($_POST["submit"], $_POST["login"], $_POST["email"], $_POST["password"], $_POST["first_name"], $_POST["last_name"])) {

    // ADD REGISTER VERIFICATION (email, password, ...)

    $login = $_POST["login"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];

    ////////////////// LOGIN VERIFICATION //////////////////

    $request_login_check = "SELECT login FROM users WHERE login = ?";
    $stmt_login_check = mysqli_prepare($connect, $request_login_check);
    mysqli_stmt_bind_param($stmt_login_check, "s", $login);
    mysqli_stmt_execute($stmt_login_check);
    $res_login = mysqli_stmt_get_result($stmt_login_check);

    if (mysqli_num_rows($res_login) > 0) {
        header("location: ../pages/register.php?message=Login already used");
        exit();
    }

    ////////////////// INSERTION //////////////////

    $request_user = "INSERT INTO users (login, email, password_hash, first_name, last_name) VALUES (?, ?, ?, ?, ?)";
    $request_prepare = mysqli_prepare($connect, $request_user);
    mysqli_stmt_bind_param($request_prepare, "sssss", $login, $email, $password, $first_name, $last_name);
    mysqli_stmt_execute($request_prepare);

    $affected_rows = mysqli_stmt_affected_rows($request_prepare);
    if ($affected_rows > 0) {
        header("location: ../pages/login.php?message=Account created.");
        exit();
    }
        
    header("location: ../pages/register.php?message=Invalid data.");
    exit();
}

?>