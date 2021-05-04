#!/bin/bash
u=$$
mode=$1
res=$2
  scanimage --mode "${mode}" --resolution "${res}" -x 215 -y 296 --source ADF --batch=/tmp/out-$u-%d.pnm 2>&1 | while read scan page num rem  ; do
        if [ "$scan"x == "Batch"x ]
        then    # num is scanned # of pages...
                total=$num
                files=""
                for i in $( seq 1 $total ) 
                do
                        files="$files /tmp/out-$u-${i}.pnm"
                done
                convert $files /tmp/img-$u.pdf
                rm $files
                echo /tmp/img-$u.pdf
        fi
  done
exit 0
