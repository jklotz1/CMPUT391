<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
ob_start();
session_start();
//get the previous screen to know where to return to
$screen = $_SESSION['screen'];
//personID of the person account to edit
$personEdit = $_SESSION['personToEdit'];

?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <!--get access to OceanDB class to connect and query the database-->
        <?php require_once 'Includes/db.php'; ?>
        <!--get the personal account information of the person to edit-->
        <?php $person = OceanDB::getInstance()->get_person_by_id($personEdit);?>
        
        <!--display the form to edit the person information-->
        <!--this form is filled out with the information retrieved from the database-->
        <h1 align="center" style="font-size: 175%">Edit Personal Profile: <?php echo $person['FIRST_NAME']." ".$person['LAST_NAME']?></h1>        
        <form name="userCreate" method="post">
            <table width="400" align="center">
                <tr>
                    <td><div align="left">Person ID:</div></td>
                    <td><div align="left"><input type="text" name="personID" size="30" value="<?php echo $person["PERSON_ID"];?>" readonly></div></td>                    
                </tr>
                <tr>
                    <td><div align="left">First Name:</div></td>
                    <td><div align="left"><input type="text" name="firstName" maxlength="24" size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['firstName']; } else { echo $person['FIRST_NAME']; }?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['firstName']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">Last Name:</div></td>
                    <td><div align="left"><input type="text" name="lastName" maxlength="24"  size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['lastName']; } else { echo $person['LAST_NAME']; }?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['lastName']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>                
                </tr>
                <tr>
                    <td><div align="left">Address:</div></td>
                    <td><div align="left"><input type="text" name="address" maxlength="128" size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['address']; } else { echo $person['ADDRESS']; }?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['address']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>
                <tr>
                    <td><div align="left">Email Address:</div></td>
                    <td><div align="left"><input type="text" name="email" maxlength="128" size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['email']; } else { echo $person['EMAIL']; }?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['email']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>                
                </tr>
                <tr>
                    <td><div align="left">Phone Number:</div> <div align="left" style="font-size:75%">(xxx-xxx-xxxx)</div></td>
                    <td><div align="left"><input type="text" name="phone" maxlength="20"  size="30" value="<?php if(isset($_REQUEST['save'])){ echo $_POST['phone']; } else { echo $person['PHONE']; }?>"></div></td>                    
                    <td><div align="left" style="color:red; display:<?php if(isset($_POST['save'])&&$_POST['phone']==''){?>inline <?php } else { ?> none <?php } ?>" >*Required</div></td>
                </tr>

            </table>
            
            <!--user action - "save" to update the person account, "cancel" to discard changes-->
            <div align="center">
            <input class="logoutButton" type="submit" value="Save" name="save" align="center" style="font-size:100%; width:100px; margin:10 ">
            <input class="logoutButton" type="submit" value="Cancel" name="cancel" align="center" style="font-size:100%; width:100px; margin:10 ">
            </div>
            
            <!--"cancel" clicked - return to previous page and discard changes-->
            <?php if(isset($_REQUEST['cancel'])) {
               if($screen == "createUser") { header('Location: createNewUser.php'); } 
               if($screen == "userManagement") { header('Location: managementUserScreen.php'); }
            }?>
            
            <!--"save" clicked - do checks on fields-->
            <!--check: fields cannot be empty-->
            <?php if(isset($_REQUEST['save'])) {
                $empty = false;
                if ($_POST['firstName']=='' or $_POST['lastName']=='' or $_POST['address']=='' or $_POST['email']=='' or $_POST['phone']==''){
                    $empty = true;
                }
            }
            ?>
            
            <!--update the person if all fields are filled out and return to previous page-->
            <?php if(isset($_REQUEST['save']) && !$empty) {
                $success = OceanDB::getInstance()->update_person($person["PERSON_ID"],$_POST['firstName'],$_POST['lastName'],$_POST['address'],$_POST['email'],str_replace("-", "",$_POST['phone']));
                if ($success){
                    if($screen == "createUser") { header('Location: createNewUser.php'); }
                    if($screen == "userManagement") { header('Location: managementUserScreen.php'); }
                }
            }
            ?>
            
        </form> 
    </body>
    <!--used for graphical interface-->
    <?php   require_once("Includes/css.php");  ?>
</html>

