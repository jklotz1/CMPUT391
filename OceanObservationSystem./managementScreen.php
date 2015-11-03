<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
session_start();
$user = $_SESSION['user'];

?>

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
        <?php  
            require_once 'Includes/db.php';
            $role = OceanDB::getInstance()->get_user_role($user);
        ?>
        <?php if ($role == 'a') { ?>
            <h1 align="center" style="font-size: 200%">Sensor and User Management Center</h1>
        <?php } else { ?>
            <h1 align="center" style="font-size: 200%">Access denied</h1>
        <?php } ?>
        
    </body>
</html>
