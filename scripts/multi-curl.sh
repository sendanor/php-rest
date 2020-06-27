#!/bin/bash

(
  for i in {1..1000}; do
    curl -s -X 'PUT' -d '"'"$RANDOM"'"' http://localhost:8000/hello& < /dev/null > /dev/null
  done
) | cat

curl http://localhost:8000/hello
