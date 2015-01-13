#!/bin/bash    
echo "password" | su
cd /home/daan/public_html/forum/PHPDevForum
runuser -l forum -c 'git pull -q'
echo 'Done!'