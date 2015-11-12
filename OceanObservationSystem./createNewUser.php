<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
$userID = '';
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php require_once 'Includes/db.php'; ?>
        <?php $sensorID = OceanDB::getInstance()->get_next_sensorID();?> 
        <?php $people = OceanDB::getInstance()->get_persons(); ?>
        
        <h1 align="center" style="font-size: 175%">Add New User</h1>        
        <form name="sensorUser" method="post">
            <table width="500" align="center" cellpadding="5">
                <tr>
                    <td><div align="left">Username:</div></td>
                    <td><div align="left"><input type="text" name="username" size ="10" value="<?php echo isset($_POST['save']) ? $_POST['username'] : '' ?>"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['username']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                    
                </tr>
                <tr>
                    <td><div align="left">Password:</div></td>
                    <td><div align="left"><input type="text" name="password" size="45" maxlength="64"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['password']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">Confirm Password:</div></td>
                    <td><div align="left"><input type="text" name="password2" size="45" maxlength="64"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['password2']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">Role:</div></td>
                    <td><div align="left"><input type="radio" name="role" value="a" <?php if(isset($_POST['save'])&&$_POST['type']=='a'){ ?>checked<?php } ?>>Administrator</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="role" value="d" <?php if(isset($_POST['save'])&&$_POST['type']=='d'){ ?>checked<?php } ?>>Data Curator</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="role" value="s" <?php if(isset($_POST['save'])&&$_POST['type']=='s'){ ?>checked<?php } ?>>Scientist</div></td>
                </tr>
                <tr >
                    <td><div align="left"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['role']==''){?>inline <?php } else { ?> none <?php } ?>">*Required - Select one</div></td>
                </tr>
                <tr>
                    <td><div align="left">Person Profile:</div></td>
                    <td><div align="left"><select> <?php while($person = oci_fetch_array($people,OCI_BOTH)){ ?>
                        <tr>
                        <td><div align="center"><input type="radio" name="userSelected" value="<?php echo $objResult["USER_NAME"];?>"></div></td> 
                        <td><div align="center"><?php echo $objResult["PERSON_ID"];?></div></td>
                        <td><div align="center"><?php echo $person["FIRST_NAME"];?></td>
                        <td><div align="center"><?php echo $objResult["LAST_NAME"];?></td>
                        <td><div align="center"><?php echo $objResult["USER_NAME"];?></td>
                        <td><div align="center"><?php echo OceanDB::getInstance()->get_role($objResult["ROLE"])?></div></td>
                        </tr>
                    <?php } ?></select></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['description']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                    
                </tr>      
            </table>
            <p align="center">
                <input type="submit" value="Save" name="save" align="center" style="width:75;margin:10">
                <input type="submit" value="Cancel" name="cancel" align="center" style="width:75;margin:10">
            </p>
            <!-- check if all the fields have been filled in -->

            <?php $isEmpty = false;
            
            if (isset($_POST['save'])) {
                if ($_POST['location'] == '') { $isEmpty = true; }
                if ($_POST['description'] == '') { $isEmpty = true; }
                if ($_POST['type'] == '') { $isEmpty = true; }
            } ?>
            
            <?php if(isset($_POST['save']) && !$isEmpty) {
                $success = OceanDB::getInstance()->add_new_sensor($_POST['sensorID'],$_POST['location'],$_POST['type'],$_POST['description']);
                if(!$success) { ?> 
                    <p style="color:red;" align="center">Error! Sensor was not saved<p> 
                <?php } else {
                    header('Location: managementSensorScreen.php');
                }
            }?>
            <?php if(isset($_POST['cancel'])) { header('Location: managementUserScreen.php'); } ?>
                        
        </form> 
    </body>
</html>

