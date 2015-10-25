<?php
$logonSuccess = false;
$userIsEmpty = false;					
$passwordIsEmpty = false;
$user = '';
$password = '';

// verify user's credentials
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    
    /** Check whether the user has filled in text field "user" and "password" */
    if ($_POST['user'] == "") {
        $userIsEmpty = true;
        echo 'Please enter a username </br>';
    } else {
        $user = $_POST['user'];
    }
   
    if ($_POST['userpassword']==""){
        $passwordIsEmpty = true;
        echo 'Please enter a password </br>';
    } else {
        $password = $_POST['userpassword'];
    }
    if (!$userIsEmpty && !$passwordIsEmpty)
    {
        /** Create database connection */
        $conn = oci_connect('sjpartri', 'letmein22');
        if (!$conn) {
            $m = oci_error();
            exit('Connect Error' . $m['message']);
        }

        /** Create sql command */
        $sql = "SELECT * FROM SJPARTRI.USERS WHERE user_name = '$user' AND password = '$password'";
        //Prepare sql using conn and returns the statement identifier
        $stid = oci_parse($conn, $sql);
        //Execute a statement returned from oci_parse()
        $res = oci_execute($stid);
        
        $count = 0;
        while (($row = oci_fetch_array($stid, OCI_ASSOC)) != false) {
            $count++;
         }
         
         if ($count > 0) { $logonSuccess = true; }
                  
    }
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <form name="logon" action="loginScreen.php" method="POST" >
           Username: <input type="text" name="user">    
           <br> <br>
           Password: <input type="password" name="userpassword">
           <br> <br>
        <input type="submit" value="Log On">
        </form>
    </body>
</html>
