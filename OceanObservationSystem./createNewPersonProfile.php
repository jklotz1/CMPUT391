<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
ob_start();
session_start();
$screen = $_SESSION['screen'];
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php require_once 'Includes/db.php'; ?>
        <?php $personID = OceanDB::getInstance()->get_next_personID();?>
        
        <h1 align="center" style="font-size: 175%">Add New Personal Profile</h1>        
        <form name="userCreate" method="post">
            <table width="400" align="center">
                <tr>
                    <td><div align="left">Person ID:</div></td>
                    <td><div align="left"><input type="text" name="personID" size="30" value="<?php echo $personID;?>" readonly></div></td>                    
                </tr>
                <tr>
                    <td><div align="left">First Name:</div></td>
                    <td><div align="left"><input type="text" name="firstName" maxlength="24" size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['firstName']; }?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['firstName']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">Last Name:</div></td>
                    <td><div align="left"><input type="text" name="lastName" maxlength="24"  size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['lastName']; } ?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['lastName']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>                
                </tr>
                <tr>
                    <td><div align="left">Address:</div></td>
                    <td><div align="left"><input type="text" name="address" maxlength="128" size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['address']; } ?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['address']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">Email Address:</div></td>
                    <td><div align="left"><input type="text" name="email" maxlength="128" size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['email']; } ?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['email']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>                
                </tr>
                <tr>
                    <td><div align="left">Phone Number:</div></td>
                    <td><div align="left"><input type="text" name="phone" maxlength="20"  size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['phone']; } ?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['phone']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>

            </table>
            
            <div align="center">
            <input class="logoutButton" type="submit" value="Save" name="save" align="center" style="font-size:100%; width:100px; margin:10 ">
            <input class="logoutButton" type="submit" value="Cancel" name="cancel" align="center" style="font-size:100%; width:100px; margin:10 ">
            </div>
            
            <?php if(isset($_REQUEST['cancel'])) {
               if($screen == "createUser") { header('Location: createNewUser.php'); }
               if($screen == "editUser") { header('Location: editUserScreen.php'); }
               if($screen == "userManagement") { header('Location: managementUserScreen.php'); }
            }?>
            
            <?php if(isset($_REQUEST['save'])) {
                $empty = false;
                if ($_POST['firstName']=='' or $_POST['lastName']=='' or $_POST['address']=='' or $_POST['email']=='' or $_POST['phone']==''){
                    $empty = true;
                }
            }
            ?>
            
            <?php if(isset($_REQUEST['save']) && !$empty) {
                $success = OceanDB::getInstance()->add_new_person($personID,$_POST['firstName'],$_POST['lastName'],$_POST['address'],$_POST['email'],str_replace("-", "",$_POST['phone']));
                if ($success){
                    if($screen == "createUser") { header('Location: createNewUser.php'); }
                    if($screen == "editUser") { header('Location: editUserScreen.php'); }
                    if($screen == "userManagement") { header('Location: managementUserScreen.php'); }
                }
            }
            ?>
           
            
        </form> 
    </body>
    <?php   require_once("Includes/css.php");  ?>
</html>

