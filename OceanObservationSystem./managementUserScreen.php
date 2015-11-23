<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
ob_start();
session_start();
$user = $_SESSION['user'];
$_SESSION['screen'] = "userManagement";
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        
        <!--allows access to use OceanDB class to connect and query the database-->
        <?php require_once 'Includes/db.php'; ?>
        
        <!--Display the management screen for users-->
        <h1 align="left" style="font-size: 175%">Sensor and User Management Center</h1> 
        <form name="userManagement" method="post">
            <!--user action- "home" to return to the home screen, "back" to return to the main management screen-->
            <input class="logoutButton" type="submit" value="Back" name="back" style="font-size:100%; width:100px; margin:10 ">
            <input class="logoutButton" type="submit" value="Home" name="home" style="font-size:100%; width:100px; margin:10 ">

            <br><br>

            <!--return to home screen when "home" button is pressed-->
            <?php if (isset($_POST['home'])) { header('Location: homeScreen.php'); }?> 
            <!--return to the management screen-->
            <?php if (isset($_POST['back'])) { header('Location: managementScreen.php'); }?> 

            <!--display user/person management screen - buttons for the different actions-->
            <!--"create new user" button to create a new users, "edit user" to edit existing users, "delete users" to delete a user-->
            <h1 align="center" style="font-size: 150%">Users</h1>
            <div align="center">
            <input class="logoutButton" type="submit" value="Create New User" name="newUser" style="font-size:100%; width:200px; margin:10 ">
            <input class="logoutButton" type="submit" value="Edit User" name="editUser" style="font-size:100%; width:150px; margin:10 ">
            <input class="logoutButton" type="submit" value="Delete User" name="deleteUser" style="font-size:100%; width:150px; margin:10 ">
            <!--checks need to be done before performing the action of the buttons-->
            <!--a user must be selected to edit or delete-->
            <!--cannot delete the user that is currently logged in the system-->
            <p style="color:red;display:<?php if(isset($_POST['deleteUser'])&&$_POST['userSelected']==''){?>inline <?php } else { ?> none <?php } ?>"><br>Please select a user to delete</p>            
            <p style="color:red;display:<?php if(isset($_POST['deleteUser'])&&$_POST['userSelected']==$user){?>inline <?php } else { ?> none <?php } ?>"><br>Cannot delete yourself</p>                        
            <p style="color:red;display:<?php if(isset($_POST['editUser'])&&$_POST['userSelected']==''){?>inline <?php } else { ?> none <?php } ?>"><br>Please select a user to edit</p>                        
            <br><br>
            </div>
            
            <!-- get the current users -->
            <?php $objParse = OceanDB::getInstance()->get_user_info(); ?>

            <!--display the current users in the system-->
                <table class="searchTable" width="700" border="1">
                    <tr>
                        <td width="25"> <div align="center"> </div></td>   
                        <td width="91"> <div align="center">Person ID</div></td>
                        <td width="98"> <div align="center">First Name</div></td>
                        <td width="98"> <div align="center">Last Name</div></td>
                        <td width="98"> <div align="center">Username</div></td>
                        <td width="197"> <div align="center">Role</div></td>
                    </tr>
                    <?php while($objResult = oci_fetch_array($objParse,OCI_BOTH)){ ?>
                        <tr>
                        <td><div align="center"><input type="radio" name="userSelected" value="<?php echo $objResult["USER_NAME"];?>"></div></td> 
                        <td><div align="center"><?php echo $objResult["PERSON_ID"];?></div></td>
                        <td><div align="center"><?php echo $objResult["FIRST_NAME"];?></td>
                        <td><div align="center"><?php echo $objResult["LAST_NAME"];?></td>
                        <td><div align="center"><?php echo $objResult["USER_NAME"];?></td>
                        <td><div align="center"><?php echo OceanDB::getInstance()->get_role($objResult["ROLE"]);?></div></td>
                        </tr>
                    <?php } ?>
                </table>
           
            <!--"create new user" button is selected - user is taken to a different page to create -->
            <?php if (isset($_POST['newUser'])) { header('Location: createNewUser.php'); } ?>

            <!--"delete user" if a sensor is selected the user is deleted-->
            <?php if (isset($_POST['deleteUser']) && $_POST['userSelected']!='') { 
                if ($_POST['userSelected'] != $user){
                    OceanDB::getInstance()->delete_user($_POST['userSelected']);
                    header('Location: managementUserScreen.php');
                }
            } ?>
            
            <!--"edit user" if a user is selected the user is taken to a different page to edit the user-->
            <?php if (isset($_POST['editUser']) && $_POST['userSelected']!='') { 
                $_SESSION['userToEdit'] = $_POST['userSelected'];
                header('Location: editUserScreen.php'); 
            } ?>
            
            <!--Personal Account table-->
            <!--get the personId of the the user currently logged into the system-->
            <?php $personID = OceanDB::getInstance()->get_personID($user); ?>
            <h1 align="center" style="font-size: 150%; margin-top: 50">Personal Accounts</h1>
            <div align="center">
            <!--display user/person management screen - buttons for the different actions-->
            <!--"create new person" button to create a new person, "edit person" to edit existing person, "delete person" to delete a person-->
            <input class="logoutButton" type="submit" value="Create New Person" name="newPerson" style="font-size:100%; width:200px; margin:10 ">
            <input class="logoutButton" type="submit" value="Edit Person" name="editPerson" style="font-size:100%; width:150px; margin:10 ">
            <input class="logoutButton" type="submit" value="Delete Person" name="deletePerson" style="font-size:100%; width:150px; margin:10 ">
            <!--checks need to be done before performing the action of the buttons-->
            <!--a person must be selected to edit or delete-->
            <!--cannot delete the person that is associated with the user that is currently logged in the system-->
            <p style="color:red;display:<?php if(isset($_POST['deletePerson'])&&$_POST['personSelected']==''){?>inline <?php } else { ?> none <?php } ?>"><br>Please select a user to delete</p>            
            <p style="color:red;display:<?php if(isset($_POST['deletePerson'])&&$_POST['personSelected']==$personID){?>inline <?php } else { ?> none <?php } ?>"><br>Cannot delete yourself</p>                        
            <p style="color:red;display:<?php if(isset($_POST['editPerson'])&&$_POST['personSelected']==''){?>inline <?php } else { ?> none <?php } ?>"><br>Please select a user to edit</p>                        
            <br><br>
            </div>
            
            <!-- get the current persons -->
            <?php $objParse = OceanDB::getInstance()->get_persons(); ?>

            <!--display the current persons in the system-->
                <table class="searchTable" width="700" border="1">
                    <tr>
                        <td width="25"> <div align="center"> </div></td>   
                        <td width="91"> <div align="center">Person ID</div></td>
                        <td width="98"> <div align="center">First Name</div></td>
                        <td width="98"> <div align="center">Last Name</div></td>
                        <td width="198"> <div align="center">Address</div></td>
                        <td width="197"> <div align="center">Email Address</div></td>
                        <td width="98"> <div align="center">Phone Number</div></td>
                    </tr>
                    <?php while($objResult = oci_fetch_array($objParse,OCI_BOTH)){ ?>
                        <tr>
                        <td><div align="center"><input type="radio" name="personSelected" value="<?php echo $objResult["PERSON_ID"];?>"></div></td> 
                        <td><div align="center"><?php echo $objResult["PERSON_ID"];?></div></td>
                        <td><div align="center"><?php echo $objResult["FIRST_NAME"];?></td>
                        <td><div align="center"><?php echo $objResult["LAST_NAME"];?></td>
                        <td><div align="center"><?php echo $objResult["ADDRESS"];?></td>
                        <td><div align="center"><?php echo $objResult["EMAIL"];?></td>
                        <td><div align="center"><?php echo $objResult["PHONE"];?></td>
                        </tr>
                    <?php } ?>
                </table>
           
            <!--"create new person" button is selected - user is taken to a different page to create -->
            <?php if (isset($_POST['newPerson'])) { header('Location: createNewPersonProfile.php'); } ?>

            <!--"delete person" if a person is selected the person is deleted-->
            <?php if (isset($_POST['deletePerson']) && $_POST['personSelected']!= $personID) { 
                    OceanDB::getInstance()->delete_person($_POST['personSelected']);
                    header('Location: managementUserScreen.php');               
            } ?>
            
            <!--"edit person" if a person is selected the user is taken to a different page to edit the person-->
            <?php if (isset($_POST['editPerson']) && $_POST['personSelected']!='') { 
                $_SESSION['personToEdit'] = $_POST['personSelected'];
                header('Location: editPersonScreen.php'); 
            } ?>
            
            
        </form> 
    </body>
    <!--used for the graphical interface-->
    <?php   require_once("Includes/css.php");  ?>
</html>

