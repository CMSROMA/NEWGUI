GUI
===

code for the DAQ GUI used at the CeF3 testbeam at BTF

the main portal is DAQ.php

The state machine goes through the following cycle:

DAQ.php -> start.php -> stop.php -> end.php -> DAQ.php

the other php scrpts and pages are accessory to pull content from log files.
