#!/bin/bash
FILES=/home/kdresdell/Desktop/*.CSV
for f in $FILES
do
  echo "Processing $f file..."
  # take action on each file. $f store current file name
  
  /home/kdresdell/Documents/jmdstore/bin/CONV_CSV.py  $f
  mv $f /home/kdresdell/Documents/
done
