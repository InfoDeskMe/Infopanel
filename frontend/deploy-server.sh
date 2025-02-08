#!/bin/bash -e

cd `dirname $`

export PORT=5173
export BROWSER=none

yarn && yarn build
#tail -f /dev/null