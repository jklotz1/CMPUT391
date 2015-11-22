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
$sensor = $_SESSION['sensor'];
$yearsR = $_SESSION["years"];
$quartersR = $_SESSION["quarters"];
$monthsR = $_SESSION["months"];
$weeksR = $_SESSION["weeks"];
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <!--check the role of the user - only scientist are allowed to access this section-->
        <?php require_once 'Includes/db.php';?>     
            
        <form name="Report" method="post">
            <input class="logoutButton" type="submit" value="Home" style="font-size:100%; width:100px; margin:10" name="home">
            <input class="logoutButton" type="submit" value="Change Sensor" style="font-size:100%; width:200px; margin:10" name="change">

            <br><br>
            <!--return to home screen when "home" button is pressed-->
            <?php if (isset($_POST['home'])) { header('Location: homeScreen.php'); }?> 
            <?php if (isset($_POST['change'])) { header('Location: analysisScreen.php'); }?> 

            
            <?php
                OceanDB::getInstance()->create_view_data($sensor);
                $dataDisplayYears = OceanDB::getInstance()->get_data_to_display_year();
                $sensorInfo = OceanDB::getInstance()->get_sensor_by_ID($sensor);
            ?>
            <h1 align="center" style="font-size:150%; margin-bottom:50">Sensor:<?php echo $sensorInfo["SENSOR_ID"];?><br>Location:<?php echo $sensorInfo["LOCATION"];?> </h1>

            <table name="Years" class="searchTable" width="100" border="1" style="margin-top:50">
                <tr>
                    <td><div align="center">Year</div></td>
                    <td><div align="center">Average</div></td>
                    <td><div align="center">Maximum</div></td>
                    <td><div align="center">Minimum</div></td>
                </tr>
                <?php while($dataYears = oci_fetch_array($dataDisplayYears,OCI_BOTH)){ ?>
                    <tr>
                        <td><div align="center"><input type="submit" value="<?php echo get_date($dataYears,'yearly');?>" name="year"></td>        
                        <td><div align="center"><?php echo $dataYears["AVERAGE"]; ?></td>
                        <td><div align="center"><?php echo $dataYears["MAXIMUM"];?></td>
                        <td><div align="center"><?php echo $dataYears["MINIMUM"];?></td>
                    </tr>
                    <?php if (($key = array_search($dataYears["YEAR"], $yearsR)) !== false){ 
                        $dataDisplayQuarter = OceanDB::getInstance()->get_data_to_display_quarter($dataYears["YEAR"]);?>
                        <tr>
                        <td colspan="4">
                        <table class="searchTable" width="100" border="1">
                            <tr>
                                <td><div align="center">Quarter</div></td>
                                <td><div align="center">Average</div></td>
                                <td><div align="center">Maximum</div></td>
                                <td><div align="center">Minimum</div></td>
                            </tr>
                            <?php while($dataQuarter = oci_fetch_array($dataDisplayQuarter,OCI_BOTH)){ ?>
                                <tr>
                                    <td><div align="center"><input type="submit" value="<?php echo get_date($dataQuarter,'quarterly');?>" name="quarters"></td>        
                                    <td><div align="center"><?php echo $dataQuarter["AVERAGE"]; ?></td>
                                    <td><div align="center"><?php echo $dataQuarter["MAXIMUM"];?></td>
                                    <td><div align="center"><?php echo $dataQuarter["MINIMUM"];?></td>
                                </tr>
                                <?php if (($key = array_search(get_date($dataQuarter,'quarterly'), $quartersR)) !== false){ 
                                $dataDisplayMonth = OceanDB::getInstance()->get_data_to_display_months($dataQuarter["QUARTER"],$dataQuarter["YEAR"]);?>
                                <tr>
                                <td colspan="4">
                                <table class="searchTable" width="100" border="1">
                                    <tr>
                                        <td><div align="center">Month</div></td>
                                        <td><div align="center">Average</div></td>
                                        <td><div align="center">Maximum</div></td>
                                        <td><div align="center">Minimum</div></td>
                                    </tr>
                                    <?php while($dataMonth = oci_fetch_array($dataDisplayMonth,OCI_BOTH)){ ?>
                                        <tr>
                                            <td><div align="center"><input type="submit" value="<?php echo get_date($dataMonth,'monthly');?>" name="months"></td>        
                                            <td><div align="center"><?php echo $dataMonth["AVERAGE"]; ?></td>
                                            <td><div align="center"><?php echo $dataMonth["MAXIMUM"];?></td>
                                            <td><div align="center"><?php echo $dataMonth["MINIMUM"];?></td>
                                        </tr>
                                        <?php if (($key = array_search(get_date($dataMonth,'monthly'), $monthsR)) !== false){ 
                                            $dataDisplayWeek = OceanDB::getInstance()->get_data_to_display_weeks($dataMonth["MONTH"],$dataMonth["YEAR"]);?>
                                            <tr>
                                            <td colspan="4">
                                            <table class="searchTable" width="100" border="1">
                                                <tr>
                                                    <td><div align="center">Week</div></td>
                                                    <td><div align="center">Average</div></td>
                                                    <td><div align="center">Maximum</div></td>
                                                    <td><div align="center">Minimum</div></td>
                                                </tr>
                                                <?php while($dataWeek = oci_fetch_array($dataDisplayWeek,OCI_BOTH)){ ?>
                                                    <tr>
                                                        <td><div align="center"><input type="submit" value="<?php echo get_date($dataWeek,'weekly');?>" name="weeks"></td>        
                                                        <td><div align="center"><?php echo $dataWeek["AVERAGE"]; ?></td>
                                                        <td><div align="center"><?php echo $dataWeek["MAXIMUM"];?></td>
                                                        <td><div align="center"><?php echo $dataWeek["MINIMUM"];?></td>
                                                    </tr>
                                                    <?php if (($key = array_search(get_date($dataWeek,'weekly'), $weeksR)) !== false){ 
                                                        $dataDisplayDay = OceanDB::getInstance()->get_data_to_display_days($dataWeek["FIRSTDAY"],$dataWeek["MONTH"],$dataWeek["YEAR"]);?>
                                                        <tr>
                                                        <td colspan="4">
                                                        <table class="searchTable" width="100" border="1">
                                                            <tr>
                                                                <td><div align="center">Day</div></td>
                                                                <td><div align="center">Average</div></td>
                                                                <td><div align="center">Maximum</div></td>
                                                                <td><div align="center">Minimum</div></td>
                                                            </tr>
                                                            <?php while($dataDay = oci_fetch_array($dataDisplayDay,OCI_BOTH)){ ?>
                                                                <tr>
                                                                    <td><div align="center"><input type="submit" value="<?php echo get_date($dataDay,'daily');?>" name="daily"></td>        
                                                                    <td><div align="center"><?php echo $dataDay["AVERAGE"]; ?></td>
                                                                    <td><div align="center"><?php echo $dataDay["MAXIMUM"];?></td>
                                                                    <td><div align="center"><?php echo $dataDay["MINIMUM"];?></td>
                                                                </tr>
                                                        <?php } ?>
                                                        </table>
                                                        </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            </table>
                                            </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                </table>
                                </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                        </table>
                        </td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </table>
            
            
            <?php if(isset($_REQUEST["year"])){
                if (($key = array_search($_POST["year"], $yearsR)) !== false) {
                    unset($yearsR[$key]);
                } else {
                    array_push($yearsR, $_POST["year"]);
                }
                $_SESSION["years"] = $yearsR;
                header('Location: reportScreen.php');
            }?>
            
            <?php if(isset($_REQUEST["quarters"])){
                if (($key = array_search($_POST["quarters"], $quartersR)) !== false) {
                    unset($quartersR[$key]);
                } else {
                    array_push($quartersR, $_POST["quarters"]);
                }               
                $_SESSION["quarters"] = $quartersR;
                header('Location: reportScreen.php');
            }?>
            
            <?php if(isset($_REQUEST["months"])){
                if (($key = array_search($_POST["months"], $monthsR)) !== false) {
                    unset($monthsR[$key]);
                } else {
                    array_push($monthsR, $_POST["months"]);
                }               
                $_SESSION["months"] = $monthsR;
                header('Location: reportScreen.php');
            }?>
            
            <?php if(isset($_REQUEST["weeks"])){
                if (($key = array_search($_POST["weeks"], $weeksR)) !== false) {
                    unset($weeksR[$key]);
                } else {
                    array_push($weeksR, $_POST["weeks"]);
                }               
                $_SESSION["weeks"] = $weeksR;
                header('Location: reportScreen.php');
            }?>
            
            
            <?php OceanDB::getInstance()->drop_view_data(); ?>
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
        else if ($date =='weekly'){return $data["FIRSTDAY"]."-".last_day($data["FIRSTDAY"],$data["LASTDAY"])."/".$data["MONTH"]."/".$data["YEAR"]; }
        else if ($date =='daily'){ return $data["DAY"]."/".$data["MONTH"]."/".$data["YEAR"]; }
        return null;
    }
    
    function last_day($first, $lastOfMonth){
        if ($first+6 > $lastOfMonth) { return $lastOfMonth; }
        else { return $first+6; }
    }
    
?>