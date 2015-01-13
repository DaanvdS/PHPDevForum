#!/bin/bash          
date=$(date +"%d %b %Y %X")
user=$1
echo 'pull'
git pull -q  
echo 'add'
git add .
echo 'commit'
git commit -q -m "Push from $user ($date)"
echo 'push'
git push -q origin master
echo 'done'
