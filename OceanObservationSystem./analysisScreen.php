<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<!--get the username of the user in the system currently-->
<?php
ob_start();
session_start();
$user = $_SESSION['user'];
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <form name="logon" method="post">
         <input class="logoutButton" type="submit" value="Help" name="Help" style="font-size:100%; width:100px; margin:10">
                   <?php 
                   if (isset($_POST['Help'])) { header('Location: userDocumentation.php'); }
                   ?>
        </form>
        <!--check the role of the user - only scientist are allowed to access this section-->
        <?php require_once 'Includes/db.php';
            $role = OceanDB::getInstance()->get_user_role($user);
        ?>

        <!--Bring up Analysis screen if scientist else deny access and display message-->
        <?php if ($role == 's') { ?>
            <h1 align="center" style="font-size: 200%">Data Analysis</h1>
            <?php $allow = true; ?>
        <?php } else { ?>
            <h1 align="left" style="font-size: 175%; color: red">Access denied</h1>
            <?php $allow = false; ?>
        <?php } ?>
        
                    
        <form name="Analysis" method="post">
        <input class="logoutButton" type="submit" value="Home" style="font-size:100%; width:100px; margin:10" name="home">
        
        <!--return to home screen when "home" button is pressed-->
        <?php if (isset($_POST['home'])) { header('Location: homeScreen.php'); }?> 

        <?php if ($allow) { ?>
           
            <?php $sensors = OceanDB::getInstance()->get_subscribed_sensors($user);?>
            
            <table width="250" align="center">
                <tr>
                    <td><div align="left" style="font-size: 125%">Select Sensor:</div></td>
                    <td><div align="left"><select name="sensor"><option value=""></option> <?php while($sen = oci_fetch_array($sensors,OCI_ASSOC)){?>
                                                                                                    <option value="<?php echo $sen['SENSOR_ID']; ?>"<?php if(isset($_POST['show'])&&$_POST['sensor']== $sen["SENSOR_ID"]){?>selected<?php }?>><?php 
                                                                                                        echo $sen["SENSOR_ID"];?>
                                                                                                    </option><?php }?></select></div>
                    </td>                
                </tr>
            </table>

            
            <div align="center">
                <?php if($_REQUEST['show']&&$_POST["sensor"]==""){ echo "<div align='center' style='color:red;'>Select a sensor.</div>";}?>
                <input class="logoutButton" type="submit" value="Show Data" name="show" align="center" style="margin-bottom:0;margin-top:15">
            </div>
            
            <?php if(isset($_REQUEST['show'])&&$_POST["sensor"]!=""){
                $_SESSION['sensor'] = $_POST["sensor"];
                $years = array();
                $quarters = array();
                $months = array();
                $weeks = array();
                $_SESSION["years"] = $years;
                $_SESSION["quarters"] = $quarters;
                $_SESSION["months"] = $months;
                $_SESSION["weeks"] = $weeks;
                header("Location: reportScreen.php");
            } ?>
                
                

        <?php } ?>
        </form>              
                            
    </body>
    <?php   require_once("Includes/css.php");  ?>
</html>

