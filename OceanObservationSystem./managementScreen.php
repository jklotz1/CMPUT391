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
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php  
            require_once 'Includes/db.php';
            $role = OceanDB::getInstance()->get_user_role($user);
            echo $user;
        ?>
        <?php if ($role == 'a') { ?>
            <h1 align="left" style="font-size: 150%">Sensor and User Management Center</h1>
        <?php } else { ?>
            <h1 align="left" style="font-size: 150%; color: red">Access denied</h1>
        <?php } ?>
        <form name="management" method="post">
            <table width="300" border="1" align="left" cellpadding="25" style="margin: ">  
                <tr> 
                <th>
                    <input type="submit" value="Sensor Management" name="pressed">
                    Table1              
                </th>
                </tr>
            </table>  
        </form>
        <form name="management" method="post">
            <table width="300" border="1" align="left" cellpadding="25">  
                <tr> 
                <th>
                    <input type="submit" value="User Management" name="pressed">
                    Table2              
                </th>
                </tr>
            </table>  
        </form>
    </body>
</html>
