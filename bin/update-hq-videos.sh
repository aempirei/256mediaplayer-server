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
charsfile="$datadir/chars.txt"
types=( mkv m4v mp4 )
typere=${types[*]}
typere=${typere// /\\|}

if [ "$1" == "-t" ]; then
find "$contentdir/" -type f \
	| sed "s=^$contentdir/\(.*\)=\1=" \
	| grep "\($typere\)$" \
	| grep -v '\bsample\.[^.]*$' \
	| grep -v '/sample-'

exit
fi

find "$contentdir/" -type f \
	| sed "s=^$contentdir/\(.*\)=\1=" \
	| grep "\($typere\)$" \
	| grep -v '\bsample\.[^.]*$' \
	| grep -v '/sample-' \
	| while read filename; do
		# try to get an accurate download date
		format="%y"
		filedir=`dirname "$filename"`
		if false && [ "$filedir" != "." ] && [ "$filedir" != ".." ] && [ -d "$contentdir/$filedir" ]; then
			filepath="$contentdir/$filedir"
		else
			filepath="$contentdir/$filename"
		fi
		d=`stat -c "$format" "$filepath" | cut -f1 -d' '`
		echo "$d,$filename"
done > "$videofile"

cat "$videofile" | cut -f1 -d',' | sort | uniq > "$datefile"
cat "$videofile" | cut -f2 -d',' | sed 's/\s//g' | sed -r 's/^the\b\W+//ig' | tr 'a-z' 'A-Z' | cut -c1 | sort | uniq > "$charsfile"
echo ${types[*]} | tr " " "\n" > "$typefile"
true
