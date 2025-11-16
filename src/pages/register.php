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
        <form action='../actions/register_action.php' method='POST'>
            <input type='text' name="login" placeholder='Login'/>
            <input type='email' name="email" placeholder='Email'/>
            <input type='password' name="password" placeholder='Password'/>
            <input type='text' name="first_name" placeholder='First name'/>
            <input type='text' name="last_name" placeholder='Last name'/>
            <input type='submit' name="submit" value="Register"/>
        </form>
    </main>
</body>
</html>