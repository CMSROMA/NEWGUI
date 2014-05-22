#!/usr/bin/perl

$url = "http://www.lnf.infn.it/acceleratori/status/get_dafne.php?get_par=0";

$oldline = "";

while (1) {
  @buffer = `wget $url -O /dev/stdout 2>/dev/null`;

  foreach $line (@buffer) {
      chomp $line;
      $line =~ s/[0-9]+/#/g;
      if ($line =~ m/DAFNE: BTF .*/) {
	  if (!($line eq $oldline)) {
	      `say warning: daphne to btf changing status...`;
	      print "$line\n";
	      if ($line =~ m/DAFNE: BTF delivering & Colliding e-lifetime: # s e\+lifetime: # s <br>/) {
		  `say Hey! Electrons are coming...`;
		  for ($i = 10; $i > 0; $i--) {
		      sleep(1);
		      $cmd = "say $i";
		      `$cmd`;
		  }
		  `say ignition`;
	      }
	      if ($line =~ m/DAFNE: BTF delivering & Colliding e\+lifetime: # s <br>/) {
		  `say Electrons are going to disappear!`;
	      }
	  }
          $oldline = $line;
      }
  }

}
