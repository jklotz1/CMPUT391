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
            <input type="submit" value="Back" name="back">
            <br><br>

            <!--return to home screen when "home" button is pressed-->
            <?php if (isset($_POST['back'])) { header('Location: managementScreen.php'); }?> 

            <!--display sensor management screen - buttons for the different actions-->

            <h1 align="left" style="font-size: 150%">Sensors</h1>
            <input type="submit" value="Create New Sensor" name="newSensor">
            <input type="submit" value="Delete Sensor" name="deleteSensor">
            <br><br>
            
            <!-- get the current sensors -->
            <?php $objParse = OceanDB::getInstance()->get_sensors(); ?>

            <!--display current sensors in the system-->
            <table width="700" border="1">
                <tr>
                <th width="25"> <div align="center"> </div></th>    
                <th width="91"> <div align="center">Sensor ID </div></th>
                <th width="98"> <div align="center">Location </div></th>
                <th width="198"> <div align="center">Sensor Type </div></th>
                <th width="97"> <div align="center">Description </div></th>
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

            <?php if (isset($_POST['deleteSensor'])) { OceanDB::getInstance()->delete_sensor($_POST['sensorSelected']);header('Location: managementSensorScreen.php'); } ?>
            
        </form> 
    </body>
</html>

