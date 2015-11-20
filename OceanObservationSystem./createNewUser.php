<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
ob_start();
session_start();
$_SESSION['screen'] = "createUser";
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php require_once 'Includes/db.php'; ?>
        <?php $people = OceanDB::getInstance()->get_persons(); ?>
        
        <h1 align="center" style="font-size: 175%">Add New User</h1>        
        <form name="userCreate" method="post">
            <table width="500" align="center" cellpadding="5">
                <tr>
                    <td><div align="left">Username:</div></td>
                    <td><div align="left"><input type="text" name="username" size ="45" value="<?php echo isset($_POST['saveNewUser']) ? $_POST['username'] : '' ?>"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['saveNewUser'])&&$_POST['username']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                    
                </tr>
                <tr>
                    <td><div align="left">Password:</div></td>
                    <td><div align="left"><input type="password" name="password" size="45" maxlength="64"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['saveNewUser'])&&$_POST['password']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">Confirm Password:</div></td>
                    <td><div align="left"><input type="password" name="password2" size="45" maxlength="64"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['saveNewUser'])&&$_POST['password2']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">Role:</div></td>
                    <td><div align="left"><input type="radio" name="role" value="a" <?php if(isset($_POST['saveNewUser'])&&$_POST['role']=='a'){ ?>checked<?php } ?>>Administrator</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="role" value="d" <?php if(isset($_POST['saveNewUser'])&&$_POST['role']=='d'){ ?>checked<?php } ?>>Data Curator</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="role" value="s" <?php if(isset($_POST['saveNewUser'])&&$_POST['role']=='s'){ ?>checked<?php } ?>>Scientist</div></td>
                </tr>
                <tr >
                    <td><div align="left"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['saveNewUser'])&&$_POST['role']==''){?>inline <?php } else { ?> none <?php } ?>">*Required - Select one</div></td>
                </tr>
                <tr>
                    <td><div align="left">Person Profile:</div></td>
                    <td><div align="left"><select name="personProfile"><option value=""></option> <?php while($person = oci_fetch_array($people,OCI_ASSOC)){?>
                                                                                                    <option value="<?php echo $person['PERSON_ID']; ?>"<?php if(isset($_POST['saveNewUser'])&&$_POST['personProfile']== $person["PERSON_ID"]){?>selected<?php }?>><?php 
                                                                                                        echo "(".$person["PERSON_ID"].") ".$person["FIRST_NAME"]." ".$person["LAST_NAME"];?>
                                                                                                    </option><?php }?></select><input class="logoutButton" type="submit" value="Add New Profile" name="newProfile" align="left" style="font-size:60%;margin-left:30"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['saveNewUser'])&&$_POST['personProfile']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>                   
                </tr>
                
            </table>
            <p align="center">
                <input class="logoutButton" type="submit" value="Save" name="saveNewUser" align="center" style="font-size:100%; width:100px; margin:10 ">
                <input class="logoutButton" type="submit" value="Cancel" name="cancelNewUser" align="center" style="font-size:100%; width:100px; margin:10 ">
            </p>
            
            <!-- check if all the fields have been filled in -->
            <?php $isEmpty = false;
            
            if (isset($_POST['saveNewUser'])) {   
                if ($_POST['username'] == '') { $isEmpty = true; }
                if ($_POST['password'] == '') { $isEmpty = true; }
                if ($_POST['password2'] == '') { $isEmpty = true; }
                if ($_POST['role'] == '') { $isEmpty = true; }
                if ($_POST['personProfile'] == '') { $isEmpty = true; }
            } ?>
            
            <!--check that username doesn't already exist-->
            <?php $validUser = false;
            if(isset($_POST['saveNewUser']) && !$isEmpty) {
                $validUser = !(OceanDB::getInstance()->user_exist($_POST['username']));
            }?>
            
            <!--check that passwords match-->
            <?php $validPassword = false;
            if(isset($_POST['saveNewUser']) && !$isEmpty) {
                $validPassword = ($_POST['password'] == $_POST['password2']);
            }?>
            
            <div align="center">
            <?php if(isset($_POST['saveNewUser']) && !$validPassword && !$isEmpty) { echo "<p style='color:red;'>Passwords do not match.<p>";} ?>
            <?php if(isset($_POST['saveNewUser']) && !$validUser && !$isEmpty) { echo "<p style='color:red;'>Username ".$_POST['username']." already exists<p>";}?>
            </div>
            
            <!--check that passwords match-->
            <?php if(isset($_POST['saveNewUser']) && !$isEmpty && $validPassword && $validUser) {
                $success = OceanDB::getInstance()->add_new_user($_POST['username'],$_POST['password'],$_POST['role'],$_POST['personProfile']);
                if ($success){
                    header('Location: managementUserScreen.php');
                }
            }?>
            
            <?php if(isset($_POST['newProfile'])) { header('Location: createNewPersonProfile.php'); }?>
            
            <?php if(isset($_POST['cancelNewUser'])) { header('Location: managementUserScreen.php'); } ?>
                        
        </form> 
    </body>
    <?php   require_once("Includes/css.php");  ?>
</html>

