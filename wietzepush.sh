#!/bin/bash          
date=$(date +"%d %b %Y %X")
user='Wietze'
git add .
git commit -m "Push from $user ($date)"
git push origin master
