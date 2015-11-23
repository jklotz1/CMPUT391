<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
ob_start();
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        
        <!--allows access to OceanDB class to connect and query database-->
        <?php require_once 'Includes/db.php'; ?>
        
        <!--Display the management screen for sensors-->
        <h1 align="left" style="font-size: 175%">Sensor and User Management Center</h1> 
        <form name="sensorManagement" method="post">
            <!--user action- "home" to return to the home screen, "back" to return to the main management screen-->
            <input class="logoutButton" type="submit" value="Back" name="back" style="font-size:100%; width:100px; margin:10 ">
            <input class="logoutButton" type="submit" value="Home" name="home" style="font-size:100%; width:100px; margin:10 ">
            <br><br>

            <!--return to home screen when "home" button is pressed-->
            <?php if (isset($_POST['home'])) { header('Location: homeScreen.php'); }?> 
            <!--return to the management screen-->
            <?php if (isset($_POST['back'])) { header('Location: managementScreen.php'); }?> 

            <!--display sensor management screen - buttons for the different user actions-->
            <!--"create new sensor" button to create a new sensor, "delete sensor" to delete a sensor-->
            <div align="center">
            <h1 align="center" style="font-size: 150%">Sensors</h1>
            <input class="logoutButton" type="submit" value="Create New Sensor" name="newSensor" style="font-size:100%; width:200px; margin:10 ">
            <input class="logoutButton" type="submit" value="Delete Sensor" name="deleteSensor" style="font-size:100%; width:200px; margin:10 ">
            
            <!--if "delete" button is clicked a sensor must be selected or a message will be displayed-->
            <p style="color:red;display:<?php if(isset($_POST['deleteSensor'])&&$_POST['sensorSelected']==''){?>inline <?php } else { ?> none <?php } ?>"><br>Please select a sensor to delete</p>
            <br><br>
            </div>
            
            <!-- get the current sensors -->
            <?php $objParse = OceanDB::getInstance()->get_sensors(); ?>

            <!--display current sensors in the system-->
            <table class="searchTable" width="700" border="1">
                <tr>
                    <td width="25"> <div align="center"> </div></td>    
                    <td width="91"> <div align="center">Sensor ID </div></td>
                    <td width="98"> <div align="center">Location </div></td>
                    <td width="198"> <div align="center">Sensor Type </div></td>
                    <td width="97"> <div align="center">Description </div></td>
                </tr>
                <?php while($objResult = oci_fetch_array($objParse,OCI_BOTH)){ ?>
                    <tr>
                    <td><div align="center"><input type="radio" name="sensorSelected" value="<?php echo $objResult["SENSOR_ID"];?>"></div></td>   
                    <td><div align="center"><?php echo $objResult["SENSOR_ID"];?></div></td>
                    <td><div align="center"><?php echo $objResult["LOCATION"];?></td>
                    <td><div align="center"><?php echo OceanDB::getInstance()->get_sensors_type($objResult["SENSOR_TYPE"])?></div></td>
                    <td><div align="center"><?php echo $objResult["DESCRIPTION"];?></div></td>
                    </tr>
                <?php } ?>
            </table>
           
            <!--"create new sensor" button is selected - user is taken to a different page to create -->
            <?php if (isset($_POST['newSensor'])) { header('Location: createNewSensor.php'); } ?>

            <!--"delete sensor" if a sensor is selected the sensor is deleted-->
            <?php if (isset($_POST['deleteSensor'])&&$_POST['sensorSelected']!='') { OceanDB::getInstance()->delete_sensor($_POST['sensorSelected']);header('Location: managementSensorScreen.php'); } ?>
            
        </form> 
    </body>
    <!--used for graphical interface-->
    <?php   require_once("Includes/css.php");  ?>
</html>

