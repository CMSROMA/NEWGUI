<html>
<head>
<title>Stopping acquisition...</title>

<?php

date_default_timezone_set('Europe/Rome');
$end_date = date('Y-m-d H:i:s');

  if( $_POST["endrun"] ) {
   // create file to stop daq application
   //touch("/Users/rahatlou/Sites/imcp/acq.stop");
   touch("/tmp/acq.stop");

   $endrun = $_POST["endrun"];

   echo "You have requested to end run $endrun for following reason:<br>";
   echo $_POST["comment"];
   echo "<br>";
   echo "stopping all running processes. please wait ... <br>";
  }
  echo "</p>";
?>

<p>
<form action="DAQ.php" method="post">

<?php if( file_exists("/tmp/acq.stop") ) { ?>

  <input type="button" value="Run <?php echo $endrun ?> in progress... please wait">
  <script>
     function refresh() {
             window.location.reload(true);
     }
     setTimeout(refresh, 2000);
  </script>

<?php 

     } else {
       $endDate = date('Y-m-d H:i:s');
       echo "run $endrun stopped at $endDate<br>";

//echo "Run Stopped.";

//Open the database

$con=mysqli_connect("127.0.0.1","root","?cms?daq?2014","rundb_test_v1");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

//$risultato = mysqli_query($con, "SELECT MAX(run_number) FROM run");

//$row = mysqli_fetch_row($risultato);
//$highest_id = $row[0];

mysqli_query($con, "UPDATE run SET run_endtime='$enddate' WHERE run_number='$endrun'");


?>
  <input type="submit" value="Back to frontend">
<?php } ?>

</form>
</p>

</html>
