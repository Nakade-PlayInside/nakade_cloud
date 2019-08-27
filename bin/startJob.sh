#!/usr/bin/env bash

#jenkins user visudo for sudo of user cmd
echo "Enable Work Site"
sudo a2dissite nakade.conf
sudo a2ensite work.conf

echo "Reload Apache2"
sudo service apache2 reload

exit 0