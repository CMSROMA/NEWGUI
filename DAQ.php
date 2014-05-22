<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html> 
  <head>
    <link href="style.css" rel="stylesheet" type="text/css">
    <title>DAQ Frontend for CMS Testbeams at BTF</title>
    <script src="jquery.min.js"></script>
    <script>
      $(document).ready(function() {
        $("#results").load("AjaxErrorLog.php");
        var refreshId = setInterval(function() {
          $("#results").load("AjaxErrorLog.php").fadeIn("slow");
        }, 2000); // refresh time (default = 2000 ms = 2 seconds)
      });
    </script>

    <meta http-equiv="refresh" content="300">

  </head>
  
  <body bgcolor="#FFFFFF" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" onLoad="draw();">
    
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr><td nowrap valign="bottom">
	  
	  <STRONG><font size="10"><font color=#00000000>DAQ Frontend for MCP Testbeam at BTF</font></STRONG></br>
    </table>
</td></tr>
<hr>

<script src="jquery.min.js"></script>

<script language='JavaScript'>
function play_horse() {
        var audioElement = document.createElement('audio');
        audioElement.setAttribute('src', 'horse.mp3');
        audioElement.setAttribute('autoplay', 'autoplay');
        audioElement.load();
        audioElement.play();
}

function areyousurestart() {
   var type = document.getElementById('tend').value;
   if (type.indexOf("Choose") >= 0) {
      alert("Choose a run type");
      return false;
   } 
   return true;
}

function enable() {
  //
  // enable detector elements
  //
  var f = 1;

  var cathode_title="cath";
  var hv_title= "hv";

  var numero = document.getElementById("detector_elements").value;
  while (f <= numero) {
       var idcheck = 'checkbox' + f;
       var idpos = 'pos_' + f;
       var idname = 'name' + f;
       var idhv = 'hv_' + f;
       var idcath = 'cath_' + f;
       var ischecked = document.getElementById(idcheck).checked;
       if (ischecked) {
	  document.getElementById(idpos).disabled = false;
	  document.getElementById(idhv).disabled = false;
	  document.getElementById(idcath).disabled = false;
       } else {
	  document.getElementById(idpos).disabled = true;
	  document.getElementById(idhv).disabled = true;
	  document.getElementById(idcath).disabled = true;
       }
       f++;
  }
}

function sel() {
  //
  // remove useless fields
  //
  var option = document.getElementById('tend').value;
  var i = 1;
  var j = 0;
  while (i < document.getElementById('run_types').value + 1) {
    var hidden_name = 'run_description_' + i;
    var hidden_value = document.getElementById(hidden_name).value;
    if (hidden_value == option) {
      j = i;
      i = document.getElementById('run_types').value + 1;
    }
    i++;
  }
  var elements = ["n_of_events", "thp", "tvp", "beam_energy", "beam_intensity",
	     "beam_hw", "beam_vw", "beam_ht", "beam_vt"];
  for (var k = 0; k < elements.length; k++) {
    var id = elements[k] + "_" + j;
    var v = document.getElementById(id);
    if (v != null) {
      document.getElementById(elements[k]).value = document.getElementById(id).value;
    }
  }
  document.getElementById("daq_gate1").value = document.getElementById("daq_gate1_").value;
  document.getElementById("daq_gate2").value = document.getElementById("daq_gate2_").value;
  var particles = ["electron", "positron", "photon"];
  var particle = document.getElementById("beam_particle").value;
  for (var k = 0; k < particles.length; k++) {
    if (particle == particles[k]) {
      document.getElementById(particle).checked = true;
    }
  }

  for (var i = 0; i < 4; i++) {
     var rowindex = i + 1;
     var row = 'beam_info_' + rowindex;
     if (option.indexOf("beam") >= 0) {
        document.getElementById(row).style.display = '';
     } else {
        document.getElementById(row).style.display = 'none';
     }
  }
}

function draw() {
  //
  // draw detector configuration
  //
  enable();
  var obj = document.getElementById("detector_elements");
  if (obj != null) {
    var numero = obj.value;
    var positions = [];
    for (var i = 0; i < numero; i++) {
      var f = i + 1;
      var idcheck = 'checkbox' + f;
      var ischecked = document.getElementById(idcheck).checked;
      if (ischecked) {
         var idpos = 'pos_' + f;
         var posvalue = document.getElementById(idpos).value;
         positions.push(posvalue);
      }
    }
    positions.sort(function(a,b){return a-b});
    var str= "<table class='setup'><tr>";
    for (var i = 0; i < positions.length; i++) {
       var j = 0;
       while (j < numero) {
         var k = j + 1;
         var idpos = 'pos_' + k;
         var posvalue = document.getElementById(idpos).value;
         var idname = 'name' + k;
         var namevalue = document.getElementById(idname).innerHTML;
         var idhv = 'hv_' + k;
         if (posvalue == positions[i]) {
            if (namevalue.indexOf("Absorber") >= 0) {
               str += "<td style='background-color: #C96333;'>";
            } else if (namevalue.indexOf("Plexiglass") >= 0) {
               str += "<td style='background-color: #3399CC;'>";
            } else {
               str += "<td>";
            }
            str += namevalue + "<br>" + posvalue + "</td>";
         }
         j++;
       }
    }
    str += "</tr></table>";
    document.getElementById("setup").innerHTML = str;
  }
}
</script>

<FORM METHOD="POST" ACTION="DAQ.php" NAME="Qfrm">

<?php

   include './global_variables.php';
   $con = mysqli_connect("127.0.0.1",$dbuser,$dbpass,$dbname);
   $comment = "";
   if ($config[emulator] == 1) {
     $comment = "EMULATOR: ";
     echo "<INPUT TYPE='hidden' NAME='emulator' VALUE='1'/>";
   }

   $status = $_POST["button"];
   $server = $_SERVER["REMOTE_ADDR"];

   if (file_exists($config[acqstart])) {
     $start_server = file_get_contents($config[acqstart]);
   } else if (file_exists($config[acqpause])) {
     $start_server = file_get_contents($config[acqpause]);
   } else {
     $start_server = $server;
   }
   
   if ($status == "START") {
     // the run can start
     file_put_contents($config[acqstart], $server);
     echo "<audio controls autoplay='autoplay' style='display: none'>";
     echo "<source src='tada.mp3' type='audio/mpeg'>";
     echo "</audio>";
     start();
   } else if ($status == "STOP RUN") {
     if ($server == $start_server) {
       if (file_exists($config[acqstart])) {
         unlink($config[acqstart]);
       }
       if (file_exists($config[acqpause])) {
         unlink($config[acqpause]);
       }
       file_put_contents($config[acqstop], $server);
       stop();
     }
   } else if ($status == "PAUSE RUN") {
     if ($server == $start_server) {
       if (file_exists($config[acqstart])) {
         unlink($config[acqstart]);
       }
       file_put_contents($config[acqpause], $server);
     }
   } else if ($status == "RESTART RUN") {
     if ($server == $start_server) {
       if (file_exists($config[acqstart])) {
         unlink($config[acqpause]);
       }
       file_put_contents($config[acqstart], $server);
     }
   } else if ($status == "GIVE UP") {
     if (file_exists($config[acqstart])) {       
       file_put_contents($config[acqstart], "NULL");
     }
     if (file_exists($config[acqpause])) {       
       file_put_contents($config[acqpause], "NULL");
     }
     $start_server = "NULL";
   } else if ($status == "TAKE OVER") {
     if ($start_server == "NULL") {
       if (file_exists($config[acqstart])) {
         file_put_contents($config[acqstart], $server);
         $start_server = $server;
       }
       if (file_exists($config[acqpause])) {
         file_put_contents($config[acqpause], $server);
         $start_server = $server;
       }
     }
   }

   echo "You are connected from $server: the control is taken by $start_server ";
   //
   // get the current status of the run
   //
   $more = "";
   if ($config[emulator] == 1) {
     $more = " <B><font color='blue'>[EMULATION MODE]</font></B>";
   }
   if (file_exists($config[acqstart])) {
     $status = "started";
     echo "<FONT COLOR='green'>RUN STARTED</FONT>$more<BR>";
   } else if (file_exists($config[acqpause])) {
     $status = "paused";
     echo "<FONT COLOR='orange'>RUN PAUSED</FONT>$more<BR>";
   } else {
     $status = "stopped";
     echo "<FONT COLOR='red'>RUN STOPPED</FONT>$more<BR>";
   }
   
   //
   // set default values
   //
   $adc265 = "checked";
   $adc792 = "";
   $tdc    = "";
   $dig    = "";

   $num_events = 1000;
   $table_h = 500;
   $table_v = 500;
   $daq_gate1 = 250;
   $daq_gate2 = 1000;

   $electron = "checked";
   $positron = "";
   $photon = "";

   $beam_energy = 450;
   $beam_intensity = 2;
   $beam_hw = 10;
   $beam_vw = 10;
   $beam_ht = 0;
   $beam_vt = 0;

   //
   // try to connect to the DB to get the last configurations
   //
   if ($con == null) {
     echo "Cannot connect to the DB: please inform the DB coordinator.";
   } else {
     $highest_id = mysqli_fetch_row(mysqli_query($con, "SELECT MAX(run_number) FROM run"))[0];
     $run_types = mysqli_fetch_row(mysqli_query($con, "SELECT MAX(run_type_id) FROM run_type"))[0];
     $daq_configuration = mysqli_fetch_row(mysqli_query($con, "SELECT daq_type_description FROM daq_configuration WHERE daq_conf_id = (SELECT run_daq_id FROM run WHERE run_number = $highest_id)"))[0];
     $daq_gate1 = mysqli_fetch_row(mysqli_query($con, "SELECT daq_user_gate1_ns FROM daq_configuration WHERE daq_conf_id = (SELECT run_daq_id FROM run WHERE run_number = $highest_id)"))[0];
     $daq_gate2 = mysqli_fetch_row(mysqli_query($con, "SELECT daq_user_gate2_ns FROM daq_configuration WHERE daq_conf_id = (SELECT run_daq_id FROM run WHERE run_number = $highest_id)"))[0];
     
     for ($i = 0; $i < $run_types; $i++) {
       $rt = $i + 1;
       $run_description = mysqli_fetch_row(mysqli_query($con, "SELECT run_type_description FROM run_type WHERE run_type_id = $rt"))[0];
       $max_run_number = mysqli_fetch_row(mysqli_query($con, "SELECT MAX(run_number) FROM run WHERE run_type_id = $rt"))[0];
       echo "<INPUT TYPE='hidden' ID='run_description_$rt' VALUE='$run_description'/>";
       echo "<INPUT TYPE='hidden' ID='max_run_number_$rt' VALUE='$max_run_number'/>";
       if ($max_run_number != NULL) {
         $n_of_events = mysqli_fetch_row(mysqli_query($con, "SELECT run_nevents FROM run WHERE run_number = $max_run_number"))[0];
         $thp = mysqli_fetch_row(mysqli_query($con, "SELECT table_horizontal_position FROM run WHERE run_number = $max_run_number"))[0];
         $tvp = mysqli_fetch_row(mysqli_query($con, "SELECT table_vertical_position FROM run WHERE run_number = $max_run_number"))[0];
         $daq_id = mysqli_fetch_row(mysqli_query($con, "SELECT run_daq_id FROM run WHERE run_number = $max_run_number"))[0];
         echo "<INPUT TYPE='hidden' ID='n_of_events_$rt' VALUE='$n_of_events'/>";
         echo "<INPUT TYPE='hidden' ID='thp_$rt' VALUE='$thp'/>";
         echo "<INPUT TYPE='hidden' ID='tvp_$rt' VALUE='$tvp'/>";
         echo "<INPUT TYPE='hidden' ID='daq_id_$rt' VALUE='$daq_id'/>";
         if (strpos($run_description, 'beam') !== false) {
           $beam_energy = mysqli_fetch_row(mysqli_query($con, "SELECT beam_energy FROM beam_configuration WHERE beam_conf_id = (SELECT run_beam_id FROM run WHERE run_number = $max_run_number)"))[0];
           $beam_intensity = mysqli_fetch_row(mysqli_query($con, "SELECT beam_intensity FROM beam_configuration WHERE beam_conf_id = (SELECT run_beam_id FROM run WHERE run_number = $max_run_number)"))[0];
           $beam_hw = mysqli_fetch_row(mysqli_query($con, "SELECT beam_horizontal_width FROM beam_configuration WHERE beam_conf_id = (SELECT run_beam_id FROM run WHERE run_number = $max_run_number)"))[0];
           $beam_vw = mysqli_fetch_row(mysqli_query($con, "SELECT beam_vertical_width FROM beam_configuration WHERE beam_conf_id = (SELECT run_beam_id FROM run WHERE run_number = $max_run_number)"))[0];
           $beam_ht = mysqli_fetch_row(mysqli_query($con, "SELECT beam_horizontal_tilt FROM beam_configuration WHERE beam_conf_id = (SELECT run_beam_id FROM run WHERE run_number = $max_run_number)"))[0];
           $beam_vt = mysqli_fetch_row(mysqli_query($con, "SELECT beam_vertical_tilt FROM beam_configuration WHERE beam_conf_id = (SELECT run_beam_id FROM run WHERE run_number = $max_run_number)"))[0];
           $beam_particle = mysqli_fetch_row(mysqli_query($con, "SELECT beam_particle FROM beam_configuration WHERE beam_conf_id = (SELECT run_beam_id FROM run WHERE run_number = $max_run_number)"))[0];
         }
         echo "<INPUT TYPE='hidden' ID='beam_energy' VALUE='$beam_energy' />";
         echo "<INPUT TYPE='hidden' ID='beam_intensity' VALUE='$beam_intensity' />";
         echo "<INPUT TYPE='hidden' ID='beam_hw' VALUE='$beam_hw' />";
         echo "<INPUT TYPE='hidden' ID='beam_vw' VALUE='$beam_vw' />";
         echo "<INPUT TYPE='hidden' ID='beam_ht' VALUE='$beam_ht' />";
         echo "<INPUT TYPE='hidden' ID='beam_vt' VALUE='$beam_vt' />";
         echo "<INPUT TYPE='hidden' ID='beam_particle' VALUE='$beam_particle' />";
       }
     } 
     echo "<INPUT TYPE='hidden' ID='run_types' VALUE='$run_types'/>";    
     echo "<INPUT TYPE='hidden' ID='daq_gate1_' VALUE='$daq_gate1'/>";    
     echo "<INPUT TYPE='hidden' ID='daq_gate2_' VALUE='$daq_gate2'/>";    
     if (strpos($daq_configuration, 'ADC265') !== false) {
	 echo "<INPUT TYPE='hidden' ID='adc265' NAME='adc265' VALUE='checked'/>";
     } else {
	 echo "<INPUT TYPE='hidden' ID='adc265' NAME='adc265' VALUE=''/>";
     }
     if (strpos($daq_configuration, 'ADC792') !== false) {
	 echo "<INPUT TYPE='hidden' ID='adc792' NAME='adc792' VALUE='checked'/>";
     } else {
	 echo "<INPUT TYPE='hidden' ID='adc792' NAME='adc792' VALUE=''/>";
     }
     if (strpos($daq_configuration, 'TDC') !== false) {
	 echo "<INPUT TYPE='hidden' ID='tdc' NAME='tdc' VALUE='checked'/>";
     } else {
	 echo "<INPUT TYPE='hidden' ID='tdc' NAME='tdc' VALUE=''/>";
     }
     if (strpos($daq_configuration, 'Digitizer') !== false) {
	 echo "<INPUT TYPE='hidden' ID='digitizer' NAME='digitizer' VALUE='checked'/>";
     } else {
	 echo "<INPUT TYPE='hidden' ID='digitizer' NAME='digitizer' VALUE=''/>";
     }
     $current_or_last = "last run number was";
     if ($status == "started") {
       $current_or_last = "current run number is";
     }
     echo "The $current_or_last: $highest_id <br>";
   }

   if ($status == "stopped") { 
     $detector_elements = mysqli_fetch_row(mysqli_query($con,"SELECT MAX(element_id) FROM element"))[0];
     echo "<INPUT TYPE='hidden' NAME='detector_elements' ID='detector_elements' VALUE='$detector_elements' />";
     $i = 0;
     echo "<table><tr><td>";
     echo "<h1>Set detector configuration:</h1>";
     echo "<table>";
     echo "<tr><th>In use<th>Name<th>Position [mm]<th>HV [V]<th>Cathode [thick=on]";
     while ($i < $detector_elements) {
       $f = $i+1;
       $pos_value=mysqli_fetch_row(mysqli_query($con,"SELECT element_position FROM element_configuration WHERE element_id=$f AND pos_run_number='$highest_id' "))[0];
       $hv_value=mysqli_fetch_row(mysqli_query($con,"SELECT element_HV FROM element_configuration WHERE element_id=$f AND pos_run_number='$highest_id' "))[0];
       $cath_value=mysqli_fetch_row(mysqli_query($con,"SELECT element_photocathode_status FROM element_configuration WHERE element_id=$f AND pos_run_number='$highest_id' "))[0];
;
       if ($cath_value == 1) {
          $cath_value = 'checked';
       } else {
          $cath_value = '';
       }
       $name=mysqli_fetch_row(mysqli_query($con,"SELECT description FROM element WHERE element_id=$f"))[0];
       $idpos = 'pos_' . $f;
       $idcheck = 'checkbox' . $f;
       $idname = 'name' . $f;
       $idhv = 'hv_' . $f;
       $idcath = 'cath_' . $f;
       $selected = '';
       if (($hv_value != 0) || ($pos_value != 0)) {
         $selected = 'CHECKED';
       }
       echo "<tr><td><input id='$idcheck' name='$idcheck' type='checkbox' onclick='enable()' $selected /><td><div id='$idname'>$name</div><td><input id='$idpos' name='$idpos' value='$pos_value' onchange='draw()' disabled>";
       if (strpos($name, 'MCP') !== false) {
         echo "<td><input id='$idhv' name='$idhv' value='$hv_value' disabled><td><input id='$idcath' name='$idcath' type='checkbox' $cath_value disabled>";
       } else {
         echo "<td><input id='$idhv' name='$idhv' value='0' type='hidden'><td><input id='$idcath' name='$idcath' type='hidden'>";
       }
       $i++;
     }
     echo "</table>";
?>

<div id='setup'></div>
<td>
<h1>Set run configuration: </h1>
<table><tr><td nowrap style="vertical-align: top">
<table cellpadding="pixels" cellspacing="5" style="width: 100%">
<tr><td nowrap><p>Choose the run type:<p>
    <td nowrap><select name="tend" id="tend" onchange="sel()" >
    <option>Choose an option</option>
    <?php
      if ($con != null) {
        $query = mysqli_query($con,"SELECT run_type_description FROM run_type ORDER BY run_type_id"); 
        while ($riga=mysqli_fetch_array($query)){ 
          $type=$riga['run_type_description']; 
          echo "<option value=\"$type\">$type</option>"; 
        }
      } else {
        echo "<option value='fake run' selected>fake run</option>";
      } 
    ?> 
   </select>

<tr><td nowrap>Number of events: 
    <td nowrap><INPUT NAME="n_of_events" id="n_of_events"   size="30" TYPE="number" VALUE="<?php echo $num_events ?>">
<tr><td nowrap>Position of the table: 
    <td nowrap>Horizontal <INPUT NAME="thp" size="10" TYPE="number" id="thp" VALUE="<?php echo $table_h ?>"> mm
    <td nowrap>Vertical   
    <td nowrap><INPUT NAME="tvp" size="10" TYPE="number" id="tvp" VALUE="<?php echo $table_v ?>"> mm
<tr><td nowrap>DAQ Configuration: 
    <td nowrap colspan='4'>
      <input type="checkbox" name="adc265"    id="adc265" <?php echo $adc265 ?> > ADC 265
      <input type="checkbox" name="adc792"    id="adc792" <?php echo $adc792 ?> > ADC 792
      <input type="checkbox" name="tdc"       id="tdc" <?php echo $tdc ?> > TDC
      <input type="checkbox" name="digitizer" id="digitizer" <?php echo $digitizer ?>> DIGITIZER
<tr><td nowrap>Gates: 
  <td nowrap>DAQ1 <input name="daq_gate1" id="daq_gate1" TYPE="number" size="10" value="<?php echo $daq_gate1 ?>" > ns
  <td nowrap>DAQ2
  <td nowrap> <input name="daq_gate2" id="daq_gate2" TYPE="number" size="10" value="<?php echo $daq_gate2 ?>"> ns
<tr id='beam_info_1' style='display: none;'><td nowrap>Beam Particle:
  <td nowrap colspan='4'>
    <div id='particle_sel'>
    <input type="radio" name="particle" value="electron" id="electron" > electron 
    <input type="radio" name="particle" value="positron" id="positron" > positron
    <input type="radio" name="particle" value="photon"   id="photon"> photon
    </div>
<tr id='beam_info_2' style='display: none;'><td nowrap>Beam Energy:
  <td nowrap><input type="text" size="10" TYPE="number" name="beam_energy" id="beam_energy_1" VALUE="<?php echo $beam_energy ?>"> MeV
  <td nowrap>Beam Intensity:
  <td nowrap><input type="text" size="10" TYPE="number" name="beam_intensity" id="beam_intensity_1" VALUE="<?php echo $beam_intensity ?>">
<tr id='beam_info_3' style='display: none;'><td nowrap>Beam Horizontal Width:
  <td nowrap><input type="text" size="10" TYPE="number" name="beam_hw" id="beam_hw_1" VALUE="<?php echo $beam_hw ?>"> mm
  <td nowrap>Beam Vertical Width:
  <td nowrap><input type="text" size="10" TYPE="number" name="beam_vw" id="beam_vw_1" VALUE="<?php echo $beam_vw ?>"> mm</td><br>
<tr id='beam_info_4' style='display: none;'><td nowrap>Beam Horizontal Tilt:
  <td nowrap><input type="text" size="10" TYPE="number" name="beam_ht" id="beam_ht_1" VALUE="<?php echo $beam_ht ?>"> &deg;
  <td nowrap>Beam Vertical Tilt:
  <td nowrap><input type="text" size="10" TYPE="number" name="beam_vt" id="beam_vt_1" VALUE="<?php echo $beam_vt ?>"> &deg;</td><br>
<tr><td nowrap>Comments:
  <td nowrap colspan='4'><INPUT NAME="user_comments" size="81" maxlength="140" VALUE="<?php echo $comment ?>">
</table>
</td><td nowrap style="vertical-align: top;">
</td></tr></table>    

</table>
<?php } ?>

<noscript><div id="response"><h1>JavaScript is required for this demo.</h1></div></noscript>
<CENTER>
<?php
   if (file_exists($config[acqstart])) {
     if ($start_server == $server) {
       echo "End of run comment<BR>";
       echo "<input name='end_user_comment' id='end_user_comment' size='160' />";
     }
     pausebutton($start_server, $server);
     stopbutton($start_server, $server);
     giveup($start_server, $server);
   } else if (file_exists($config[acqpause])) {
     restartbutton($start_server, $server);
     stopbutton($start_server, $server);
     giveup($start_server, $server);
   } else {
     startbutton($type);
   }

   echo "</CENTER><P>";
   dumpConfiguration();
      
?>

</FORM>


<p>

<hr>
<address></address>
<!-- hhmts start -->Last modified: Fri Apr 18 09:51:33 CEST 2014 <!-- hhmts end --><br>
<span id='miniclock'></div>
<div id='dafnestatus'></div>
<iframe style="width:100%;" id='dafne' src='http://www.lnf.infn.it/acceleratori/status/get_dafne.php?get_par=0' />
</body>
</html>
