#!/bin/bash

(
  for i in {1..1000}; do

    if test "$RANDOM" -gt 16383; then
      ( echo PUT "$( curl -s -X 'PUT' -d '"'"$RANDOM"'"' http://localhost:8000/hello )" )& < /dev/null
    else
      ( echo GET "$( curl -s http://localhost:8000/hello )" )& < /dev/null
    fi

  done
) | cat

