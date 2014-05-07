<html>
<head>
<title>Stopping acquisition...</title>

</head>


<body>

<?php

function isRunning($pid)
{
    try {
        $result = shell_exec(sprintf('ps %d', $pid));
        if(count(preg_split("/\n/", $result)) > 2) {
            return true;
        }
    } catch(Exception $e) {}

    return false;
}

date_default_timezone_set('Europe/Rome');
$end_date = date('Y-m-d H:i:s');

    $emulator = 0;
    if( isset($_GET[emulator]) ) {

      if( $_GET[emulator] == 1 ) {
        echo "<font color=red size=large>ATTENTION: this is an emulator session!</font><br>";
        $emulator = 1;
      }
    }

   // define stop file

   if( $emulator == 1 ) {
     $daqhome = "/tmp/";
   } else {
     $daqhome = "/home/cmsdaq/DAQ/VMEDAQ/";
   }

   $stopfile = $daqhome . "/acq.stop";

  if( $_POST["endrun"] ) {

  
   //touch($stopfile);
   exec("touch $stopfile", $output);
   exec("chmod ug+w $stopfile", $output);
   exec("chown www-data.www-data $stopfile", $output);

   $endrun = $_POST["endrun"];
   $pid = $_POST["daqpid"];

   echo "You have requested to end run $endrun with process id $pid <br>";
   echo "<br>";
   echo "stopping all running processes. please wait ... <br>";
  }
  echo "</p>";
?>

<p>

<?php 

    if( file_exists($stopfile) && isRunning($pid) ) { 


?>


  <input type="button" value="Run <?php echo $endrun ?> in progress... please wait">
  <script>
     function refresh() {
             window.location.reload(true);
     }
     setTimeout(refresh, 2000);
  </script>

<?php 

     } else {
       if( !isRunning($pid) ) {
         echo "DAQ processed not running. check if run ended correctly<br>";
         echo "Forcing removal of acq.stop<br>";
       }

       $endDate = date('Y-m-d H:i:s');
       echo "run $endrun stopped at $endDate<br>";

//echo "Run Stopped.";
?>

  <FORM METHOD="POST" ACTION="end.php?emulator=<?php echo $emulator;?>" NAME="end">
  Reason for ending the run:<input name="fin_comment" size="250"><br>
  <input type="hidden" name="endrun" value=<?php echo $run_num;?> />
  <INPUT TYPE="SUBMIT" onclick=" return confirmFunc()" VALUE='End Run'>

<?php } ?>

</form>
</p>

</body>

</html>
