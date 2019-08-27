#!/usr/bin/env bash
#Params are root dir
PATH=$1
RIGHTS=775

#template file existing
function findPath()
{
    PATH=$1
    if [ ! -d "$PATH" ]
    then
        echo Path "$PATH" not found.
        echo Exit.
        exit 1
    else
        echo Path "$PATH" found.
        echo "Make subdir rights writable: $RIGHTS"
        /bin/chmod "$RIGHTS" -R "$PATH"
    fi
}


#iterate thru all params
while [ $# -gt 0 ]
do
  findPath "$1"/var
  findPath "$1"/vendor
  shift
done

exit 0
