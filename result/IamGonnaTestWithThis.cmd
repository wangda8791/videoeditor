./bin/ffmpeg -y -i "./result/IamGonnaTestWithThis/video/70min-finger-00038.ts" -i "./result/IamGonnaTestWithThis/video/70min-finger-00036.ts" -i "./result/IamGonnaTestWithThis/video/70min-finger-00024.ts" -i "./result/IamGonnaTestWithThis/video/70min-finger-00007.ts" -i "./result/IamGonnaTestWithThis/video/70min-finger-00019.ts" -i "./result/IamGonnaTestWithThis/video/70min-finger-00023.ts" -i "./result/IamGonnaTestWithThis/audio/Death Grips - Get Got.mp3" -i "./result/IamGonnaTestWithThis/image/chart3.png" -i "./result/IamGonnaTestWithThis/image/chart1.png" -i "./result/IamGonnaTestWithThis/image/chart4.png" -i "./result/IamGonnaTestWithThis/image/chart2.png" -vcodec h264 -pix_fmt yuv420p -s 640*480 -acodec aac -strict experimental "./result/IamGonnaTestWithThis.mp4"