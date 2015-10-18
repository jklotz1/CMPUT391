<?php

$dbHost="gwynne.cs.ualberta.ca:1521:crs"; 
$dbUsername="sjpartri";
$dbPassword="letmein22";

/** other variables */
$userNameIsUnique = true;
$passwordIsValid = true;				
$userIsEmpty = false;					
$passwordIsEmpty = false;				
$password2IsEmpty = false;	

/** Check that the page was requested from itself via the POST method. */
if ($_SERVER['REQUEST_METHOD'] == "POST") {

/** Check whether the user has filled in the wisher's name in the text field "user" */
    if ($_POST['user'] == "") {
        $userIsEmpty = true;
    }

    /** Create database connection */

    $con = oci_connect('sjpartri', 'letmein22');
    if (!$con) {
        $m = oci_error();
        exit('Connect Error' . $m['message']);

    }
}


?>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
      Welcome!<br>
        <form action="createNewUser.php" method="POST">
            Your name: <input type="text" name="user"/><br/>
            Password: <input type="password" name="password"/><br/>
            Please confirm your password: <input type="password" name="password2"/><br/>
            <input type="submit" value="Register"/>
        </form>
     </body>
</html>
