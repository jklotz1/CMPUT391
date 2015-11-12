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
        <h1 align="center" style="font-size: 200%">Welcome to The Ocean Observation System</h1>
        <form name="logon" method="post">
            <table width="300" border="1" align="center" cellpadding="25">  
                <tr> 
                <th>
                    Username: <input type="text" name="user" value="<?php echo isset($_POST['pressed']) ? $_POST['user'] : '' ?>">    
                <?php   
                    if (isset($_REQUEST['pressed'])){
                        if($_POST["user"] == "")
                        {
                            echo "<p style='color:red;'>Please enter a username<p>";
                           
                        } else { echo "<br> <br> <br>"; }
                    } else { echo "<br> <br> <br>"; } 
                ?>
                
                Password: <input type="password" name="userpassword">
                <?php   
                    if (isset($_REQUEST['pressed'])){
                        if($_POST["userpassword"] == "")
                        {
                            echo "<p style='color:red;'>Please enter a password<p>";
                        } else { echo "<br> <br> <br>"; }
                    } else { echo "<br> <br> <br>"; }
                ?>                
                <input type="submit" value="Log On" name="pressed">
                <?php
                require_once("Includes/db.php");
                if (isset($_REQUEST['pressed'])){
                    if ($_POST["user"]!="" && $_POST["userpassword"]!=""){
                        $valid = OceanDB::getInstance()->is_valid_login($_POST["user"],$_POST["userpassword"]);
                        if($valid)
                        {
                            session_start();
                            $_SESSION['user'] = $_POST["user"];
                            header('Location: homeScreen.php');
                            //header('Location: managementScreen.php');
                            exit();
                        }
                        else
                            echo "<p style='color:red;'>Incorrect username and/or password<p>"; 
                    }
                }
                ?>
                </th>
                </tr>
            </table>            
        </form>
    </body>
</html>
