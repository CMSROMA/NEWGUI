#!/usr/bin/perl

#
# This script gets the current dafne status and predicts when electron beams
# are going to disappear or to appear
#
# Works only on a MAC using the 'say' command
#
# run this script on a computer having access to the WAN with sound on
#
$url = "http://www.lnf.infn.it/acceleratori/status/get_dafne.php?get_par=0";

$oldline = "";
$os = `uname -s`;

while (1) {
  @buffer = `wget $url -O /dev/stdout 2>/dev/null`;

  foreach $line (@buffer) {
      chomp $line;
      $line =~ s/[0-9]+/#/g;
      if ($line =~ m/DAFNE: BTF .*/) {
	  if (!($line eq $oldline)) {
	      $cmd = "say warning: daphne to btf changing status...";
	      if ($os =~ m/Linux/) {
		  $cmd = "echo 'warning: daphne to btf changing status...' | espeak";
	      } 
	      `$cmd`;
	      print "$line\n";
	      if ($line =~ m/DAFNE: BTF delivering & Colliding e-lifetime: # s e\+lifetime: # s <br>/) {
		  $cmd = "say Hey! Electrons are coming...";
		  if ($os =~ m/Linux/) {
		      $cmd = "echo 'say Hey! Electrons are coming...' | espeak";
		  }
		  `$cmd`;
		  for ($i = 10; $i > 0; $i--) {
		      sleep(1);
		      $cmd = "say $i";
		      if ($os =~ m/Linux/) {
			  $cmd = "echo $i | espeak";
		      }
		      `$cmd`;
		  }
		  $cmd = "say ignition";
		  if ($os =~ m/Linux/) {
		      $cmd = "echo 'ignition' | espeak";
		  }
		  `$cmd`;
	      }
	      if ($line =~ m/DAFNE: BTF delivering & Colliding e\+lifetime: # s <br>/) {
		  $cmd = "say Electrons are going to disappear!";
		  if ($os =~ m/Linux/) {
		      $cmd = "echo 'Electrons are going to disappear!' | espeak";
		  }
		  `$cmd`;
	      }
	  }
          $oldline = $line;
      }
  }

}
