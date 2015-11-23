<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
CMPUT 391 Project
Code Belongs to: Sean Partridge, Jessica Klotz, Brennan Stang
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Subscription Navigation</title>
        <h1 align="left" style="font-size: 150%">Subscription Navigation </h1> 
    </head>
    <body>
        
    <!-- Navigation Panel -->
         <form name="logon" method="post">
         <input class="logoutButton" type="submit" value="Help" name="Help">
                   <?php 
                   if (isset($_POST['Help'])) { header('Location: userDocumentation.php'); }
                   ?>
        </form>
              <!--check the role of the user - only administrators are allowed to access this section-->
        <?php require_once 'Includes/db.php';
            session_start();
            $user = $_SESSION['user'];
            $role = OceanDB::getInstance()->get_user_role($user);
        ?>
        
        <!--Bring up Management screen if administrator else deny access and display message-->
        <?php if ($role == 's') { ?>
           
            <?php $allow = true; ?>
        <?php } else { ?>
            <h1 align="left" style="font-size: 175%; color: red">Access denied: Not a Scientist</h1>
            <?php $allow = false; ?>
        <?php } ?>
            
        <!-- Navigation ToolBar -->
    <form name="subscriptionNavigation" method="post">
   
               <input class="logoutButton" type="submit" value="Home" style="font-size:100%; width:200px; margin:10 " name="home"> Return to Home Screen
                <br><br>
                       <?php if ($allow) { ?>
                <input class="logoutButton" type="submit" value="Subscriptions(s)" style="font-size:100%; width:200px; margin:10 " name="subscriptions"> See Current Subscriptions<br>
                <input class="logoutButton" type="submit" value="Available Subscription(s)" style="font-size:100%; width:200px; margin:10 " name="list"> See All Avaiable Subscriptions
                <br><br>
                 <?php  } ?>
                <!--return to home screen when "home" button is pressed-->
                <?php if (isset($_POST['home'])) { header('Location: homeScreen.php'); }?> 

                <?php if (isset($_POST['subscriptions'])) { header('Location: sensorSubscriptions.php'); }?>  
                <?php if (isset($_POST['list'])) { header('Location: availableSensorSubscriptions.php'); }?>  
 

    
                       <?php   require_once("Includes/css.php");  ?>
        </form>
    </body>
</html>
