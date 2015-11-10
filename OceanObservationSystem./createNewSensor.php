<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
$sensorID = '';
$location = '';
$type = '';
$description = '';
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php require_once 'Includes/db.php'; ?>
        <?php $sensorID = OceanDB::getInstance()->get_next_sensorID();?> 
        
        <h1 align="center" style="font-size: 175%">Add New Sensor</h1>        
        <form name="sensorCreate" method="post">
            <table width="450" align="center" cellpadding="5">
                <tr>
                    <td><div align="left">Sensor ID:</div></td>
                    <td><div align="left"><input type="text" name="sensorID" value="<?php echo $sensorID ?>" readonly></div></td>
                </tr>
                <tr>
                    <td><div align="left">Location:</div></td>
                    <td><div align="left"><input type="text" name="location" maxlength="64" value="<?php echo isset($_POST['save']) ? $_POST['location'] : '' ?>"></div></td>
                </tr>
                <tr>
                    <td><div align="left">Sensor Type:</div></td>
                    <td><div align="left"><input type="radio" name="type" value="a" <?php if(isset($_POST['save'])&&$_POST['type']=='a'){ ?>checked<?php } ?>>Audio Recorder</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="type" value="i" <?php if(isset($_POST['save'])&&$_POST['type']=='i'){ ?>checked<?php } ?>>Image Recorder</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="type" value="s" <?php if(isset($_POST['save'])&&$_POST['type']=='s'){ ?>checked<?php } ?>>Scalar Value Recorder</div></td>
                </tr>
                <tr>
                    <td><div align="left">Description:</div></td>
                    <td><div align="left"><input type="text" name="description" size="45" maxlength="128" value="<?php echo isset($_POST['save']) ? $_POST['description'] : '' ?>"></div></td>
                </tr>      
            </table>
            <p align="center">
                <input type="submit" value="Save" name="save" align="center" style="width:75;margin:10">
                <input type="submit" value="Cancel" name="cancel" align="center" style="width:75;margin:10">
            </p>
            <?php if(isset($_POST['save'])) {
                $success = OceanDB::getInstance()->add_new_sensor($_POST['sensorID'],$_POST['location'],$_POST['type'],$_POST['description']);
                if(!$success) { ?> 
                    <p style="color:red;" align="center">Error! Sensor was not saved<p> 
                <?php } else {
                    header('Location: managementSensorScreen.php');
                }
            }?>
            <?php if(isset($_POST['cancel'])) { header('Location: managementSensorScreen.php'); } ?>
                        
        </form> 
    </body>
</html>

