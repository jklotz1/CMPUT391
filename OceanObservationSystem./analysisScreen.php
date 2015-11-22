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
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <form name="logon" method="post">
         <input class="logoutButton" type="submit" value="Help" name="Help">
                   <?php 
                   if (isset($_POST['Help'])) { header('Location: userDocumentation.php'); }
                   ?>
        </form>
        <!--check the role of the user - only administrators are allowed to access this section-->
        <?php require_once 'Includes/db.php';
            $role = OceanDB::getInstance()->get_user_role($user);
        ?>
        
        <!--Bring up Management screen if administrator else deny access and display message-->
        <?php if ($role == 's') { ?>
            <h1 align="left" style="font-size: 150%">Data Analysis</h1>
            <?php $allow = true; ?>
        <?php } else { ?>
            <h1 align="left" style="font-size: 175%; color: red">Access denied</h1>
            <?php $allow = false; ?>
        <?php } ?>
            
        <form name="Analysis" method="post">
        <input class="logoutButton" type="submit" value="Home" style="font-size:100%; width:100px; margin:10" name="home">
        <br><br>
        <!--return to home screen when "home" button is pressed-->
        <?php if (isset($_POST['home'])) { header('Location: homeScreen.php'); }?> 

        <?php if ($allow) { ?>

        <!--display user management screen when "Edit users" button is pressed-->
            
            <?php $sensors = OceanDB::getInstance()->get_subscribed_sensors($user);?>
            
            <table width="250" align="center">
                <tr>
                    <td><div align="left">Select Sensor:</div></td>
                    <td><div align="left"><select name="sensor"><option value=""></option> <?php while($sen = oci_fetch_array($sensors,OCI_ASSOC)){?>
                                                                                                    <option value="<?php echo $sen['SENSOR_ID']; ?>"<?php if(isset($_POST['show'])&&$_POST['sensor']== $sen["SENSOR_ID"]){?>selected<?php }?>><?php 
                                                                                                        echo $sen["SENSOR_ID"];?>
                                                                                                    </option><?php }?></select></div></td>                </tr>
                <tr>
                </tr>
                <tr>
                    <td><div align="left">Select Time Interval:</div></td>
                    <td><div align="left"><input type="radio" name="time" value="yearly" <?php if(isset($_POST['show'])&&$_POST['time']=='yearly'){ ?>checked<?php } ?>>Yearly</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="time" value="quarterly" <?php if(isset($_POST['show'])&&$_POST['time']=='quarterly'){ ?>checked<?php } ?>>Quarterly</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="time" value="monthly" <?php if(isset($_POST['show'])&&$_POST['time']=='monthly'){ ?>checked<?php } ?>>Monthly</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="time" value="weekly" <?php if(isset($_POST['show'])&&$_POST['time']=='weekly'){ ?>checked<?php } ?>>Weekly</div></td>
                </tr>
                <tr>
                    <td><div align="left"></div></td>
                    <td><div align="left"><input type="radio" name="time" value="daily" <?php if(isset($_POST['show'])&&$_POST['time']=='daily'){ ?>checked<?php } ?>>Daily</div></td>
                </tr>

            </table>
            <?php if(isset($_REQUEST['show'])) {
                $empty = false;
                if ($_POST['sensor']=='' or $_POST['time']==''){
                    $empty = true;
                }
            }
            ?>
            <div align="center">
                <?php if($_REQUEST['show']&&$empty){ echo "<div align='center' style='color:red;'>Missing Fields. Fill out all fields.</div>";}?>
                <input class="logoutButton" type="submit" value="Show Data" name="show" align="center" style="margin-bottom:0;margin-top:15">
            </div>
            <?php if(isset($_REQUEST['show'])) {
                $empty = false;
                if ($_POST['sensor']=='' or $_POST['time']==''){
                    $empty = true;
                }
            }
            ?>
            
            <?php if(isset($_REQUEST['show'])&&!$empty){
                $dataDisplay = OceanDB::getInstance()->get_data_to_display($_POST["sensor"],$_POST["time"]);
                $sensorInfo = OceanDB::getInstance()->get_sensor_by_ID($_POST["sensor"]);
            ?>
            <h1 align="center" style="font-size:150%; margin-bottom:50">Sensor:<?php echo $sensorInfo["SENSOR_ID"];?><br>Location:<?php echo $sensorInfo["LOCATION"];?> </h1>
            
            
                <!--get all user accounts associated with username-->            
                <table class="searchTable" width="100" border="1" style="margin-top:50">
                    <tr>
                        <td><div align="center"><?php echo get_time_label($_POST["time"])?></div></td>
                        <td><div align="center">Average</div></td>
                        <td><div align="center">Maximum</div></td>
                        <td><div align="center">Minimum</div></td>
                    </tr>
                    
                    <?php
                    $maximum=null;
                    $minimum=null;
                    $sum=0;
                    $average=null;
                    $count=0;
                    $week=null;
                    $month=null;
                    $year=null;
                    $dayofweek=null;
                    $date=null;
                    ?>
                    <?php while($data = oci_fetch_array($dataDisplay,OCI_BOTH)){ ?>
                        <?php if($_POST["time"] == 'weekly'){
                            if ($week == null) {
                                $maximum=$data['VALUE'];
                                $minimum=$data['VALUE'];
                                $sum=$data['VALUE'];
                                $count=1;
                                $week=$data['WEEK'];
                                $month=$data['MONTH'];
                                $year=$data['YEAR'];
                                $dayofweek=$data['DAYOFWEEK'];
                                $date=$data['DATE_CREATED'];
                            }
                            elseif($week==$data['WEEK'] && $year==$data['YEAR']){
                                $maximum= (($data['VALUE']>$maximum)?$data['VALUE']:$maximum);
                                $minimum= (($data['VALUE']<$minimum)?$data['VALUE']:$minimum);
                                $sum= $sum+$data['VALUE'];
                                $count=$count+1;
                            }
                            elseif($week=='53' and $data['WEEK']=='0'){
                                $maximum= (($data['VALUE']>$maximum)?$data['VALUE']:$maximum);
                                $minimum= (($data['VALUE']<$minimum)?$data['VALUE']:$minimum);
                                $sum= $sum+$data['VALUE'];
                                $count=$count+1;
                                $week=$data['WEEK'];
                                $year=$data['YEAR'];
                            }
                            else   
                            { 
                                ?>
                                <tr>
                                    <td><div align="center"><?php echo get_week($date,$dayofweek);?></td>
                                    <td><div align="center"><?php echo $sum/$count;?></td>
                                    <td><div align="center"><?php echo $maximum;?></td>
                                    <td><div align="center"><?php echo $minimum;?></td>
                                </tr>
                                <?php
                                $maximum=$data['VALUE'];
                                $minimum=$data['VALUE'];
                                $sum=$data['VALUE'];
                                $count=1;
                                $week=$data['WEEK'];
                                $month=$data['MONTH'];
                                $year=$data['YEAR'];
                                $dayofweek=$data['DAYOFWEEK'];
                                $date=$data['DATE_CREATED'];
                            }
                        
                        
                        ?>
                    <?php } else { ?>
                    <tr>
                        <td><div align="center"><?php echo get_date($data,$_POST["time"]);?></td>
                        <td><div align="center"><?php echo $data["AVERAGE"];?></td>
                        <td><div align="center"><?php echo $data["MAXIMUM"];?></td>
                        <td><div align="center"><?php echo $data["MINIMUM"];?></td>
                    </tr>
                    <?php } ?>
                    <?php } 
                    if($_POST["time"] == 'weekly'){ ?>
                        <tr>
                            <td><div align="center"><?php echo get_week($date,$dayofweek);?></td>
                            <td><div align="center"><?php echo $sum/$count;?></td>
                            <td><div align="center"><?php echo $maximum;?></td>
                            <td><div align="center"><?php echo $minimum;?></td>
                        </tr>
                    <?php } ?>   
                </table>
                
            <?php } ?>
        <?php } ?>
        </form>              
                            
    </body>
    <?php   require_once("Includes/css.php");  ?>
</html>

<?php
    function get_time_label($date){
        if ($date =='yearly'){ return "Year"; }
        else if ($date =='quarterly'){ return "Quarter"; }
        else if ($date =='monthly'){ return "Month"; }
        else if ($date =='weekly'){ return "Week"; }
        else if ($date =='daily'){ return "Day"; }
        return null;
    }
    
    function get_date($data,$date){
        if ($date =='yearly'){ return $data["YEAR"]; }
        else if ($date =='quarterly'){ return "Q".$data["QUARTER"]."/".$data["YEAR"]; }
        else if ($date =='monthly'){ return $data["MONTH"]."/".$data["YEAR"]; }
        else if ($date =='daily'){ return $data["DAY"]."/".$data["MONTH"]."/".$data["YEAR"]; }
        return null;
    }
    
    function get_week($date,$dayOfWeek){
        $daybefore = $dayOfWeek-1;
        $dayafter = 7-$dayOfWeek;
        return  date('d/m/Y', strtotime("-$daybefore day",strtotime($date)))."-". date('d/m/Y', strtotime("+$dayafter day",strtotime($date)));

    }
?>