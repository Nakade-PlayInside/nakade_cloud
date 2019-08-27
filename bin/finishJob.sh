#!/usr/bin/env bash

#jenkins user visudo for sudo of user cmd
echo "Enable Nakade Site"
sudo a2dissite work.conf
sudo a2ensite nakade.conf

echo "Reload Apache2"
sudo service apache2 reload

exit 0