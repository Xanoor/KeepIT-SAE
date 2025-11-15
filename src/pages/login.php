<!DOCTYPE html> 
<html>
<!-- <?php include 'header.php'; ?> -->
<?php include '../fragments/functions.php'; ?>
<?php


session_start();

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}


?>

<body>
    <main>
        <form action='../actions/login_action.php' method='POST'>
            <input type='text' name="uid" placeholder='User id'/>
            <input type='password' name="password" placeholder='Password'/>
            <input type='submit' name="submit" value="Login"/>
        </form>
    </main>
</body>
</html>