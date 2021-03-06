<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <!--help button that will display user documentation to use the system-->
        <form name="logon" method="post">
         <input class="logoutButton" type="submit" value="Help" name="Help">
                   <?php 
                   if (isset($_POST['Help'])) { header('Location: userDocumentation.php'); }
                   ?>
        </form>
        
        <!--Display the login screen-->
        <!--prompt for username and password-->
        <h1 align="center" style="font-size: 200%">Welcome to The Ocean Observation System</h1>
        <br><br><br>
        <form name="logon" method="post">
            <table class="searchTable">  
                <tr> 
                <th>
                    <br>
                    <span style='color:crimson;'>Username: </span><input type="text" name="user" value="<?php echo (isset($_POST['logon']) or isset($_REQUEST['person'])) ? $_POST['user'] : '' ?>">    
                <?php   
                    if (isset($_REQUEST['logon']) or isset($_REQUEST['person'])){
                        if($_POST["user"] == "")
                        {
                            echo "<p style='color:red;'>Please enter a username<p>";
                           
                        } else { echo "<br> <br> <br>"; }
                    } else { echo "<br> <br> <br>"; } 
                ?>
                
                <span style='color:crimson;'>Password: </span><input type="password" name="userpassword">
                <?php   
                    if (isset($_REQUEST['logon']) or isset($_REQUEST['person'])){
                        if($_POST["userpassword"] == "")
                        {
                            echo "<p style='color:red;'>Please enter a password<p>";
                        } else { echo "<br> <br> <br>"; }
                    } else { echo "<br> <br> <br>"; }
                ?>     
                
                <!--user action - "log on" to the system, "account" to edit the users personal/user account information-->
                <input class="logoutButton" type="submit" value="Log On" name="logon">
                <input class="logoutButton" type="submit" value="Account" name="person">
                
                <?php
                //access to OceanDB class to connect and query the database
                require_once("Includes/db.php");
                //check that username and password are entered
                if (isset($_REQUEST['logon']) or isset($_REQUEST['person'])){
                    if ($_POST["user"]!="" && $_POST["userpassword"]!=""){
                        //check if the username are password are vaild
                        //if valid allow the user action to the system or edit account
                        $valid = OceanDB::getInstance()->is_valid_login($_POST["user"],$_POST["userpassword"]);
                        if($valid)
                        {
                            session_start();
                            $_SESSION['user'] = $_POST["user"];
                            $_SESSION['screen'] = "Logon";
                            if (isset($_REQUEST['logon'])) { header('Location: homeScreen.php'); }
                            if (isset($_REQUEST['person'])) { header('Location: personalAccount.php'); }
                            exit();
                        }
                        else
                            echo "<br><p style='color:red;'>Incorrect username and/or password<p>"; 
                    }
                }
                ?>
                </th>
                </tr>
            </table>            
        </form>
    </body>
    <!--used for graphical interface-->
    <?php   require_once("Includes/css.php");  ?>
</html>
