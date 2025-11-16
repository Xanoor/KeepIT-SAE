

<?php 
require_once '../fragments/functions.php';
require_once '../fragments/db.php';

session_start();

if (isset($_POST["submit"], $_POST["email"], $_POST["password"], $_POST["first_name"], $_POST["last_name"])) {

    // ADD REGISTER VERIFICATION (email, password, ...)

    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];

    ////////////////// EMAIL VERIFICATION //////////////////
    $request_email_check = "SELECT email FROM users WHERE email = ?";
    $stmt_email_check = mysqli_prepare($connect, $request_email_check);
    mysqli_stmt_bind_param($stmt_email_check, "s", $email);
    mysqli_stmt_execute($stmt_email_check);
    $res_email = mysqli_stmt_get_result($stmt_email_check);

    if (mysqli_num_rows($res_email) > 0) {
        header("location: ../pages/register.php?message=Email already used");
        exit();
    }

    ////////////////// UID VERIFICATION //////////////////

    $max_attempts = 100;
    $is_uid_unique = false;

    $request_uid = "SELECT uid FROM users WHERE uid=?";
    $request_prepare = mysqli_prepare($connect, $request_uid);
    for ($i = 0; $i < $max_attempts; $i++) {
        $uid = generateCustomUid();

        mysqli_stmt_bind_param($request_prepare, "s", $uid);
        mysqli_stmt_execute($request_prepare);

        $res = mysqli_stmt_get_result($request_prepare);
        if (mysqli_num_rows($res) == 0) {
            $is_uid_unique = true;
            break;
        }
    }

    if (!$is_uid_unique) {
        header("location: ../pages/register.php?message=UID generation failed");
        exit();
    }

    ////////////////// INSERTION //////////////////

    $request_user = "INSERT INTO users (uid, email, password_hash, first_name, last_name) VALUES (?, ?, ?, ?, ?)";
    $request_prepare = mysqli_prepare($connect, $request_user);
    mysqli_stmt_bind_param($request_prepare, "sssss", $uid, $email, $password, $first_name, $last_name);
    mysqli_stmt_execute($request_prepare);

    $affected_rows = mysqli_stmt_affected_rows($request_prepare);
    if ($affected_rows > 0) {
        header("location: ../pages/login.php");
        exit();
    }
        
    header("location: ../pages/register.php?message=Invalid data.");
    exit();
}

?>