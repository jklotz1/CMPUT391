<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<?php
ob_start();
session_start();
//the user to edit
$userEdit = $_SESSION['userToEdit'];
//the previous screen to return to
$previousScreen = $_SESSION['screen'];
//the user currently login into the system
$user = $_SESSION['user'];
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        
        <!--allows access to the OceanDB class to connect and query the database-->
        <?php  
            require_once 'Includes/db.php';
        ?>

        <!--display the form to edit the selected user-->
        <form name="personalProfile" method="post">
            <!--get the user account information of the user to edit - used to populate the form-->
            <?php $userInfo = OceanDB::getInstance()->get_user_info_only($userEdit); ?>
            <!--get persons for the drop down person profile list-->
            <?php $people = OceanDB::getInstance()->get_persons(); ?>
            <h1 align="left" style="font-size:150%; margin-bottom:50">Edit User: <?php echo $userEdit;?></h1>
            <table width="400" align="center">
                <tr>
                    <td width="170"><div align="left">Username:</div></td>
                    <td><div align="left"><input type="text" name="username"value="<?php if(isset($_REQUEST['save'])){ echo $_POST['username']; } else { echo $userInfo['USER_NAME']; }?>"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['username']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                    
                </tr>
                <tr>
                    <td><div align="left">Role:</div></td>
                    <td><div align="left"><input type="radio" name="role" value="a" <?php if(isset($_POST['save'])&& $_POST['role']=='a'){?> checked <?php } else if($userInfo['ROLE']=='a'){ ?>checked<?php } ?>>Administrator</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="role" value="d" <?php if(isset($_POST['save'])&& $_POST['role']=='d'){?> checked <?php } else if($userInfo['ROLE']=='d'){ ?>checked<?php } ?>>Data Curator</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="role" value="s" <?php if(isset($_POST['save'])&& $_POST['role']=='s'){?> checked <?php } else if($userInfo['ROLE']=='s'){ ?>checked<?php } ?>>Scientist</div></td>
                </tr>
                <tr >
                    <td><div align="left"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['role']==''){?>inline <?php } else { ?> none <?php } ?>">*Required - Select one</div></td>
                </tr>
                <td><div align="left">Person Profile:</div></td>
                    <td><div align="left"><select name="personProfile"><option value=""></option> <?php while($person = oci_fetch_array($people,OCI_ASSOC)){?>
                                                                                                    <option value="<?php echo $person['PERSON_ID']; ?>"<?php if($userInfo["PERSON_ID"]== $person["PERSON_ID"]){?>selected<?php }?>><?php 
                                                                                                        echo "(".$person["PERSON_ID"].") ".$person["FIRST_NAME"]." ".$person["LAST_NAME"];?>
                                                                                                    </option><?php }?></select></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['personProfile']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
            </table>
            
            <!--the user has the option to change the password or not-->
            <!--to change the password, the old password and new password must be entered-->
            <!--if all password field are left blank the current password is kept-->
            <h1 align="center" style="font-size: 110%"><br>Change password</h1>
            <table width="400" align="center">
                <tr>
                    <td><div align="left">Old Password:</div></td>
                    <td><div align="left"><input type="password" name="oldPassword" size="25" maxlength="32"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&($_POST['newPassword']!='' or $_POST['newPassword']!='' or $_POST['oldPassword']!='')){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">New Password:</div></td>
                    <td><div align="left"><input type="password" name="newPassword" size="25" maxlength="32"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&($_POST['newPassword']!='' or $_POST['newPassword']!='' or $_POST['oldPassword']!='')){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">Confirm New Password:</div></td>
                    <td><div align="left"><input type="password" name="newPasswordConfirm" size="25" maxlength="32"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&($_POST['newPassword']!='' or $_POST['newPassword']!='' or $_POST['oldPassword']!='')){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>

            </table>
            <h1 align="center" style="font-size:100%;font-style:italic">To change password enter current password following by new password. Leave blank to keep current password</h1>

            <!--user action- "save" to update the user information, "cancel" to discard changes-->
            <div align="center">
            <input class="logoutButton" type="submit" align="center" value="Save" name="save" style="font-size:100%; width:100px; margin:10 ">
            <input class="logoutButton" type="submit" align="center" value="Cancel" name="cancel" style="font-size:100%; width:100px; margin:10 ">
            </div>
            
            <!--"cancel" clicked - discard changes and return to previous page-->
            <?php if(isset($_REQUEST['cancel'])){
                if($previousScreen=='personalAccount'){ header('Location: personalAccount.php');}
                if($previousScreen=='userManagement'){ header('Location: managementUserScreen.php');}
            }?>
            
            <!--Check if the user chose to change the password-->
            <?php
            $keepPassword = false;
            $emptyPasswords = true;
            if (isset($_REQUEST['save']))
            {
                //keep password when all three password fields are empty
                //if atleast one of the three password fields are filled out assume the password is being changed and make sure that all three fields have been filled out
                if ($_POST["oldPassword"]=="" && $_POST["newPassword"]=="" && $_POST["newPasswordConfrim"]==""){
                    $keepPassword = true;
                } elseif ($_POST["oldPassword"]=="" or $_POST["newPassword"]=="" or $_POST["newPasswordConfirm"]==""){
                    $emptyPasswords = true;
                } else {
                    $emptyPasswords = false;
                }
            }

            //checks for changing password
            //check: not empty fields
            //check: the old password matches that of the user
            //check: the new password and confirm new password match
            //if any of these checks fail, display a message
            $changePassword = false;
            if (isset($_REQUEST['save'])){
                if (!$emptyPasswords){
                    if (OceanDB::getInstance()->password_match($userEdit,$_POST["oldPassword"])) {
                        if ($_POST["newPassword"] == $_POST["newPasswordConfirm"]) {
                            $changePassword = true;
                        } else { echo "<br><div align='center' style='color:red;'>New passwords do not match.</div>"; }
                    } else { echo "<br><div align='center' style='color:red;'>Incorrect password combination.</div>"; }
                }
            }
            ?>
            
            <!--make sure that the user fields are all filled out-->
            <?php
            if (isset($_REQUEST['save'])){
                $blank = true;
                if ($_POST['role']=="" or $_POST['username']=="" or $_POST['personProfile']==""){
                    $blank = true;
                } else {
                    $blank = false;
                }
                $saveInfo = false;
                
                //if username has been changed do check that it doesn't already exist
                if (!$blank){
                    if($userEdit!=$_POST['username']){
                        if (!(OceanDB::getInstance()->user_exist($_POST['username']))){
                            $saveInfo = true;
                        } else { echo "<br><div align='center' style='color:red;'>Username '".$_POST['username']."' already exists.</div>"; }
                    } else { $saveInfo = true; }
                }
            }
            ?>
                 
            <!--if all checks pass then the user can be updated and return to the previous page-->
            <!--if the user that was edited is the currently logged in user - must update the session user variable-->
            <?php
            if ($changePassword && $saveInfo){
                $success = OceanDB::getInstance()->update_user($userEdit, $_POST['role'], $_POST['username'], $_POST['newPassword'],$_POST['personProfile']);
                if ($success) {
                    if($user == $userEdit){$_SESSION['user'] = $_POST['username'];}
                    $_SESSION['userToEdit'] = $_POST['username']; 
                    if($previousScreen=='personalAccount'){ header('Location: personalAccount.php');}
                    if($previousScreen=='userManagement'){ header('Location: managementUserScreen.php');}
                }
            }
            elseif ($keepPassword && $saveInfo){
                $success = OceanDB::getInstance()->update_user($userEdit, $_POST['role'], $_POST['username'],$userInfo['PASSWORD'],$_POST['personProfile']);
                if ($success) {
                    if($user == $userEdit){$_SESSION['user'] = $_POST['username'];}
                    $_SESSION['userToEdit'] = $_POST['username'];                   
                    if($previousScreen=='personalAccount'){ header('Location: personalAccount.php');}
                    if($previousScreen=='userManagement'){ header('Location: managementUserScreen.php');}
                }    
            }
            ?>
            
        </form>              
                            
    </body>
    <!--used for graphical interface-->
    <?php   require_once("Includes/css.php");  ?>
</html>
