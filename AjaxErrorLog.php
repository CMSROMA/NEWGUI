<?php

$lines = $_GET[lines];
$logfile = $_GET[logfile];
$rawfile = preg_replace("/\.log/", "", $logfile);
$rawfile = preg_replace("/\/log\//", "\/runs\/", $rawfile);

//$logfile = '/Users/rahatlou/run.log';

//
// tail the logfile
//
$cmd = "tail -$lines $logfile"; 
exec("$cmd 2>&1", $output);

echo '<div class="log-entry"><pre>';

$still_running = 1;

//
// get the current time, and read the file containing the last valid reading
//
$now = time();
$lastevt = file_get_contents("/tmp/lastevent");
$tarray = explode(" ", $lastevt);
$tdiff = $now - $tarray[0];
$lastevt = $tarray[1];
echo "DEBUGGING: $tdiff $lastevt\n"; // remove this line when successfull
if ($tdiff > 300) {
   echo "============= Run paused since long...you may want to stop it.\n";
   echo "              To do that, stop the run and remove the pause...\n";
}

// 
// loop on strings got from logfile
//
foreach($output as $outputline) {
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
    if ((intval($evtnum) % 1000) == 0) {
       //
       // run DQM
       //
       exec("/var/www/BTF/runDQM.sh $rawfile > /dev/null &"); 
       echo (" *** DQM just ran *** ");
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

if (($still_running == 0) && (!file_exists('/tmp/horse.veto'))) {
   //
   // run stopped: play sound
   //
   echo '<script type="text/javascript">play_sound();</script>';
   touch('/tmp/horse.veto');
   sleep(3);
   exec("/var/www/BTF/runDQM.sh $rawfile > /dev/null &"); 
}
?>
