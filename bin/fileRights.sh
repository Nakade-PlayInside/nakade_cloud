#!/usr/bin/env bash
#Params are root dir
PATH=$1

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
    fi
}

function makeRights()
{
    RIGHTS=$1
    PATH=$2
    changeOwner $2
    echo "Make subdir rights writable: $RIGHTS"
    /bin/chmod "$RIGHTS" -R "$PATH"
}

function changeOwner()
{
    PATH=$1
    echo "Change owner: $1"
    sudo /bin/chown "jenkins:www-data" -R "$PATH"
}


#iterate thru all params
while [ $# -gt 0 ]
do
  findPath "$1"/var
  findPath "$1"/vendor
  makeRights 775 "$1"/var
  makeRights 775 "$1"/vendor
  makeRights 777 "$1"/var/log
  shift
done

exit 0
