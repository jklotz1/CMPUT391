<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<!--get the username of the user in the system currently-->
<?php
session_start();
$user = $_SESSION['user'];
$allow = false;
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <!--help button to display user documentation on how to use the system-->
         <form name="logon" method="post">
         <input class="logoutButton" type="submit" value="Help" name="Help">
            <?php 
            if (isset($_POST['Help'])) { header('Location: userDocumentation.php'); }
            ?>
        </form>
        
        <!--check the role of the user - only administrators are allowed to access this section-->
        <?php  
            require_once 'Includes/db.php';
            $role = OceanDB::getInstance()->get_user_role($user);
        ?>
        
        <!--Bring up Management screen if administrator else deny access and display message-->
        <?php if ($role == 'a') { ?>
            <h1 align="left" style="font-size: 175%">Sensor and User Management Center</h1>
            <?php $allow = true; ?>
        <?php } else { ?>
            <h1 align="left" style="font-size: 175%; color: red">Access denied</h1>
            <?php $allow = false; ?>
        <?php } ?>
         
            <!--user action - "home" to return to home screen, "sensors" to manage sensors, "users" to manage users-->
            <form name="management" method="post">
                <input class="logoutButton" type="submit" value="Home" style="font-size:100%; width:200px; margin:10" name="home"> Return to Home Screen
                <br><br>
                <!--return to home screen when "home" button is pressed-->
                <!--this will be displayed for all users-->
                <?php if (isset($_POST['home'])) { header('Location: homeScreen.php'); }?> 
                
                <?php if ($allow) { ?>
                    <!--displayed only for allowed users - administrators-->
                    <input class="logoutButton" type="submit" value="Sensors" style="font-size:100%; width:200px; margin:10 " name="sensorsEdit"> Create and Delete Sensors<br>
                    <input class="logoutButton" type="submit" value="Users" style="font-size:100%; width:200px; margin:10 " name="usersEdit"> Create, Edit and Delete Users and Person Accounts
                    <br><br>

                    <!--navigate to the correct screen for either user or sensor management-->
                    <?php if (isset($_POST['sensorsEdit'])) { header('Location: managementSensorScreen.php'); }?>  
                    <?php if (isset($_POST['usersEdit'])) { header('Location: managementUserScreen.php'); }?>  
                <?php } ?>
                    
            </form>            
    </body>
    <!--used for graphical interface-->
    <?php   require_once("Includes/css.php");  ?>
</html>
