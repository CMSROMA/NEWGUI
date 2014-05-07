#include<stdio.h>
#include<time.h>
#include<stdlib.h>
#include<math.h>
#include<unistd.h>


int main(int argc, char** argv) {

  int run;
  int events, i;
  char startDate[20];
  char outfile[200];
  char rawname[200];

  if(argc<4) {

    printf("error! usage: daq <run number> <number of events> <rawdatafile>\n");
    exit(-1);
  }


  run = atoi( argv[1] );
  events = atoi( argv[2] );
  sprintf( rawname, "%s", argv[3] );

  //sprintf(outfile,"run-%07d-%s", run, startDate);


  printf("Starting run: %07d with %d events. Raw data at <%s>\n", run, events, rawname);


  // event loop
  for(i=0; i<events; i++) {

    // every 10 events check if stop command issued
    if( i%30 == 0) {
      //       FILE* infile = fopen("/tmp/acq.stop","r");
      if (access("/tmp/acq.stop", F_OK)) {
	  //       if( infile ) {
           printf("stop command detected. ending the run after %d events.\n", i);
           // fclose(infile);
           system("rm -f /tmp/acq.stop");
           break;
       } 
    }

    printf("Recording event %d\n", i+1);
    fflush(stdout);

    usleep(5000);

  }
  printf("Data acquisition stopped\n");
  return 0;

}

