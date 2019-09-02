#!/usr/bin/env bash
#Params are root dir
PATH=$1

#proof if path exists
if [ ! -d "$PATH" ]
  then
     echo Path \"$PATH\" not found.
     echo Exit.
     exit 1
  else
     echo found \"$PATH\" ...
fi

#file rights
echo "make recursive rights to 775 in  \"$PATH\" ..."
/usr/bin/sudo /bin/chmod 775 -R "$PATH"

#log files
echo "make log files accessible (777): \"$PATH/var/log\""
/usr/bin/sudo /bin/chmod 777 -R "$PATH/var/log/"

exit 0
