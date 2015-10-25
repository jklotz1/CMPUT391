<?php
$logonSuccess = false;

// verify user's credentials
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $logonSuccess = (WishDB::getInstance()->verify_wisher_credentials($_POST['user'], $_POST['userpassword']));
    if ($logonSuccess == true) {
        session_start();
        $_SESSION['user'] = $_POST['user'];
        header('Location: editWishList.php');
        exit;
    }
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <A HREF="homeScreen.php">Home Screen</A>
        <form name="logon" action="index.php" method="POST" >
           Username: <input type="text" name="user">                                                                                                                                    
           Password  <input type="password" name="userpassword">
        <input type="submit" value="Log On">
        </form>
        <a href="createNewUser.php">Create now</a>
    </body>
</html>







