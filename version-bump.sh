#!/bin/bash
# bash script for automatic version setting in build.xml
# EXPECTING VERSION AS PARAMETER
VERSION=$1

BASEDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
TEMPLATE="footer.html.twig"
TMP_PATH="$BASEDIR"/templates/"$TEMPLATE"

echo Base directory is "$BASEDIR"

#invalid version
if [ "$#" -eq 0 ]; then
    echo "No valid version supplied. Exit."
    exit 1
fi

echo New version is $VERSION

#creating file project.properties
sed -i 's/project.version = .*$/project.version = '$VERSION'/' $BASEDIR/project.properties 2>/dev/null
if [ $? != 0 ]; then
  echo project.version = $VERSION >> $BASEDIR/project.properties
fi

#template file existing
if [ ! -f "$TMP_PATH" ]
then
        echo File "$TMP_PATH" not found.
        echo Exit.
        exit 1
else    echo File "$TMP_PATH" found.
fi

#replacing version in file
sed -i 's/ class="version">.*</ class="version">'$VERSION'</' "$TMP_PATH" 2>/dev/null
if [ $? -ne 0 ]
then
    echo Could not edit "$TMP_PATH"
    exit 1
else echo Set new version in template.
fi
