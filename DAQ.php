<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML//EN">
<html> <head>
<title>B.T.F. GUI Resource</title>
</head>

<body bgcolor="#FFFFFF" marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" onLoad="self.focus();document.Qfrm.what.focus()">

<table width="100%"  height="88"border="0" cellpadding="0" cellspacing="0">
<tr><td valign="bottom">
 
<STRONG><font size="10"><font color=#00000000>DAQ Frontend for CeFe3 Testbeam at BTF</font></STRONG></br>
 </table>

 
    </td></tr>
<hr>

<?php
$con=mysqli_connect("127.0.0.1","root","?cms?daq?2014","rundb_test_v1");
$risultato = mysqli_query($con, "SELECT MAX(run_number) FROM run");
$row = mysqli_fetch_row($risultato);
$highest_id = $row[0];
echo "<blink><font color=red>Last acquired run: $highest_id </font></blink><br>";
?>

<SCRIPT LANGUAGE="JavaScript">

function sel(){
    
var tend= document.getElementById("tend").value;
if(tend=='beam'){

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
    var daq = document.getElementById("namebox3").value;

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

var sic = confirm("Do you really want to launch an acquisition?\nRUN RECAP:\nNumber of events: "+ num + ";\nTable horizontal position: " + hpos + " mm;\nTable vertical position: " + vpos + " mm;\nDAQ config: " + daq +"\nRun type: Beam.\nBEAM RECAP:\nParticle: "+part+";\nEnergy: " + en +" MeV;\nIntensity: " + intens +";\nH width: " + hwidth + " mm;\nV width: " + vwidth + " mm;\nH tilt: " + htilt +"\xB0;\nV tilt: " + vtilt + "\xB0.");

}
else{ 
        var sic = confirm("Do you really want to launch an acquisition?\nRUN RECAP:\nNumber of events: "+ num + ";\nTable horizontal position: " + hpos + " mm;\nTable vertical position: " + vpos + " mm;\nDAQ config: " + daq +"\nRun type: " + tend + ".");

   
     }

      
    if (sic == true){
    
    return true;
    }else{
    return false; 
    }
    }

    
//-->
    </SCRIPT>

<FORM METHOD="POST" ACTION="start.php" NAME="Qfrm">
<input type="hidden" name="fragmented" value="true">
<h1>Fill each field: </h1>
<table><tr><td>
  Number of events: </td><td><INPUT NAME="run_events" id="namebox" size="30"><br>
</td></tr>
<tr>
<td>Horizontal position of the table: </td><td><INPUT
  NAME="table_oriz" size="30" id="namebox1">mm
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Vertical position of the
  table:<INPUT NAME="table_vert" size="30" id="namebox2">mm<BR>
</td></tr>
<tr><td>
DAQ Configuration: </td><td><INPUT NAME="daq_conf" size="100" id="namebox3"><br>
</td></tr>
<tr><td><p>Choose the run type:<p></tr></td><br>
<td><select name="tendina" id="tend" onclick="sel()" >
  <option>Choose an option </option>
<?php 
$query = mysqli_query($con,"SELECT run_type_description FROM run_type ORDER BY run_type_id"); 
while ($riga=mysqli_fetch_array($query)){ 
    $type=$riga['run_type_description']; 
    echo "<option value=\"$type\">$type</option>"; 
} 
?> 
</select></td></tr>
<tr><td>Beam Particle:</td><td><input type="checkbox" 
  name="electr" value="ON" id="el_box">electron<input type="checkbox" 
  name="positr" value="ON" id="pos_box">positron<input type="checkbox" 
  name="photon" value="ON" id="ph_box">photon</td></tr><br>
<tr><td>Beam Energy:</td><td><input type="text" size="10"
  name="nome" id="energy" >MeV; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Beam
  Intensity:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" size="10" name="nome1" id="intensity" ></td><br>
<tr><td>Beam Horizontal Width:</td><td><input type="text" 
  size="10" name="nome2" id="bhw" >mm; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Beam Vertical Width:&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" size="10" name="nome3" id="bvw">mm;</td><br>
<tr><td>Beam Horizontal Tilt:</td><td><input type="text" size="10" name="nome4" id="bht" >&deg;; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Beam
  Vertical Tilt:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" size="10" name="nome5" id="bvt" >&deg;.</td><br>
  
<tr><td>Comments:</td><td><INPUT NAME="user_comments" size="140"><BR>
</td></tr>
</table>
<center>
<INPUT TYPE="SUBMIT" onclick=" return confirmFunc()" VALUE='Start Run'>
</center>

</FORM>



<p> <p>

<hr>
<address></address>
<!-- hhmts start -->Last modified: Fri Apr 18 09:51:33 CEST 2014 <!-- hhmts end -->

</body>
</html>
