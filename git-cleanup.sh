#!/bin/bash
# Written by Jack Wink https://github.com/JackWink
set -e

TMP_FILE=/tmp/merged-branches

# Delete merged branches
function cleanup_merged {
    git branch --merged | grep -v "master" | grep -v "develop" >$TMP_FILE
    if [ -s "$TMP_FILE" ]; then
        vim $TMP_FILE
        xargs git branch -d <$TMP_FILE
        rm $TMP_FILE
    fi
}

function cleanup_local {
    # Fetch latest, prunes remote
    git fetch -p
	# list branches that are no longer on remote
    git branch -vv | awk '/: gone]/{print $1}' >$TMP_FILE
    if [ -s "$TMP_FILE" ]; then
        vim $TMP_FILE
        xargs git branch -D <$TMP_FILE
        rm $TMP_FILE
    fi
}

branch=$(git rev-parse --abbrev-ref HEAD)
if [[ "$branch" != "master" && "$branch" != "develop" ]]; then
  echo 'Git cleanup should be run on master or develop';
  exit 1;
fi

cleanup_merged || true
cleanup_local
