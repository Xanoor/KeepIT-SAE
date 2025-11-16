

<?php 

require_once '../fragments/db.php';

session_start();

if (isset($_POST["submit"], $_POST["uid_email"], $_POST["password"])) {
    $login = $_POST["uid_email"];
    $password = $_POST["password"];

    $request_user = "SELECT * FROM users WHERE uid=? OR email=?";
    $request_prepare = mysqli_prepare($connect, $request_user);
    mysqli_stmt_bind_param($request_prepare, "ss", $login, $login);
    mysqli_stmt_execute($request_prepare);

    $res = mysqli_stmt_get_result($request_prepare);
    if (mysqli_num_rows($res) === 1) {
        $user = mysqli_fetch_assoc($res);
        
        if (password_verify($password, $user["password_hash"])) {
            $_SESSION["uid"] = $user["uid"];
            $_SESSION["name"] = $user["first_name"] . " " . $user["last_name"];
            $_SESSION["role"] = $user["role"];
            $_SESSION['last_activity'] = time();

            $requst_set_last_activity = "UPDATE users SET last_login_at = NOW() WHERE uid = ? OR email = ?";
            $stmt_set_last_activity = mysqli_prepare($connect, $requst_set_last_activity);
            mysqli_stmt_bind_param($stmt_set_last_activity, "ss", $login, $login);
            mysqli_stmt_execute($stmt_set_last_activity);

            header("location: ../pages/index.php?message=Connexion successful.");
            exit();
        } else {
            header("location: ../pages/login.php?message=Invalid password or uid.");
            exit();   
        }
    } else {
        header("location: ../pages/login.php?message=Invalid password or uid.");
        exit();
    }

}



?>