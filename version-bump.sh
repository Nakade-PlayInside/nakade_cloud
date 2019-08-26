#!/bin/bash
# bash script for automatic version setting in build.xml
# EXPECTING TWO PARAMS: FIRST ROOT DIR, 2nd VERSION
BASEDIR=$1
VERSION=$2

TEMPLATE=base.html.twig
PATH="$BASEDIR"/templates/"$TEMPLATE"

echo Base directory is "$BASEDIR"

#invalid version
if [ "$#" -eq 0 ]; then
    echo "No valid version supplied. Exit."
    exit 1
fi

echo New version is $1

#creating file version.properties
sed -i 's/project.version = .*$/project.version = '$VERSION'/' $BASEDIR/version.properties 2>/dev/null
if [ $? != 0 ]; then
    echo project.version = $VERSION >> $BASEDIR/project.properties
fi


#template file existing
if [ ! -f "$PATH" ]
then
        echo File "$PATH" not found.
        echo Exit.
        exit 1
else    echo File "$PATH" found.
fi

#replacing version in file
sed -i 's/<li class="version">.*</<li class="version">'$VERSION'</' "$PATH" 2>/dev/null
if [ $? -ne 0 ]
then
    echo Could not edit "$PATH"
    exit 1
else echo Set new version in template.
fi
