#!/bin/bash

if [ ! -f ./etc/config ]; then
	echo
	echo $0 is not configured
	echo please refer to README
	echo
	false
	exit
fi

source ./etc/config

datadir="$webdir/data"
videofile="$datadir/videos.txt"
datefile="$datadir/dates.txt"
typefile="$datadir/types.txt"
types=( mkv m4v mp4 )
typere=${types[*]}
typere=${typere// /\\|}

find "$contentdir/" -type f \
	| sed "s=^$contentdir/\(.*\)=\1=" \
	| grep "\($typere\)$" \
	| grep -v '\bsample\.[^.]*$' \
	| grep -v '/sample-' \
	| while read filename; do
		d=`stat -c "%y" "$contentdir/$filename" | cut -f1 -d' '`
		echo "$d,$filename"
done > "$videofile"

cat "$videofile" | cut -f1 -d',' | sort | uniq > "$datefile"
echo ${types[*]} | tr " " "\n" > "$typefile"
true
