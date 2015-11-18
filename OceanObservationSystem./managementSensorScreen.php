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
        
        <!--check the role of the user - only administrators are allowed to access this section-->
        <?php require_once 'Includes/db.php'; ?>
        
        <h1 align="left" style="font-size: 175%">Sensor and User Management Center</h1> 
        <form name="sensorManagement" method="post">
            <input class="logoutButton" type="submit" value="Back" name="back" style="font-size:100%; width:100px; margin:10 ">
            <input class="logoutButton" type="submit" value="Home" name="home" style="font-size:100%; width:100px; margin:10 ">
            <br><br>

            <!--return to home screen when "home" button is pressed-->
            <?php if (isset($_POST['home'])) { header('Location: homeScreen.php'); }?> 
            <?php if (isset($_POST['back'])) { header('Location: managementScreen.php'); }?> 

            <!--display sensor management screen - buttons for the different actions-->
            <div align="center">
            <h1 align="center" style="font-size: 150%">Sensors</h1>
            <input class="logoutButton" type="submit" value="Create New Sensor" name="newSensor" style="font-size:100%; width:200px; margin:10 ">
            <input class="logoutButton" type="submit" value="Delete Sensor" name="deleteSensor" style="font-size:100%; width:200px; margin:10 ">
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
           
            
            <?php if (isset($_POST['newSensor'])) { header('Location: createNewSensor.php'); } ?>

            <?php if (isset($_POST['deleteSensor'])&&$_POST['sensorSelected']!='') { OceanDB::getInstance()->delete_sensor($_POST['sensorSelected']);header('Location: managementSensorScreen.php'); } ?>
            
        </form> 
    </body>
    <?php   require_once("Includes/css.php");  ?>
</html>

