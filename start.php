<html>
<head>
<title>Running...</title>
<style>
        pre {
                font: 10px/1.5 Courier, "Courier New", mono;
                background-color: #efefef; border: 1px solid #ccc;
                width: 700px; margin: 7px; padding: 10px;
                white-space: pre-wrap;
                }
</style>

<!-- <script src="http://ajax.`googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script> -->
<script src="jquery.min.js"></script>

<script type="text/javascript">
    function play_sound() {
        var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', 'horse.mp3');
        audioElement.setAttribute('autoplay', 'autoplay');
        audioElement.load();
        audioElement.play();
    }

    function play_start_sound() {
        var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', 'tada.mp3');
        audioElement.setAttribute('autoplay', 'autoplay');
        audioElement.load();
        audioElement.play();
    }
</script>

<SCRIPT LANGUAGE="JavaScript">

    function confirmFunc() {
      var sic = confirm("Do you really want to stop the acquisition?");
      if (sic == true){
        return true;
      }else{
        return false; 
      }
    }
</SCRIPT>


</head>

<?php
unlink('/tmp/horse.veto'); 
unlink('/tmp/lastevent'); 
echo '<script type="text/javascript">play_start_sound();</script>';
date_default_timezone_set('Europe/Rome');

$date = date('Y-m-d H:i:s');
$run_date=date('Ymd-His');

//echo "daq1: $_POST[daq1]    daq2: $_POST[daq2]  tendina: $_POST[tendina]<br>";


if( $_POST[tendina] ) { // first time opening this page

  //Open the database

  $con=mysqli_connect("127.0.0.1","root","?cms?daq?2014","rundb_v1");
  // Check connection
  if (mysqli_connect_errno()) {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

  //Checking DAQ Configuration

  if($_POST[adc265]=='ON'){

    $adc1='ADC265 ';

  }else{

    $adc1='';

  }

  if($_POST[adc792]=='ON'){

  $adc2='ADC792 ';

  }else{

  $adc2='';

  }


  if($_POST[tdc]=='ON'){

    $tdc='TDC ';

  }else{

    $tdc='';

  }

  if($_POST[digitizer]=='ON'){

    $dig='Digitizer';

  }else{

    $dig='';

  }

  $daq_con= $adc1 . $adc2 . $tdc . $dig;


  echo "DAQ configuration: $daq_con <br>";


$daq=mysqli_query($con, "SELECT daq_conf_id FROM daq_configuration
  WHERE daq_type_description='$daq_con' AND daq_user_gate1_ns='$_POST[daq1]' AND daq_user_gate2_ns='$_POST[daq2]'");


  $daq_res =mysqli_fetch_row($daq);
  $a= $daq_res[0];
  //echo "Daq id $a <br>";

  if($a=='') {

    mysqli_query($con, "INSERT INTO daq_configuration VALUES(NULL,'$daq_con', '$_POST[daq1]', '$_POST[daq2]')");

    $ris_daq=mysqli_query($con, "SELECT MAX(daq_conf_id) FROM daq_configuration");
    $row_daq=mysqli_fetch_row($ris_daq);
    $daq_id=$row_daq[0];
  }else{

    $daq_id=$a;
  }

   //echo "Daq id $daq_id <br>";



//Checking run type

$c=mysqli_query($con,"SELECT run_type_id FROM run_type WHERE run_type_description='$_POST[tendina]'");
$row1 = mysqli_fetch_row($c);
$highest_id1 = $row1[0];

//Checking last run

$last_run = mysqli_query($con, "SELECT MAX(run_number) FROM run");

$last_row = mysqli_fetch_row($last_run);
$last_id = $last_row[0];
//echo "$last_id";


//Start reading the previuos php page


if($_POST[tendina]=='beam'){
  //echo "beam";

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

echo "Beam control query select beam_conf_id from beam_configuration where beam_particle= $part and beam_energy=$_POST[nome] AND beam_intensity=$_POST[nome1] AND beam_horizontal_width=$_POST[nome2] AND beam_vertical_width=$_POST[nome3] AND beam_horizontal_tilt=$_POST[nome4] AND beam_vertical_tilt=$_POST[nome5] <br>";

  echo "Beam configuration id $b <br>";
  
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

echo "Beam configuration id after query $last_id <br>";

    mysqli_query($con, "INSERT INTO run VALUES(null, '$_POST[run_events]',null, 1, '$last_id', '$_POST[table_oriz]', '$_POST[table_vert]', '$_POST[user_comments]',NULL, '$date', null, null, '$daq_id')");

    echo "Database filled correctly with a beam run.<br>";

  }else{ // not beam

    echo "Not a beam run. Run type: $_POST[tendina] <br>";

    mysqli_query($con, "INSERT INTO run VALUES(NULL, '$_POST[run_events]', null,'$highest_id1', NULL, '$_POST[table_oriz]', '$_POST[table_vert]', '$_POST[user_comments]',NULL, '$date', NULL, NULL, '$daq_id')");

    echo "Database filled correctly with a ";
    echo  $_POST[tendina];
    echo " run.<br>";

  }


//Acquiring last run id for the acquisition exec

$risultato = mysqli_query($con, "SELECT MAX(run_number) FROM run");

$row = mysqli_fetch_row($risultato);
$highest_id = $row[0];
echo "New run to start shortly is #  <span style='color: red'>$highest_id</span> <br>";

mysqli_close($con);


  // run number and type and also last run number
  $last_run = $last_id;
  $run_num = $highest_id;
  $run_type = $_POST[tendina];
 


  //Acquisition exec

  if( $run_num == $last_run ) { // start if valid run number

    echo "no entry created in DB. Probolems! Acquisition aborted.<br>";

  } else { 

    echo "Starting new run $run_num of type <b><span style='color: red;'>${run_type}</span></b> at $run_date <br>";

    $events_num = $_POST[run_events];

    $run_opts = '-b';
    $runname = $run_type;


    $emulator = 0;
    if( isset($_GET[emulator]) ) {

      if( $_GET[emulator] == 1 ) {
        echo "<font color=red size=huge>ATTENTION: this is an emulator session!</font><br>";
        $emulator = 1;
      }
    }


    // change only if pedestal
    if( strpos($run_type,'pedestal') !== FALSE ){

      echo "pedestal run! <br>";
      list($ped_nome, $ped_freq, $freq_unit) = split(' ', $run_type, 3);
      $run_opts = "-r ${ped_freq}"; 
      $runname = 'pedestal';
    }

    // define vars to access raw and log files
    $daqhome = "/home/cmsdaq/DAQ/VMEDAQ/";
    $run_dir  = "/home/cmsdaq/BTF/runs/";
    $log_dir  = "/home/cmsdaq/BTF/log/";

    if( $emulator == 1) {
       $run_dir = "/home/cmsdaq/BTF/emulator/";
       $log_dir = "/home/cmsdaq/BTF/emulator/";
    }

    $logfile =  $log_dir . "run_BTF_${run_num}_${run_date}_${runname}.log";
    $rawfile =  $run_dir . "run_BTF_${run_num}_${run_date}_${runname}";
    $dqm_dir =  "/run_BTF_${run_num}_${run_date}_${runname}";

    if( $emulator == 1 ) {// emulator
       $daqapp   =  $daqhome . "emuldaq.exe";
       $command = "${daqapp} $run_num ${events_num} $rawfile";
    } else {

       $daqapp =  $daqhome . "/acquire";
      $command = "${daqapp} -p 10 -f ${rawfile} -n ${events_num} ${run_opts}";
 
    }


    // change to php option
    $lines = 30;
/*
    session_start();
    $_SESSION[logfile] = $logfile;
    $_SESSION[sess_run] = $run_num;
*/


    echo "<br>";
    echo "Now executing: $command <br>"; 

    $pid = shell_exec(sprintf('%s > %s 2>&1 & echo $!', $command, $logfile));

    echo "<br>";

    echo "DAQ process id: $pid started for run $run_num<br>";

    echo "logfile available at <a href='$logfile' target='logfile'>$logfile</a><br><br>";

    echo "raw data available at: <a href='$rawfile' target='rawfile'>$rawfile</a><br><br>";

    if( $emulator != 1 ) {
      echo "make DQM plots with: <br>";
      echo "<pre>";
      echo "cd /home/cmsdaq/DQM/ ; source /home/cmsdaq/root/bin/thisroot.sh; ./makeDqmPlots.sh $rawfile";
      echo "</pre><br>";
      echo "plots available at <a target='new' href='http://192.168.189.82/DQM/$dqm_dir'>http://192.168.189.82/DQM/$dqm_dir</a><br><br>";
    }

    echo "logfile to be viewed: $logfile with $lines lines<br>";

  } // valid $run_num


} // closes if($_POST[tendina])

?>


<script>
        $(document).ready(function() {
                $("#results").load("AjaxErrorLog.php?lines=<?php echo $lines?>&logfile=<?php echo $logfile?>");
                var refreshId = setInterval(function() {
                        $("#results").load("AjaxErrorLog.php?lines=<?php echo $lines?>&logfile=<?php echo $logfile?>").fadeIn("slow");
                }, 3000); // refresh time (default = 2000 ms = 2 seconds)
        });
</script>

 <noscript><div id="response"><h1>JavaScript is required for this demo.</h1></div></noscript>
 <div id="results"></div>


  <!-- End current run at: <input type="text" name="date"  value=<?php echo date('Ymd-His');?> /><br> -->

  <FORM METHOD="POST" ACTION="stop.php?emulator=<?php echo $emulator;?>" NAME="stop">
<!--
  Reason for ending run <?php echo $run_num;?>: <input name="fin_comment" size="250"><br>
-->
  <input type="hidden" name="daqpid" value=<?php echo $pid;?> />
  <input type="hidden" name="endrun" value=<?php echo $run_num;?> />
  <INPUT TYPE="SUBMIT" onclick=" return confirmFunc()" VALUE='Stop'>
</FORM>


</html>
