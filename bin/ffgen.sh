#!/bin/bash
 
# Written by Alexis Bezverkhyy <alexis@grapsus.net> in 2011
# This is free and unencumbered software released into the public domain.
# For more information, please refer to <http://unlicense.org/>
 
function usage {
        echo "Usage : ffsplit.sh input.file chunk-duration [output-filename-format]"
        echo -e "\t - input file may be any kind of file reconginzed by ffmpeg"
        echo -e "\t - chunk duration must be in seconds"
        echo -e "\t - output filename format must be printf-like, for example myvideo-part-%04d.avi"
        echo -e "\t - if no output filename format is given, it will be computed\
 automatically from input filename"
}
 
IN_FILE="$1"
OUT_FILE="$2"
 
DURATION_HMS=$(ffmpeg -i "$IN_FILE" 2>&1 | grep Duration | cut -f 4 -d ' ')
DURATION_H=$(echo "$DURATION_HMS" | cut -d ':' -f 1)
DURATION_M=$(echo "$DURATION_HMS" | cut -d ':' -f 2 | sed 's/^0\+\([0-9]\)/\1/')
DURATION_S=$(echo "$DURATION_HMS" | cut -d ':' -f 3 | cut -d '.' -f 1 | sed 's/^0\+\([0-9]\)/\1/')
let "DURATION = ( DURATION_H * 60 + DURATION_M ) * 60 + DURATION_S"
 
if [ "$DURATION" = '0' ] ; then
        echo "Invalid input video"
        usage
        exit 1
fi
 
ffmpeg -y -loop 1 -f image2 -i ./bin/empty.png -r 30 -t "$DURATION" "./tmp/$OUT_FILE"
