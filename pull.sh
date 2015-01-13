#!/bin/bash    
echo "spek12" | sudo -s <<EOF
cd /home/daan/public_html/forum/PHPDevForum
runuser -l forum -c 'git pull -q'
echo 'Done!'
