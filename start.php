<SCRIPT LANGUAGE="JavaScript"><!--

    function confirmFunc() {
      var sic = confirm("Do you really want to stop the acquisition?");

    if (sic == true){
    
    return true;
    }else{
    return false; 
    }
    }

    
//-->
    </SCRIPT>
<html>
<head>
<title>Running...</title>
</head>

<?php
date_default_timezone_set('Europe/Rome');

$date = date('Y-m-d H:i:s');
$run_date=date('Ymd-His');

if( $_POST[tendina] ) {


//Open the database

$con=mysqli_connect("127.0.0.1","root","?cms?daq?2014","rundb_test_v1");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

//Start reading the previuos php page

$c=mysqli_query($con,"SELECT run_type_id FROM run_type WHERE run_type_description='$_POST[tendina]'");
$row1 = mysqli_fetch_row($c);
$highest_id1 = $row1[0];

if($highest_id1==5){


  if($_POST[electr]=='ON'){

    $part='electron';

  }

  if($_POST[positr]=='ON'){

    $part='positron';
  }

if($_POST[photon]=='ON'){

  $part='photon';
  }



$contr=mysqli_query($con, "SELECT beam_conf_id FROM beam_configuration
  WHERE beam_particle='$part' AND beam_energy='$_POST[nome]' AND
  beam_intensity='$_POST[nome1]' AND
  beam_horizontal_width='$_POST[nome2]' AND
  beam_vertical_width='$_POST[nome3]' AND
  beam_horizontal_tilt='$_POST[nome4]' AND beam_vertical_tilt='$_POST[nome5]'");

  $result =mysqli_fetch_row($contr);
  $b= $result[0];
  
  if($b==''){
    mysqli_query($con, "INSERT INTO beam_configuration
  VALUES(NULL,'$part', '$_POST[nome]','$_POST[nome1]',
  '$_POST[nome2]', '$_POST[nome3]', '$_POST[nome4]', '$_POST[nome5]')");

    $ris_beam=mysqli_query($con, "SELECT MAX(beam_conf_id) FROM beam_configuration");
    $row_beam=mysqli_fetch_row($ris_beam);
    $last_id=$row_beam[0];
  }else{

    $last_id=$b;
}


mysqli_query($con, "INSERT INTO run VALUES(null, '$_POST[run_events]', 5, '$last_id', '$_POST[table_oriz]', '$_POST[table_vert]', '$_POST[user_comments]', '$date', null, null, '$_POST[daq_conf]')");

echo "Database filled correctly with a beam run.<br>";
}else{

mysqli_query($con, "INSERT INTO run VALUES(NULL, '$_POST[run_events]', '$highest_id1', NULL, '$_POST[table_oriz]', '$_POST[table_vert]', '$_POST[user_comments]', '$date', NULL, NULL, '$_POST[daq_conf]')");

echo "Database filled correctly with a ";
echo  $_POST[tendina];
echo " run.<br>";


}

//Acquiring last run id for the acquisition exec

$risultato = mysqli_query($con, "SELECT MAX(run_number) FROM run");

$row = mysqli_fetch_row($risultato);
$highest_id = $row[0];
echo "Created DB entry for run $highest_id <br>";

mysqli_close($con);

  // run number to be retrieved from sqlite DB
  $run_num = $highest_id;
  //$startDate = $_POST["date"];

//Acquisition exec

echo "Starting new run $run_num at $run_date <br>";

  $stopfile = '/tmp/acq.stop';

  $flag = file_exists($stopfile);

  $events_num = $_POST[run_events1];

  $comm = "/tmp/acquire -p 10 -f runs/run${run_num}_${run_date}  -n ${events_num} 2>&1 > runs/run${run_num}_${run_date} -n ${events_num}.log &";

  echo "executing |$comm| <br>"; 

  //exec("./acquire -p 10 -f runs/run'$highest_id'_'$run_date' -n '$_POST[run_events]' 2>&1 > runs/run'$highest_id'_'$run_date' -n '$_POST[run_events].log &");
  $pid = shell_exec("/tmp/daq.exe $run_num $run_date >> /dev/null &");
  echo "<br>";

  echo "DAQ process started for run $run_num<br>";

  } // closes if($_POST[tendina]) 
?>

  <!-- End current run at: <input type="text" name="date"  value=<?php echo date('Ymd-His');?> /><br> -->
  <FORM METHOD="POST" ACTION="stop.php" NAME="stop">
  Reason for ending run <?php echo $run_num;?>: <textarea name="comment" rows=3 cols=100></textarea><br>
  <input type="hidden" name="endrun" value=<?php echo $run_num;?> />
  <INPUT TYPE="SUBMIT" onclick=" return confirmFunc()" VALUE='End Run'>


</FORM>

</html>
