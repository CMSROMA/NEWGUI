<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html> 
  <head>
    <title>DAQ Frontend for CeFe3 Testbeam at BTF</title>
  </head>
  
  <body bgcolor="#FFFFFF" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" onLoad="self.focus();document.Qfrm.what.focus()">
    
    <table width="100%"  height="88"border="0" cellpadding="0" cellspacing="0">
      <tr><td valign="bottom">
	  
	  <STRONG><font size="10"><font color=#00000000>DAQ Frontend for CeFe3 Testbeam at BTF</font></STRONG></br>
    </table>
    
    
</td></tr>
<hr>

<?php
   unlink("/home/cmsdaq/DAQ/VMEDAQ/acq.stop");
   $con=mysqli_connect("127.0.0.1","root","?cms?daq?2014","rundb_v1");
   $risultato = mysqli_query($con, "SELECT MAX(run_number) FROM run");
   $row = mysqli_fetch_row($risultato);
   $highest_id = $row[0];
   echo "The last run was number: $highest_id <br>";

   $last_daq_id=mysqli_fetch_row(mysqli_query($con,"SELECT run_daq_id FROM run WHERE run_number=$highest_id"));

   $daq_query=mysqli_fetch_row(mysqli_query($con,"SELECT daq_type_description FROM daq_configuration WHERE daq_conf_id=$last_daq_id[0]"));

   $daq_gate1=mysqli_fetch_row(mysqli_query($con,"SELECT daq_user_gate1_ns FROM daq_configuration WHERE daq_conf_id=$last_daq_id[0]"));

   $daq_gate2=mysqli_fetch_row(mysqli_query($con,"SELECT daq_user_gate2_ns FROM daq_configuration WHERE daq_conf_id=$last_daq_id[0]"));



if(strpos($daq_query[0], 'ADC265')!== false){

$adc265b=1;

}else{
$adc265b=0;

}


if(strpos($daq_query[0],'ADC792')!== false){

$adc792b=1;

}else{
$adc792b=0;
}

if(strpos($daq_query[0],'TDC')!==false){

$tdcb=1;

}else{
$tdcb=0;
}

if(strpos($daq_query[0],'Digitizer')!==false){
$digb=1;

}else{
$digb=0;

}


   while ($highest_id>0) {
     $prova = mysqli_fetch_row(mysqli_query($con, "SELECT run_type_id FROM run WHERE run_number='$highest_id'"));
     $beam=mysqli_fetch_row(mysqli_query($con, "SELECT run_type_description FROM run_type WHERE run_type_id='$prova[0]'"));
     if($beam[0]=='beam'){
       break;
     }

     $highest_id--;
  }

  $beam_num=mysqli_fetch_row(mysqli_query($con, "SELECT run_nevents FROM run WHERE run_number='$highest_id'"));
  $beam_ht=mysqli_fetch_row(mysqli_query($con, "SELECT table_horizontal_position FROM run WHERE run_number='$highest_id'"));
  $beam_vt=mysqli_fetch_row(mysqli_query($con, "SELECT table_vertical_position FROM run WHERE run_number='$highest_id'"));
  $b_id=mysqli_fetch_row(mysqli_query($con, "SELECT run_beam_id FROM run WHERE run_number='$highest_id'"));
  $b_energy=mysqli_fetch_row(mysqli_query($con, "SELECT beam_energy FROM beam_configuration WHERE beam_conf_id='$b_id[0]'"));
  $b_intensity=mysqli_fetch_row(mysqli_query($con, "SELECT beam_intensity FROM beam_configuration WHERE beam_conf_id='$b_id[0]'"));
  $b_hw=mysqli_fetch_row(mysqli_query($con, "SELECT beam_horizontal_width FROM beam_configuration WHERE beam_conf_id='$b_id[0]'"));
  $b_vw=mysqli_fetch_row(mysqli_query($con, "SELECT beam_vertical_width FROM beam_configuration WHERE beam_conf_id='$b_id[0]'"));
  $b_htb=mysqli_fetch_row(mysqli_query($con, "SELECT beam_horizontal_tilt FROM beam_configuration WHERE beam_conf_id='$b_id[0]'"));
  $b_vtb=mysqli_fetch_row(mysqli_query($con, "SELECT beam_vertical_tilt FROM beam_configuration WHERE beam_conf_id='$b_id[0]'"));

  $b_particle=mysqli_fetch_row(mysqli_query($con, "SELECT beam_particle FROM beam_configuration WHERE beam_conf_id='$b_id[0]'"));


    
$risultato = mysqli_query($con, "SELECT MAX(run_number) FROM run");
$row = mysqli_fetch_row($risultato);
$highest_id = $row[0];

       while($highest_id>0){
$prova= mysqli_fetch_row(mysqli_query($con, "SELECT run_type_id FROM
    run WHERE run_number='$highest_id'"));

$ped=mysqli_fetch_row(mysqli_query($con, "SELECT run_type_description
    FROM run_type WHERE run_type_id='$prova[0]'"));
if(strpos($ped[0],'pedestal') !== false){
break;
}
   
    
$highest_id--;
    
    }

  $ped_num=mysqli_fetch_row(mysqli_query($con, "SELECT run_nevents FROM run WHERE run_number='$highest_id'"));
  $ped_ht=mysqli_fetch_row(mysqli_query($con, "SELECT table_horizontal_position FROM run WHERE run_number='$highest_id'"));
  $ped_vt=mysqli_fetch_row(mysqli_query($con, "SELECT table_vertical_position FROM run WHERE run_number='$highest_id'"));


$risultato = mysqli_query($con, "SELECT MAX(run_number) FROM run");
$row = mysqli_fetch_row($risultato);
$highest_id = $row[0];

       while($highest_id>0){
$prova= mysqli_fetch_row(mysqli_query($con, "SELECT run_type_id FROM
    run WHERE run_number='$highest_id'"));

$cosm=mysqli_fetch_row(mysqli_query($con, "SELECT run_type_description
    FROM run_type WHERE run_type_id='$prova[0]'"));
if($cosm[0]=='cosmic'){
break;
    }
    
    
$highest_id--;
    
    }

  $cosm_num=mysqli_fetch_row(mysqli_query($con, "SELECT run_nevents FROM run WHERE run_number='$highest_id'"));
  $cosm_ht=mysqli_fetch_row(mysqli_query($con, "SELECT table_horizontal_position FROM run WHERE run_number='$highest_id'"));
  $cosm_vt=mysqli_fetch_row(mysqli_query($con, "SELECT table_vertical_position FROM run WHERE run_number='$highest_id'"));


$risultato = mysqli_query($con, "SELECT MAX(run_number) FROM run");
$row = mysqli_fetch_row($risultato);
$highest_id = $row[0];

       while($highest_id>0){
$prova= mysqli_fetch_row(mysqli_query($con, "SELECT run_type_id FROM
    run WHERE run_number='$highest_id'"));

$na=mysqli_fetch_row(mysqli_query($con, "SELECT run_type_description
    FROM run_type WHERE run_type_id='$prova[0]'"));
if($na[0]=='Na source'){
break;
    }
    
    
$highest_id--;
    
    }

  $na_num=mysqli_fetch_row(mysqli_query($con, "SELECT run_nevents FROM run WHERE run_number='$highest_id'"));
  $na_ht=mysqli_fetch_row(mysqli_query($con, "SELECT table_horizontal_position FROM run WHERE run_number='$highest_id'"));
  $na_vt=mysqli_fetch_row(mysqli_query($con, "SELECT table_vertical_position FROM run WHERE run_number='$highest_id'"));

$risultato = mysqli_query($con, "SELECT MAX(run_number) FROM run");
$row = mysqli_fetch_row($risultato);
$highest_id = $row[0];

       while($highest_id>0){
$prova= mysqli_fetch_row(mysqli_query($con, "SELECT run_type_id FROM
    run WHERE run_number='$highest_id'"));

$sr=mysqli_fetch_row(mysqli_query($con, "SELECT run_type_description
    FROM run_type WHERE run_type_id='$prova[0]'"));
if($sr[0]=='Sr source'){
break;
    }
    
    
$highest_id--;
    
    }

  $sr_num=mysqli_fetch_row(mysqli_query($con, "SELECT run_nevents FROM run WHERE run_number='$highest_id'"));
  $sr_ht=mysqli_fetch_row(mysqli_query($con, "SELECT table_horizontal_position FROM run WHERE run_number='$highest_id'"));
  $sr_vt=mysqli_fetch_row(mysqli_query($con, "SELECT table_vertical_position FROM run WHERE run_number='$highest_id'"));


?>

<!-- buffer for data from DB -->
<input type="hidden" id="ped_num" VALUE="<?php echo "$ped_num[0]";?>">
<input type="hidden" id="ped_ht" VALUE="<?php echo "$ped_ht[0]";?>">
<input type="hidden" id="ped_vt" VALUE="<?php echo "$ped_vt[0]";?>">
<input type="hidden" id="cosm_num" VALUE="<?php echo "$cosm_num[0]";?>">
<input type="hidden" id="cosm_ht" VALUE="<?php echo "$cosm_ht[0]";?>">
<input type="hidden" id="cosm_vt" VALUE="<?php echo "$cosm_vt[0]";?>">
<input type="hidden" id="na_num" VALUE="<?php echo "$na_num[0]";?>">
<input type="hidden" id="na_ht" VALUE="<?php echo "$na_ht[0]";?>">
<input type="hidden" id="na_vt" VALUE="<?php echo "$na_vt[0]";?>">
<input type="hidden" id="sr_num" VALUE="<?php echo "$sr_num[0]";?>">
<input type="hidden" id="sr_ht" VALUE="<?php echo "$sr_ht[0]";?>">
<input type="hidden" id="sr_vt" VALUE="<?php echo "$sr_vt[0]";?>">
<input type="hidden" id="beam_num" VALUE="<?php echo "$beam_num[0]";?>">
<input type="hidden" id="beam_ht" VALUE="<?php echo "$beam_ht[0]";?>">
<input type="hidden" id="beam_vt" VALUE="<?php echo "$beam_vt[0]";?>">
<input type="hidden" id="beam_en" VALUE="<?php echo "$b_energy[0]";?>">
<input type="hidden" id="beam_intensity" VALUE="<?php echo "$b_intensity[0]";?>">
<input type="hidden" id="beam_hw" VALUE="<?php echo "$b_hw[0]";?>">
<input type="hidden" id="beam_vw" VALUE="<?php echo "$b_vw[0]";?>">
<input type="hidden" id="beam_htb" VALUE="<?php echo "$b_htb[0]";?>">
<input type="hidden" id="beam_vtb" VALUE="<?php echo "$b_vtb[0]";?>">
    


<input type="hidden" id="265hid" value="<?php echo "$adc265b"?>">
<input type="hidden" id="792hid" value="<?php echo "$adc792b"?>">
<input type="hidden" id="tdchid" value="<?php echo "$tdcb"?>">
<input type="hidden" id="dighid" value="<?php echo "$digb"?>">
<input type="hidden" id="beam_p" value="<?php echo "$b_particle[0]"?>">
<input type="hidden" id="gate1" value="<?php echo "$daq_gate1[0]"?>">
<input type="hidden" id="gate2" value="<?php echo "$daq_gate2[0]"?>">


<SCRIPT LANGUAGE="JavaScript">

function sel(){
   
document.getElementById("daq1").value=document.getElementById("gate1").value;
document.getElementById("daq2").value=document.getElementById("gate2").value;

if(document.getElementById("265hid").value==1){
document.Qfrm.adc265.checked=true;
}

if(document.getElementById("792hid").value==1){
document.Qfrm.adc792.checked=true;
}

if(document.getElementById("tdchid").value==1){
document.Qfrm.tdc.checked=true;
}

if(document.getElementById("dighid").value==1){
document.Qfrm.digitizer.checked=true;
}
var tend= document.getElementById("tend").value;

var n=tend.indexOf("pedestal");

if(n!=-1){
document.getElementById("namebox").value=document.getElementById("ped_num").value;
document.getElementById("namebox1").value=document.getElementById("ped_ht").value;
document.getElementById("namebox2").value=document.getElementById("ped_vt").value;

}
if(tend=='cosmics'){
document.getElementById("namebox").value=document.getElementById("cosm_num").value;
document.getElementById("namebox1").value=document.getElementById("cosm_ht").value;
document.getElementById("namebox2").value=document.getElementById("cosm_vt").value;


}
if(tend=='Na source'){
document.getElementById("namebox").value=document.getElementById("na_num").value;
document.getElementById("namebox1").value=document.getElementById("na_ht").value;
document.getElementById("namebox2").value=document.getElementById("na_vt").value;


}
if(tend=='Sr source'){
document.getElementById("namebox").value=document.getElementById("sr_num").value;
document.getElementById("namebox1").value=document.getElementById("sr_ht").value;
document.getElementById("namebox2").value=document.getElementById("sr_vt").value;


}

if(tend=='beam'){

if(document.getElementById("beam_p").value=='electron'){
document.Qfrm.electr.checked=true;
}else{
document.Qfrm.electr.checked=false;
}
if(document.getElementById("beam_p").value=='positron'){
document.Qfrm.positr.checked=true;
}else{
document.Qfrm.positr.checked=false;
}
if(document.getElementById("beam_p").value=='photon'){
document.Qfrm.photon.checked=true;
}else{
document.Qfrm.photon.checked=false;
}

document.getElementById("namebox").value=document.getElementById("beam_num").value;
document.getElementById("namebox1").value=document.getElementById("beam_ht").value;
document.getElementById("namebox2").value=document.getElementById("beam_vt").value;
document.getElementById("energy").value=document.getElementById("beam_en").value;
document.getElementById("intensity").value=document.getElementById("beam_intensity").value;
document.getElementById("bhw").value=document.getElementById("beam_hw").value;
document.getElementById("bvw").value=document.getElementById("beam_vw").value;
document.getElementById("bht").value=document.getElementById("beam_htb").value;
document.getElementById("bvt").value=document.getElementById("beam_vtb").value;

document.Qfrm.nome.disabled=false;
document.Qfrm.nome1.disabled=false;
document.Qfrm.nome2.disabled=false;
document.Qfrm.nome3.disabled=false;
document.Qfrm.nome4.disabled=false;
document.Qfrm.nome5.disabled=false;
document.Qfrm.electr.disabled=false;
document.Qfrm.positr.disabled=false;
document.Qfrm.photon.disabled=false;

    }else{
    document.Qfrm.nome.disabled=true;
    document.Qfrm.nome1.disabled=true;
    document.Qfrm.nome2.disabled=true;
    document.Qfrm.nome3.disabled=true;
    document.Qfrm.nome4.disabled=true;
    document.Qfrm.nome5.disabled=true;
    document.Qfrm.electr.disabled=true;
    document.Qfrm.positr.disabled=true;
    document.Qfrm.photon.disabled=true;

    }

}


    function confirmFunc() {
    //First characteristics of the run
    var num = document.getElementById("namebox").value;
    var hpos = document.getElementById("namebox1").value;
    var vpos = document.getElementById("namebox2").value;

    //Decision for the run type
    var tend =document.getElementById("tend").value;
    

   //Beam characteristics
   var en = document.getElementById("energy").value;
   var hwidth = document.getElementById("bhw").value;
   var vwidth = document.getElementById("bvw").value;
   var htilt = document.getElementById("bht").value;
   var vtilt = document.getElementById("bvt").value;
   var intens = document.getElementById("intensity").value;
   var el_button=document.getElementById("el_box").checked;
   var pos_button=document.getElementById("pos_box").checked;
   var phot_button=document.getElementById("ph_box").checked;
   var adc1_button=document.getElementById("265box").checked;
   var adc2_button=document.getElementById("792box").checked;
   var tdc_button=document.getElementById("tdcbox").checked;
   var dig_button=document.getElementById("dig_box").checked;

//Controlling fields

if(adc1_button==false && adc2_button==false && tdc_button==false && dig_button==false){
var daq_con_contr='problem';

}else{

var daq_con_contr='';

}

if(tend=='beam'){
if(el_button==false && pos_button==false && phot_button==false){

var b_conf= 'b_problem';
}else{

var b_conf='';
}
}

if(num=='' || hpos=='' || vpos=='' || en=='' || hwidth=='' || vwidth=='' || htilt=='' || vtilt=='' || intens=='' || daq_con_contr=='problem' || b_conf=='b_problem'){
alert("Attention!! At least one field is empty!");
return false;
}

if(adc1_button==true){
adc1_button='ON';}else adc1_button='OFF';
if(adc2_button==true){
adc2_button='ON';}else adc2_button='OFF';
if(tdc_button==true){
tdc_button='ON';}else tdc_button='OFF';
if(dig_button==true){
dig_button='ON';}else dig_button='OFF';


    if(tend=='beam'){
if(el_button==true){
var part= 'electron';

}

if(pos_button==true){
var part='positron';

}

if(phot_button==true){
    var part='photon';
 }

var sic = confirm("Do you really want to launch an acquisition?\nRUN RECAP:\nNumber of events: "+ num + ";\nTable horizontal position: " + hpos + " mm;\nTable vertical position: " + vpos + " mm;\nDAQ config:\nADC265: " + adc1_button +"; ADC792: "+ adc2_button +"\nTDC: "+ tdc_button +"; Digitizer: "+ dig_button  +".\nRun type: Beam.\nBEAM RECAP:\nParticle: "+part+";\nEnergy: " + en +" MeV;\nIntensity: " + intens +";\nH width: " + hwidth + " mm;\nV width: " + vwidth + " mm;\nH tilt: " + htilt +"\xB0;\nV tilt: " + vtilt + "\xB0.");

}
else{ 
        var sic = confirm("Do you really want to launch an acquisition?\nRUN RECAP:\nNumber of events: "+ num + ";\nTable horizontal position: " + hpos + " mm;\nTable vertical position: " + vpos + " mm;\nDAQ config:\nADC265: " + adc1_button +"; ADC792: "+ adc2_button +"\nTDC: "+ tdc_button +"; Digitizer: "+ dig_button  +".\nRun type: " + tend + ".");

   }
     

      
    if (sic == true){
    
    return true;
    }else{
    return false; 
    }
    
}
    
</SCRIPT>

<?php

$emulator = 0;

if( isset($_GET[emulator]) ) {
   if( $_GET[emulator] == "yes" || $_GET[emulator] == 1 ) {
    echo "<font color=red size=huge>ATTENTION: this is an emulator session! No real run will be recorded</font><br>";

    $emulator = 1;
   }
}

?>


<FORM METHOD="POST" ACTION="start.php?emulator=<?php echo ${emulator};?>" NAME="Qfrm">
<input type="hidden" name="fragmented" value="true">

<h1>Fill each field: </h1>

<table cellpadding="pixels" cellspacing="10">
<tr><td><p>Choose the run type:<p>
<td><select name="tendina" id="tend" onclick="sel()" >
  <option>Choose an option </option>
<?php 
$query = mysqli_query($con,"SELECT run_type_description FROM run_type ORDER BY run_type_id"); 
while ($riga=mysqli_fetch_array($query)){ 
    $type=$riga['run_type_description']; 
    echo "<option value=\"$type\">$type</option>"; 
} 
?> 
</select>
<!-- &nbsp;&nbsp;&nbsp;&nbsp;Choose the pedestal frequency <INPUT
name="ped_freq"size="20"> Hz</td></tr>
 <br>
-->

<tr><td>
  Number of events: </td><td><INPUT NAME="run_events" id="namebox"
    size="30" VALUE="0"><br>    
</td></tr>
<tr>
<td>Horizontal position of the table: </td><td><INPUT
  NAME="table_oriz" size="30" id="namebox1" VALUE="0">mm
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Vertical position of the
  table:<INPUT NAME="table_vert" size="30" id="namebox2" VALUE="0">mm<BR>
</td></tr>
<tr><td>
DAQ Configuration: </td><td><input type="checkbox" 
  name="adc265" value="ON" id="265box"> ADC 265<input type="checkbox" 
  name="adc792" value="ON" id="792box"> ADC 792<input type="checkbox" 
  name="tdc" value="ON" id="tdcbox"> TDC<input type="checkbox" 
  name="digitizer" value="ON" id="dig_box"> DIGITIZER</td><br>
</td></tr>
<td>DAQ1 Gate: <input name="daq1" id="daq1" size="10"></td><br><td>DAQ2 Gate: <input name="daq2" id="daq2" size="10"> ns</td>
<tr><td>Beam Particle:</td><td><input type="checkbox" 
  name="electr" value="ON" id="el_box">electron<input type="checkbox" 
  name="positr" value="ON" id="pos_box">positron<input type="checkbox" 
  name="photon" value="ON" id="ph_box">photon</td></tr><br>
<tr><td>Beam Energy:</td><td><input type="text" size="10"
  name="nome" id="energy" VALUE="0">MeV; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Beam
  Intensity:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" size="10" name="nome1" id="intensity" VALUE="0"></td><br>
<tr><td>Beam Horizontal Width:</td><td><input type="text" 
  size="10" name="nome2" id="bhw" VALUE="0">mm; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Beam Vertical Width:&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" size="10" name="nome3" id="bvw"VALUE="0">mm;</td><br>
<tr><td>Beam Horizontal Tilt:</td><td><input type="text" size="10" name="nome4" id="bht" VALUE="0">&deg;; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Beam
  Vertical Tilt:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" size="10" name="nome5" id="bvt" VALUE="0">&deg;.</td><br>
  
<tr><td>Comments:</td><td><INPUT NAME="user_comments" size="140"><BR>
</td></tr>
</table>
<center>
<INPUT TYPE="SUBMIT" onclick=" return confirmFunc()" VALUE='  Submit  '>
</center>

</FORM>

<p> <p>

<hr>
<address></address>
<!-- hhmts start -->Last modified: Fri Apr 18 09:51:33 CEST 2014 <!-- hhmts end -->

</body>
</html>
