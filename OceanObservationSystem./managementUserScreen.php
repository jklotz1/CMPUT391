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
        <form name="userManagement" method="post">
            <input type="submit" value="Back" name="back">
            <br><br>

            <!--return to home screen when "home" button is pressed-->
            <?php if (isset($_POST['back'])) { header('Location: managementScreen.php'); }?> 

            <!--display sensor management screen - buttons for the different actions-->

            <h1 align="left" style="font-size: 150%">Users</h1>
            <input type="submit" value="Create New User" name="newUser">
            <input type="submit" value="Edit User" name="editUser">
            <input type="submit" value="Delete User" name="deleteUser">
            <br><br>
            
            <!-- get the current users -->
            <?php $objParse = OceanDB::getInstance()->get_user_info(); ?>

            <!--display the current users in the system-->
                <table width="700" border="1">
                    <tr>
                    <th width="25"> <div align="center"> </div></th>   
                    <th width="91"> <div align="center">User ID</div></th>
                    <th width="98"> <div align="center">First Name</div></th>
                    <th width="98"> <div align="center">Last Name</div></th>
                    <th width="98"> <div align="center">Username</div></th>
                    <th width="197"> <div align="center">Role</div></th>
                    </tr>
                    <?php while($objResult = oci_fetch_array($objParse,OCI_BOTH)){ ?>
                        <tr>
                        <td><div align="center"><input type="radio" name="userSelected" value="<?php echo $objResult["USER_NAME"];?>"></div></td> 
                        <td><div align="center"><?php echo $objResult["PERSON_ID"];?></div></td>
                        <td><div align="center"><?php echo $objResult["FIRST_NAME"];?></td>
                        <td><div align="center"><?php echo $objResult["LAST_NAME"];?></td>
                        <td><div align="center"><?php echo $objResult["USER_NAME"];?></td>
                        <td><div align="center"><?php echo OceanDB::getInstance()->get_role($objResult["ROLE"])?></div></td>
                        </tr>
                    <?php } ?>
                </table>
           
            
            <?php if (isset($_POST['newUser'])) { header('Location: createNewUser.php'); } ?>

            <?php if (isset($_POST['deleteSensor'])) { OceanDB::getInstance()->delete_sensor($_POST['sensorSelected']);header('Location: managementSensorScreen.php'); } ?>
            
        </form> 
    </body>
</html>

