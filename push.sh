#!/bin/bash          
date=$(date +"%d %b %Y %X")
user=$1
echo 'git pull'
git pull -q  
echo 'git add'
git add .
echo 'git commit'
git commit -q -m "Push from $user ($date)"
echo 'git push'
git push -q origin master
echo 'git done'
