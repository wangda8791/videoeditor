cmd="( "

outfile="result.mp4"

for i; do
    cmd="${cmd}ffmpeg -i $i -ab 256000 -vb 10000000 -mbd rd -trellis 2 -cmp 2 -subcmp 2 -g 100 -f mpeg -; "
done
cmd="${cmd} ) | ffmpeg -y -i - -threads 8 ${h264options} -vb 10000000 -acodec libfaac -ar 44100 -ab 128k -s 640*480 ${outfile}"
echo "${cmd}"
eval ${cmd}
