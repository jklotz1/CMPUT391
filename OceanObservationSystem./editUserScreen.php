<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<!--get the username of the user in the system currently-->
<?php
session_start();
$userEdit = $_SESSION['userToEdit'];
$previousScreen = $_SESSION['screen'];
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
        ?>

        <!--display user management screen when "Edit users" button is pressed-->
        <form name="personalProfile" method="post">
            <?php $userInfo = OceanDB::getInstance()->get_user_info_only($userEdit); ?>
            <h1 align="left" style="font-size:150%; margin-bottom:50">Edit User: <?php echo $userEdit;?></h1>
            <table width="400" align="center">
                <tr>
                    <td width="170"><div align="left">Username:</div></td>
                    <td><div align="left"><input type="text" name="username"value="<?php if(isset($_REQUEST['save'])){ echo $_POST['username']; } else { echo $userInfo['USER_NAME']; }?>"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['username']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                    
                </tr>
                <tr>
                    <td><div align="left">Role:</div></td>
                    <td><div align="left"><input type="radio" name="role" value="a" <?php if($userInfo['ROLE']=='a'){ ?>checked<?php } ?>>Administrator</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="role" value="d" <?php if($userInfo['ROLE']=='d'){ ?>checked<?php } ?>>Data Curator</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="role" value="s" <?php if($userInfo['ROLE']=='s'){ ?>checked<?php } ?>>Scientist</div></td>
                </tr>
                <tr >
                    <td><div align="left"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&$_POST['role']==''){?>inline <?php } else { ?> none <?php } ?>">*Required - Select one</div></td>
                </tr>
            </table>
            
            <h1 align="center" style="font-size: 110%"><br>Change password</h1>
            <table width="400" align="center">
                <tr>
                    <td><div align="left">Old Password:</div></td>
                    <td><div align="left"><input type="text" name="oldPassword" size="25" maxlength="32"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&($_POST['newPassword']!='' or $_POST['newPassword']!='' or $_POST['oldPassword']!='')){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">New Password:</div></td>
                    <td><div align="left"><input type="text" name="newPassword" size="25" maxlength="32"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&($_POST['newPassword']!='' or $_POST['newPassword']!='' or $_POST['oldPassword']!='')){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">Confirm New Password:</div></td>
                    <td><div align="left"><input type="text" name="newPasswordConfirm" size="25" maxlength="32"></div></td>
                    <td><div align="left" style="color:red; display: <?php if(isset($_POST['save'])&&($_POST['newPassword']!='' or $_POST['newPassword']!='' or $_POST['oldPassword']!='')){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>

            </table>
            <h1 align="center" style="font-size:100%;font-style:italic">To change password enter current password following by new password. Leave blank to keep current password</h1>

            <div align="center">
            <input class="logoutButton" type="submit" align="center" value="Save" name="save" style="font-size:100%; width:100px; margin:10 ">
            <input class="logoutButton" type="submit" align="center" value="Cancel" name="cancel" style="font-size:100%; width:100px; margin:10 ">
            </div>
            
            <?php if(isset($_REQUEST['cancel'])){
                if($previousScreen=='personalAccount'){ header('Location: personalAccount.php');}
                if($previousScreen=='userManagement'){ header('Location: managementUserScreen.php');}
            }?>
            
            <!--Check if password can be changed-->
            <?php
            
            $keepPassword = false;
            $emptyPasswords = true;
            if (isset($_REQUEST['save']))
            {
                if ($_POST["oldPassword"]=="" && $_POST["newPassword"]=="" && $_POST["newPasswordConfrim"]==""){
                    $keepPassword = true;
                } elseif ($_POST["oldPassword"]=="" or $_POST["newPassword"]=="" or $_POST["newPasswordConfirm"]==""){
                    $emptyPasswords = true;
                } else {
                    $emptyPasswords = false;
                }
            }

            $changePassword = false;
            if (isset($_REQUEST['save'])){
                if (!$emptyPasswords){
                    if (OceanDB::getInstance()->password_match($userEdit,$_POST["oldPassword"])) {
                        if ($_POST["newPassword"] == $_POST["newPasswordConfirm"]) {
                            $changePassword = true;
                        } else { echo "<br><div style='color:red;'>New passwords do not match.</div>"; }
                    } else { echo "<br><div style='color:red;'>Incorrect password combination.</div>"; }
                }
            }
            ?>
            
            <!--check if User Info can be saved-->
            <?php
            if (isset($_REQUEST['save'])){
                $blank = true;
                if ($_POST['role']=="" or $_POST['username']==""){
                    $blank = true;
                } else {
                    $blank = false;
                }
                $saveInfo = false;
                if (!$blank){

                    if($userEdit!=$_POST['username']){
                        if (!(OceanDB::getInstance()->user_exist($_POST['username']))){
                            $saveInfo = true;
                        } else { echo "<br><div style='color:red;'>Username '".$_POST['username']."' already exists.</div>"; }
                    } else { $saveInfo = true; }
                }
            }
            ?>
                        
            <?php
            
            if ($changePassword && $saveInfo){
                $success = OceanDB::getInstance()->update_user($userEdit, $_POST['role'], $_POST['username'], $_POST['newPassword']);
                if ($success) {
                    $_SESSION['userToEdit'] = $_POST['username'];
                    if($previousScreen=='personalAccount'){ header('Location: personalAccount.php');}
                    if($previousScreen=='userManagement'){ header('Location: managementUserScreen.php');}
                }
            }
            elseif ($keepPassword && $saveInfo){
                $success = OceanDB::getInstance()->update_user($userEdit, $_POST['role'], $_POST['username'],$userInfo['PASSWORD']);
                if ($success) {
                    $_SESSION['userToEdit'] = $_POST['username'];
                    if($previousScreen=='personalAccount'){ header('Location: personalAccount.php');}
                    if($previousScreen=='userManagement'){ header('Location: managementUserScreen.php');}
                }    
            }
            ?>
            
        </form>              
                            
    </body>
    <?php   require_once("Includes/css.php");  ?>
</html>
