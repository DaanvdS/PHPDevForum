#!/bin/bash          
date=$(date +"%d %b %Y %X")
user=$1
echo 'Pulling'
git pull -q  
echo 'Adding all changes'
git add -A
echo 'Commiting the changes'
git commit -q -m "Push from $user ($date)"
echo 'Pushing to GitHub.com'
git push -q origin master
echo 'Done!'
