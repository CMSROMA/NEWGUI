<?php

   include "./configuration.php";
   $config = getconf();

   function getconf() {
     include "./configuration.php";
     $config = array();
     $config[emulator] = 0;
     if ((isset($_POST["emulator"])) || (isset($_GET["emulator"]))) {
        $config[emulator] = 1;
        $daqhome = "/tmp";
        shell_exec("/bin/mkdir -p $daqhome/DAQ/VMEDAQ");
        shell_exec("/bin/mkdir -p $daqhome/IMCP/log");
        shell_exec("/bin/mkdir -p $daqhome/IMCP/runs");
     }
     $config[daqhome] = $daqhome;
     $config[run_dir] = "$daqhome/IMCP/runs/";
     $config[log_dir] = "$daqhome/IMCP/log/";

     $config[daqapp]   = $daqhome . "/DAQ/VMEDAQ/acquire";
     $config[acqstop]  = $daqhome . "/DAQ/VMEDAQ/acq.stop";
     $config[acqstart] = $daqhome . "/DAQ/VMEDAQ/acq.start";
     $config[acqpause] = $daqhome . "/DAQ/VMEDAQ/acq.pause";
     return $config;
  }

  function dumpConfiguration() {
     global $config;
     echo "<table><tr><th colspan='2'>Current software configuration";
     echo "<tr><td>run_dir<td>$config[run_dir]";
     echo "<tr><td>log_dir<td>$config[log_dir]";
     echo "<tr><td>daqapp<td>$config[daqapp]";
     echo "<tr><td>acqstop<td>$config[acqstop]";
     echo "<tr><td>acqstart<td>$config[acqstart]";
     echo "<tr><td>acqpause<td>$config[acqpause]";
     echo "<tr><td>daqhome<td>$config[daqhome]";
     echo "<tr><td>emulator<td>$config[emulator]";
     echo "</table>";
  }

   function startbutton($t) {
     //
     // this function draws a button
     //
     if (strpos((string)$t, 'fake')!==false) {
       fakestartbutton();
     } else {
       echo "<INPUT TYPE='submit' STYLE='background-color: green; color: white;' " .
            "ID='startbutton' NAME='button' VALUE='START' onclick='return areyousurestart()'>";
     }
   }

   function fakestartbutton() {
     //
     // this function draws a button
     //
     echo "<INPUT TYPE='submit' STYLE='background-color: palegreen; color: blue;' " .
          "ID='fakestartbutton' NAME='button' VALUE='START FAKE RUN'>";
   }

   function stopbutton($start_server, $server) {
     //
     // this function draws a button
     //
     global $start_server, $server;
     if ($start_server == $server) {
       echo "<INPUT TYPE='submit' STYLE='background-color: red; color: white;' " .
       "ID='stopbutton' NAME='button' VALUE='STOP RUN' onclick='return areyousurestop()'>";
     }
   }

   function pausebutton($start_server, $server) {
     //
     // this function draws a button
     //
     global $start_server, $server;
     echo "<div id='results'></div><br>";
     if ($start_server == $server) {
       echo "<INPUT TYPE='submit' STYLE='background-color: orange; color: black;' " .
            "ID='pausebutton' NAME='button' VALUE='PAUSE RUN'>";
     }
   }

   function restartbutton($start_server, $server) {
     //
     // this function draws a button
     //
     echo "<div id='results'></div>";
     echo "<INPUT TYPE='submit' STYLE='background-color: green; color: white;' " .
        "ID='restartbutton' NAME='button' VALUE='RESTART RUN'>";
   }

   function giveup($start_server, $server) {
     global $start_server, $server;
     if ($start_server == $server) {
       echo "<INPUT TYPE='submit' STYLE='background-color: blue; color: white;' " .
         "ID='giveupbutton' NAME='button' VALUE='GIVE UP'>";
     } else if ($start_server == "NULL") {
       $start_server = $server;
       echo "<INPUT TYPE='submit' STYLE='background-color: blue; color: white;' " .
         "ID='takeoverbutton' NAME='button' VALUE='TAKE OVER'>";
     }
   }

   function writeConfigurationToDB() {
     //
     // write the configuration chosen by the user to the DB
     //
     global $start_server, $server, $config, $con;
     $daq_conf = "";
     if ($_POST[adc265] == "on") {
       $daq_conf .= "ADC265 ";
     }
     if ($_POST[adc792] == "on") {
       $daq_conf .= "ADC792 ";
     }
     if ($_POST[tdc] == "on") {
       $daq_conf .= "TDC ";
     }
     if ($_POST[digitizer] == "on") {
       $daq_conf .= "Digitizer";
     }
     $daq_gate1 = $_POST[daq_gate1];
     $daq_gate2 = $_POST[daq_gate2];
     $query = "SELECT daq_conf_id FROM daq_configuration WHERE " .
              "daq_type_description = '$daq_conf' AND daq_user_gate1_ns = " . 
	      "$daq_gate1 AND daq_user_gate2_ns = $daq_gate2";
     $daq_conf_id = mysqli_fetch_row(mysqli_query($con, $query))[0];
     if ($daq_conf_id == '') {
        mysqli_query($con, "INSERT INTO daq_configuration VALUES (NULL, '$daq_conf', $daq_gate1, $daq_gate2)");
        $daq_conf_id = mysqli_fetch_row(mysqli_query($con, "SELECT LAST_INSERT_ID()"))[0];
     }
     $run_type = $_POST[tend];
     $particle = $_POST[particle];
     $beam_conf_id = "NULL";
     if (strpos($run_type, 'beam') !== false) {
        $beam_energy = $_POST[beam_energy];
        $beam_intensity = $_POST[beam_intensity];
        $bhw = $_POST[beam_hw];
        $bvw = $_POST[beam_vw];
        $bht = $_POST[beam_ht];
        $bvt = $_POST[beam_vt];
        $query = "SELECT beam_conf_id FROM beam_configuration WHERE beam_particle = " .
  	  "'$particle' AND beam_energy = $beam_energy AND beam_intensity = " .
	  "$beam_intensity AND beam_horizontal_width = $bhw AND beam_vertical_width = $bvw " .
	  "AND beam_horizontal_tilt = $bht AND beam_vertical_tilt = $bvt";
        $beam_conf_id = mysqli_fetch_row(mysqli_query($con, $query))[0];
        if ($beam_conf_id == '') {
           mysqli_query($con, "INSERT INTO beam_configuration VALUES " .
  	     "(NULL, '$particle', $beam_energy, $beam_intensity, $bhw, $bvw, $bht, $bvt)");
           $beam_conf_id = mysqli_fetch_row(mysqli_query($con, "SELECT LAST_INSERT_ID()"))[0];
        }
     }
     $ucomment = $_POST[user_comments];
     if ($config[emulator]) {
        if (preg_match("/^EMULATOR: .*/", $ucomment) <= 0) {
           $ucomment = "EMULATOR: " . $ucomment;
        }
     }
     $nevents = $_POST[n_of_events];
     $thp = $_POST[thp];
     $tvp = $_POST[tvp];
     $query = "INSERT INTO run VALUES (NULL, $nevents, NULL, (SELECT run_type_id " .
       "FROM run_type WHERE run_type_description = '$run_type'), $beam_conf_id, " .
       "$thp, $tvp, '$ucomment', NULL, NOW(), NULL, NULL, $daq_conf_id)";
     mysqli_query($con, $query);
     $run_number = mysqli_fetch_row(mysqli_query($con, "SELECT LAST_INSERT_ID()"))[0];
     return $run_number;
   }

   function writeDetectorConfigurationToDB($run_number) {
     global $start_server, $server, $config, $con;
     $n = $_POST[detector_elements];
     for ($i = 0; $i < $n; $i++) {
        $f = $i + 1;
        $idpos = 'pos_' . $f;
        $position = $_POST[$idpos];
        $idhv = 'hv_' . $f;
        $hv = $_POST[$idhv];
        $idcath = 'cath_' . $f;
        $cath = $_POST[$idcath];
        if ($cath != '') {
           $cath = 1;
        } else {
           $cath = 0;
        }
        $query = "INSERT INTO element_configuration VALUES (NULL, $run_number, $f, $position, $hv, $cath)";
        $idcheck = 'checkbox' . $f;
        $ischecked = $_POST[$idcheck];
        if ($ischecked != '') {
           mysqli_query($con, $query);
        }
     }
   }

   function start() {
     //
     // called at start of a run. really start if not yet started
     //
     global $start_server, $server, $config, $con;
     $pidfile = "$config[daqapp].pid";
     if (!file_exists($pidfile)) {
       realstart();
     }
   }

   function realstart() {
     //
     // called at the start of a run
     //
     global $start_server, $server, $config, $con;
     $run_number = writeConfigurationToDB();
     writeDetectorConfigurationToDB($run_number);
     date_default_timezone_set('Europe/Rome');
     $run_date = date('Ymd-His');
     $runname = $_POST[tend];
     $runnname = preg_replace("/ .*/", "", $runname);
     //
     // build the command
     // 
     $logfile =  $config[log_dir] . "run_IMCP_${run_number}_${run_date}_${runname}.log";
     echo "<INPUT TYPE='hidden' NAME='logfile' VALUE='$logfile' />";
     $rawfile =  $config[run_dir] . "run_IMCP_${run_number}_${run_date}_${runname}";
     $run_opts = '-b';
     if (strpos($runname, 'pedestal') !== false) {
       list($dummy, $ped_freq, $unit) = split(' ', $runname, 3);
       $run_opts = '-r $ped_freq';
     } else if (strpos($runname, 'LED') !== false) {
       $run_opts = '-l';
     }
     $nevents = $_POST[n_of_events];
     $command = $config[daqapp] . " -p 10 -f ${rawfile} -n ${nevents} " .
        "${run_opts} 1>${logfile} 2>&1 & echo $!";
     echo "Executing <font color='orange'>${command}</font><BR>";
     echo "Data are in <font color='blue'>$rawfile</font><br>";
     echo "Logfile is <font color='blue'>$logfile</font> linked as " .
        "<font color='blue'>$config[daqhome]/IMCP/log/current_logfile</font><br>";
     if (file_exists($config[acqstop])) {
       unlink($config[acqstop]);
     }
     if (file_exists("$config[daqhome]/IMCP/log/current_logfile")) {
       unlink("$config[daqhome]/IMCP/log/current_logfile");
     }
     $mklink = "/bin/ln -s ${logfile} $config[daqhome]/IMCP/log/current_logfile";
     shell_exec($mklink);
     $pid = shell_exec($command);
     $pidfile = "$config[daqapp].pid";
     file_put_contents($pidfile, $pid);
     echo "DAQ Process id: <font color='blue'>$pid</font><br>";

     echo "<p>make DQM plots with the following command: <br>";
     echo "<span class='plain'>";
     echo "cd $config[daqhome]/DQM ; source $config[daqhome]/root/bin/thisroot.sh; ./makeDqmPlots.sh $rawfile";
     echo "</span><p>";
     $pccmsdaq01 = shell_exec("/sbin/ifconfig | grep 'inet ' | grep -v 127.0.0.1");
     $pccmsdaq01 = preg_replace("/ *inet [^0-9]*/", "", $pccmsdaq01);
     $pccmsdaq01 = preg_replace("/ .*/", "", $pccmsdaq01);
     $dqm_dir =  "run_IMCP_${run_number}_${run_date}_${runname}";
     $dqm_url = "http://$pccmsdaq01/NEWGUI/$dqm_dir";
     echo "plots available at <a target='new' href='$dqm_url'>$dqm_url</a><br><br>";
   }

  function stop() {
     //
     // called when a run is stopped
     //
     global $start_server, $server, $config, $con;
     $euser_comment = $_POST[end_user_comment];
     $query = "SELECT MAX(run_number) FROM run";
     $result = mysqli_query($con, $query);
     $new_run_id = mysqli_fetch_row($result)[0];
     $query = "UPDATE run SET run_endtime = NOW(), run_end_user_comment = " .
        "'$euser_comment' WHERE run_number = $new_run_id";
     mysqli_query($con, $query);
     $pidfile = "$config[daqapp].pid";
     if (file_exists($pidfile)) {
       unlink($pidfile);
     }
   }
 
   function isRunning($program) {
     $result = shell_exec("/bin/ps auxc | grep $program");
     $tokens = split(' ', $result);
     return $tokens[sizeof($tokens) - 1];
   }
?>