#include<stdio.h>
#include<time.h>
#include<stdlib.h>
#include<math.h>
#include<unistd.h>


int main(int argc, char** argv) {

  int run;
  char startDate[20];
  char outfile[200];
  char logname[200];
  FILE* logfile;

  if(argc<3) {

    printf("error! usage: daq <run number> <date>\n");
    exit(-1);
  }


  run = atoi( argv[1] );

  sprintf( startDate, "%s", argv[2] );



  sprintf(outfile,"run-%07d-%s", run, startDate);
  sprintf(logname, "%s%s%s", "/tmp/",outfile, ".log"); 

  logfile = fopen(logname, "w");
  if(!logfile) {
    printf("error opening logfile <%s>.... abort.\n", logname);
    exit(-1);
  }
  printf("Logfile avaiable at  <%s>\n", logname);


  fprintf(logfile, "Starting run: %07d at %s. data stored in <%s.dat>\n", run, startDate, outfile);


  // event loop
  for(int i=0; i<1000000; i++) {

    // every 10 events check if stop command issued
    if( i%10 == 0) {
       FILE* infile = fopen("/tmp/acq.stop","r");
       if( infile ) {
           fprintf(logfile, "stop command detected. ending the run after %d events.\n", i);
           fclose(infile);
           system("rm -f /tmp/acq.stop");
           break;
       } 
    }

    fprintf(logfile, "Recording event %d\n", i+1);
    printf("Recording event %d\n", i+1);
    fflush(logfile);

    usleep(2000);

  }

  fclose(logfile);

  return 0;

}

