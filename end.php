<html>
<head>
<title>Ending run</title>

</head>

<body>


<?php
    $emulator = 0;
    if( isset($_GET[emulator]) ) {

      if( $_GET[emulator] == 1 ) {
        echo "<font color=red size=large>ATTENTION: this is an emulator session!</font><br>";
        $emulator = 1;
      }
    }


date_default_timezone_set('Europe/Rome');
$end_date = date('Y-m-d H:i:s');

//Open the database

$con=mysqli_connect("127.0.0.1","root","?cms?daq?2014","rundb_v1");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

$risultato = mysqli_query($con, "SELECT MAX(run_number) FROM run");

$row = mysqli_fetch_row($risultato);
$highest_id = $row[0];

echo "Updating DB for run ${highest_id} with final comment: <br><pre>${_POST[fin_comment]}</pre><br>";

mysqli_query($con, "UPDATE run SET run_endtime='$end_date' WHERE run_number='$highest_id'");
mysqli_query($con, "UPDATE run SET run_end_user_comment='$_POST[fin_comment]' WHERE run_number='$highest_id'");

?>
<p>
<form action="DAQ.php?emulator=<?php echo $emulator;?>" method="post">
<input type="submit" value="Back to frontend">

</form>
</p>

</body>


</html>
