<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<!--get the username of the user in the system currently-->
<?php
ob_start();
session_start();
$user = $_SESSION['user'];
$_SESSION['screen'] = 'personalAccount';
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        
        <!--check the role of the user - only administrators are allowed to access this section-->
        <?php require_once 'Includes/db.php';?>

        <!--display user management screen when "Edit users" button is pressed-->
        <form name="personalProfile" method="post">
            
            <?php $personInfo = OceanDB::getInstance()->get_personal_info($user);?>
            <h1 align="left" style="font-size: 150%">Personal Profile: <?php echo $personInfo["FIRST_NAME"]." ".$personInfo["LAST_NAME"];?></h1>
            <input class="logoutButton" type="submit" value="Back" name="back" style="margin-bottom:30;margin-top:15">
            
            <?php if(isset($_POST['back'])){ header('Location: loginScreen.php'); }?>
            
            <table width="400" align="center">
                <tr>
                    <td><div align="left">Person ID:</div></td>
                    <td><div align="left"><input type="text" name="personID" size="30" value="<?php echo $personInfo["PERSON_ID"];?>" readonly></div></td>                    
                </tr>
                <tr>
                    <td><div align="left">First Name:</div></td>
                    <td><div align="left"><input type="text" name="firstName" maxlength="24" size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['firstName']; } else { echo $personInfo["FIRST_NAME"];}?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['firstName']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">Last Name:</div></td>
                    <td><div align="left"><input type="text" name="lastName" maxlength="24"  size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['lastName']; } else { echo $personInfo["LAST_NAME"];}?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['lastName']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>                
                </tr>
                <tr>
                    <td><div align="left">Address:</div></td>
                    <td><div align="left"><input type="text" name="address" maxlength="128" size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['address']; } else { echo $personInfo["ADDRESS"];}?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['address']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">Email Address:</div></td>
                    <td><div align="left"><input type="text" name="email" maxlength="128" size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['email']; } else { echo $personInfo["EMAIL"];}?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['email']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>                
                </tr>
                <tr>
                    <td><div align="left">Phone Number:</div></td>
                    <td><div align="left"><input type="text" name="phone" maxlength="10"  size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['phone']; } else { echo $personInfo["PHONE"];}?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['phone']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>

            </table>
            
            <div align="center">
            <input class="logoutButton" type="submit" value="Save Changes" name="save" align="center" style="margin-bottom:0;margin-top:15">
            <input class="logoutButton" type="submit" value="Cancel Changes" name="cancel" align="center" style="margin-bottom:0;margin-top:15">
            </div>
            <?php if(isset($_REQUEST['save'])) {
                $empty = false;
                if ($_POST['firstName']=='' or $_POST['lastName']=='' or $_POST['address']=='' or $_POST['email']=='' or $_POST['phone']==''){
                    $empty = true;
                }
            }
            ?>
            
            <?php if(isset($_REQUEST['save'])&&!$empty){
                $success = OceanDB::getInstance()->update_person($personInfo["PERSON_ID"],$_POST['firstName'],$_POST['lastName'],$_POST['address'],$_POST['email'],$_POST['phone']);
               if ($success) { ?> <p style="color:red">Saved!</p> <?php ; header('Location: personalAccount.php'); }
            }
            ?>
            
            
            <!--get all user accounts associated with username-->
            <?php $usersAll = OceanDB::getInstance()->get_all_users_by_user($user); ?>
            
            <table class="searchTable" width="100" border="1" style="margin-top:50">
                <tr>
                    <td width="25"> <div align="center"> </div></td>
                    <td> <div align="center">Username</div></td>
                    <td> <div align="center">Role</div></td>
                </tr>
                <?php while($users1 = oci_fetch_array($usersAll,OCI_BOTH)){ ?>
                    <tr>
                    <td><div align="center"><input type="radio" name="userSelected" value="<?php echo $users1["USER_NAME"];?>"></div></td>
                    <td><div align="center"><?php echo $users1["USER_NAME"];?></td>
                    <td><div align="center"><?php echo OceanDB::getInstance()->get_role($users1["ROLE"]);?></div></td>
                    </tr>
                <?php } ?>
            </table>
            <div align="center">
            <input class="logoutButton" type="submit" value="Edit User" name="userEdit" style="margin-top:15;margin-bottom:15">
            <p style="color:red;display:<?php if(isset($_POST['userEdit'])&&$_POST['userSelected']==''){?>inline <?php } else { ?> none <?php } ?>"><br>Select a user to edit</p>
            </div>
            
            <?php if(isset($_POST['userEdit'])&&$_POST['userSelected']){
                $_SESSION['userToEdit'] = $_POST['userSelected'];
                header('Location: editUserScreen.php');
            } ?>
        </form>              
                            
    </body>
    <?php   require_once("Includes/css.php");  ?>
</html>
