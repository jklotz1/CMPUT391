<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<!--get the username of the user in the system currently-->
<?php
session_start();
$user = $_SESSION['user'];
$allow = false;
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        
        <!--check the role of the user - only administrators are allowed to access this section-->
        <?php  
            require_once 'Includes/db.php';
            $role = OceanDB::getInstance()->get_user_role($user);
        ?>
        
        <!--Bring up Management screen if administrator else deny access and display message-->
        <?php if ($role == 'a') { ?>
            <h1 align="left" style="font-size: 175%">Sensor and User Management Center</h1>
            <?php $allow = true; ?>
        <?php } else { ?>
            <h1 align="left" style="font-size: 175%; color: red">Access denied</h1>
            <?php $allow = false; ?>
        <?php } ?>
         
        <?php if ($allow) { ?>
            <form name="management" method="post">
            <input type="submit" value="Home" name="home">
            <?php echo "<br><br>" ?>
            <input type="submit" value="Edit Sensors" name="sensors">
            <input type="submit" value="Edit Users" name="users">
            <?php echo "<br><br>" ?>
            
            <!--return to home screen when "home" button is pressed-->
            <?php if (isset($_REQUEST['home'])) { header('Location: homeScreen.php'); }?>          
            
            <!--display sensor management screen when "Edit Sensors" button is selected-->
            <div id="sensorManagement" style="display: <?php if (!isset($_REQUEST['sensors'])) { ?> none <?php } ?>">
                <h1 align="left" style="font-size: 150%">Edit Sensors</h1>
                <input type="submit" value="Create New Sensor" name="new">
                <input type="submit" value="Manage Sensor" name="delete">
                <?php echo "<br>Select sensor and press 'Manage Sensor' to view, update and delete sensor.<br><br>" ?>
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
            </div>

            <!--display user management screen when "Edit users" button is pressed-->
            <div id="userManagement" style="display: <?php if (!isset($_REQUEST['users'])) { ?> none <?php } ?>">
                <h1 align="left" style="font-size: 150%">Edit Users</h1>
                <input type="submit" value="Create New User" name="new">
                <input type="submit" value="Manage User" name="update">
                <?php echo "<br>Select user and press 'Manage User' to view, update and delete user.<br><br>"  ?>
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
            </div>
                            
            </form>
        <?php } ?>
    </body>
</html>
