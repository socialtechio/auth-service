#!/usr/bin/env bash
TIMEOUT_SEC=8
REQUEST_CONCURRENCY=5
REQUEST_COUNT=25

docker run --rm --net=host --entrypoint=/bin/sh jordi/ab \
"-c" \
"if timeout -t ${TIMEOUT_SEC} ab -n ${REQUEST_COUNT} -c ${REQUEST_CONCURRENCY} http://localhost:80/uploadDataURL?someVar=someValue; then echo \"Test successful, your code now as 🚀🚀🚀\"; else echo \"Too slow 🐌🐌🐌. Test Failed\"; fi"
