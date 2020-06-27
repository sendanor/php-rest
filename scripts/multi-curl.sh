#!/bin/bash

(
  for i in {1..1000}; do

    #if test "$RANDOM" -lt 16383; then 50%
    if test "$RANDOM" -lt 3276; then # 10%
      ( echo PUT "$( sleep 1; curl -s -X 'PUT' -d '"'"$RANDOM"'"' http://localhost:8000/hello )" )& < /dev/null
    else
      ( echo GET "$( sleep 1; curl -s http://localhost:8000/hello )" )& < /dev/null
    fi

  done
) | cat

