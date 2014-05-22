<?php

include './global_variables.php';

$lines = 30;
$logfile = $config[daqhome] . "/IMCP/log/current_logfile";
if (file_exists($logfile)) {
  $logfile = readlink($logfile);
}
$rawfile = preg_replace("/\.log/", "", $logfile);
$rawfile = preg_replace("/\/log\//", "/runs/", $rawfile);
$output = array();

//
// tail the logfile
//
if (file_exists($logfile)) {
  $cmd = "tail -$lines $logfile"; 
  exec("$cmd 2>&1", $output);
}

echo '<div class="log-entry"><pre>';

$still_running = 1;

//
// get the current time, and read the file containing the last valid reading
//
$now = time();
if (file_exists("/tmp/lastevent")) {
  $lastevt = file_get_contents("/tmp/lastevent");
}
$tarray = explode(" ", $lastevt);
$tdiff = $now - $tarray[0];
$lastevt = $tarray[1];
if ($tdiff > 300) {
   echo "============= Run paused since long...you may want to stop it.\n\n";
}

// 
// loop on strings got from logfile
//
foreach ($output as $outputline) {
 echo ("$outputline");
 $pos = strpos($outputline, "stopped");
 if ($pos != false) {
    // 
    // the run stopped
    //
    $still_running = 0;
 } 
 $evtnum = "";
 //
 // get the current number of events to launch operations
 //
 if (preg_match("/Event number: /", $outputline)) {
    $evtnum = preg_replace("/.*Event number: +/", "", $outputline);
    $evtnum = preg_replace("/ .*/", "", $evtnum);
    $freq = 5000;
    if ($evtnum < 10000) {
       $freq = 1000;
    }
    if ((intval($evtnum) % $freq) == 0) {
       //
       // run DQM
       //
       exec("/var/www/GUI/runDQM.sh $rawfile > /dev/null &"); 
       echo("----------------------");
       echo(" *** DQM just ran *** ");
       echo("----------------------");
    }
    if (intval($evtnum) > intval($lastevt)) {
      //
      // write the last valid entry into a file for further usage
      //
      file_put_contents("/tmp/lastevent", sprintf("%d %d\n", $now, $evtnum)); 
    }
 }
 echo ("\n");
}
echo '</pre></div>';

echo "DQM run command: /var/www/GUI/runDQM.sh $rawfile <br>";

if (($still_running == 0) && (!file_exists('/tmp/horse.veto'))) {
   //
   // run stopped: play sound
   //
   echo "<audio controls autoplay='autoplay' style='display:none'>";
   echo "<source src='horse.mp3' type='audio/mpeg'>";
   echo "</audio>";
   touch('/tmp/horse.veto');
   sleep(3);
   echo "Running DQM at the end of the run<br>";
   exec("/var/www/GUI/runDQM.sh $rawfile > /dev/null &"); 
}
?>
