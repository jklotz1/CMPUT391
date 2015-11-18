<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <h1 align="left" style="font-size: 150%">Subscription Navigation </h1> 
    </head>
    <body>
        <!-- Navigation ToolBar -->
    <form name="subscriptionNavigation" method="post">
   
               <input class="logoutButton" type="submit" value="Home" style="font-size:100%; width:200px; margin:10 " name="home"> Return to Home Screen
                <br><br>
                <input class="logoutButton" type="submit" value="Subscriptions(s)" style="font-size:100%; width:200px; margin:10 " name="subscriptions"> See Current Subscriptions<br>
                <input class="logoutButton" type="submit" value="Available Subscription(s)" style="font-size:100%; width:200px; margin:10 " name="list"> See All Avaiable Subscriptions
                <br><br>

                <!--return to home screen when "home" button is pressed-->
                <?php if (isset($_POST['home'])) { header('Location: homeScreen.php'); }?> 

                <?php if (isset($_POST['subscriptions'])) { header('Location: sensorSubscriptions.php'); }?>  
                <?php if (isset($_POST['list'])) { header('Location: availableSensorSubscriptions.php'); }?>  
 

    </form>
    </body>
    <?php   require_once("Includes/css.php");  ?>
</html>
