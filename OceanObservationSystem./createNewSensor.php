<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
$sensorID = '';
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <!--provides the ability to access the class OceanDB in db.php used for connecting and quering the database-->
        <?php require_once 'Includes/db.php'; ?>
        <!--get the next sensorID in the system that can be used for creating a new sensor-->
        <!--this field is filled in automatically and is read only - the user cannot edit it-->
        <?php $sensorID = OceanDB::getInstance()->get_next_sensorID();?> 
        
        <!--display the form to create a new sensor-->
        <h1 align="center" style="font-size: 175%">Add New Sensor</h1>        
        <form name="sensorCreate" method="post">
            <table width="500" align="center" cellpadding="5">
                <tr>
                    <td><div align="left">Sensor ID:</div></td>
                    <td><div align="left"><input type="text" name="sensorID" size ="10" value="<?php echo $sensorID ?>" readonly></div></td>
                </tr>
                <tr>
                    <td><div align="left">Location:</div></td>
                    <td><div align="left"><input type="text" name="location" size="45" maxlength="64" value="<?php echo isset($_POST['save']) ? $_POST['location'] : '' ?>"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['location']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <!--user radio buttons for the sensor type, one of three choice must be selected-->
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
                <tr >
                    <td><div align="left"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['type']==''){?>inline <?php } else { ?> none <?php } ?>">*Required - Select one</div></td>
                </tr>
                <tr>
                    <td><div align="left">Description:</div></td>
                    <td><div align="left"><input type="text" name="description" size="45" maxlength="128" value="<?php echo isset($_POST['save']) ? $_POST['description'] : '' ?>"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['description']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>

                </tr>      
            </table>
            
            <!--user action - "save" to save the sensor or "cancel" to discard the form and return to the previous page-->
            <p align="center">
                <input class="logoutButton" type="submit" value="Save" name="save" align="center" style="font-size:100%; width:100px; margin:10 ">
                <input class="logoutButton" type="submit" value="Cancel" name="cancel" align="center" style="font-size:100%; width:100px; margin:10 ">
            </p>
            
            <!--if "save" button is selected, must perform checked on the user enter fields-->
            <!-- check: all the fields have been filled in, cannot have empty fields-->
            <?php $isEmpty = false;
            
            if (isset($_POST['save'])) {
                if ($_POST['location'] == '') { $isEmpty = true; }
                if ($_POST['description'] == '') { $isEmpty = true; }
                if ($_POST['type'] == '') { $isEmpty = true; }
            } ?>
            
            <!--if "save" button is selected and fields are not empty, save the sensor to the database and return to the previous screen-->
            <?php if(isset($_POST['save']) && !$isEmpty) {
                $success = OceanDB::getInstance()->add_new_sensor($_POST['sensorID'],$_POST['location'],$_POST['type'],$_POST['description']);
                if(!$success) { ?> 
                    <p style="color:red;" align="center">Error! Sensor was not saved<p> 
                <?php } else {
                    header('Location: managementSensorScreen.php');
                }
            }?>
                        
             <!--"cancel" clicked - return the the previous page-->           
            <?php if(isset($_POST['cancel'])) { header('Location: managementSensorScreen.php'); } ?>
                        
        </form> 
    </body>
    <!--used for graphical interface-->
    <?php   require_once("Includes/css.php");  ?>
</html>

