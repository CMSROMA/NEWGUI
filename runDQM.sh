#!/bin/bash

export LC_ALL="C"
cd /home/cmsdaq/DQM/ 
. /home/cmsdaq/root/bin/thisroot.sh
./makeDqmPlots.sh $1 > /tmp/dqmlog


