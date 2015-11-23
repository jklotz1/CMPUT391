<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>User Documentation</title>
    </head>
    <body>

        <?php
            require_once 'Includes/Parsedown.php';
            
            $parsedown = new Parsedown();
            
            $text = file_get_contents('Includes/userDocumentation.txt');
            
            echo $parsedown->text($text);
            
            require_once("Includes/css.php");  
        ?>
    </body>
</html>
