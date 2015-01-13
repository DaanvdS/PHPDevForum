#!/bin/bash          
date=$(date +"%d %b %Y %X")
user=%1
git add .
git commit -m "Push from $user ($date)"
git push origin master
