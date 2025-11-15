

<?php 
session_start();

$connect = mysqli_connect("localhost", "root", "");
$db = mysqli_select_db($connect, "IMT_database");

if ($db) {
    print_r($connect);

    print_r(isset($_POST["uid"]));
    if (isset($_POST["submit"], $_POST["uid"], $_POST["password"])) {
        $uid = $_POST["uid"];
        $password = $_POST["password"];

        $request_user = "SELECT * FROM users WHERE uid=?";
        $request_prepare = mysqli_prepare($connect, $request_user);
        mysqli_stmt_bind_param($request_prepare, "s", $uid);
        mysqli_stmt_execute($request_prepare);

        $res = mysqli_stmt_get_result($request_prepare);
        if (mysqli_num_rows($res) === 1) {
            $user = mysqli_fetch_assoc($res);
            
            if (password_verify($password, $user["password_hash"])) {
                $_SESSION["uid"] = $user["uid"];
                $_SESSION["name"] = $user["first_name"] . " " . $user["last_name"];
                $_SESSION["role"] = $user["role"];
                $_SESSION['last_activity'] = time();
                
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
} else {
    //error conn
}



?>