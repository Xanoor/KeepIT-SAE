<?php
include '../fragments/functions.php';

session_start();

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>

<!-- <?php include 'header.php'; ?> -->

<body>
    <main>
        <?php echo icon('accessibility'); ?>
        <form action='../actions/login_action.php' method='POST'>
            <input type='text' name="login_email" placeholder='Email or login'/>
            <input type='password' name="password" placeholder='Password'/>
            <input type='submit' name="submit" value="Login"/>
        </form>
    </main>
</body>
</html>