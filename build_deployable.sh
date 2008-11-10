#!/bin/sh
MYDIR=`pwd`
cd `dirname $0`

function safe_copy()
{
  if [ ! -d `dirname $2` ]; then
    if [ $DRYRUN -eq 1 ]; then
      echo mkdir -p `dirname $2`
    else
      mkdir -p `dirname $2`
    fi
  fi

  if [ "$QUIET" -ne 1 -a -f "$2" ]; then
    echo Warning: file $2 already exists in destination
  fi;
  if [ $DRYRUN -eq 1 ]; then
    echo cp "$1" "$2"
  else
    cp "$1" "$2"
  fi
}

COUNT=0
QUIET=0
DRYRUN=0

while getopts cd:nq flag; do
  case $flag in
    # "Directory" flag; what directory are you deploying to?
    d)
      DEPLOYDIR=$OPTARG;
      COUNT=$[$COUNT+2];;
    n)
      DRYRUN=1;
      COUNT=$[$COUNT+1];;
    q)
      QUIET=1;
      COUNT=$[$COUNT+1];;
  esac;
done

DEPLOYDIR=${DEPLOYDIR:-$MYDIR}

echo Creating deployable unit in $DEPLOYDIR from `pwd` running in $PWD

if [ -f "$DEPLOYDIR" ]; then
  echo "$DEPLOYDIR" is not a directory
  exit 1
fi

if [ $DRYRUN -ne 1 ]; then
  mkdir -p $DEPLOYDIR
  mkdir -p $DEPLOYDIR/includes
fi

shift $COUNT

for i in api social web m3 demo-apps system-apps; do
  (if [ -d "$i/public" ]; then cd "$i/public"; pwd;
   find . \( \( -name .settings -or -name .cache -or -name .svn -or -name .git \) -prune \) -or \( -name .DS_Store -or -name .gitignore \) -or -type f -print | while read file; do safe_copy "$file" "$DEPLOYDIR"/"$file"; done; fi
  )
  (if [ -d "$i/includes" ]; then cd "$i/includes"; pwd;
   find . \( \( -name .settings -or -name .cache -or -name .svn -or -name .git \) -prune \) -or \( -name .DS_Store -or -name .gitignore \) -or -type f -print | while read file; do safe_copy "$file" "$DEPLOYDIR"/includes/"$file"; done; fi
  )
  (if [ -d "$i/clients/php" ]; then cd "$i/clients/php"; pwd;
   find . \( \( -name .settings -or -name .cache -or -name .svn -or -name .git \) -prune \) -or \( -name .DS_Store -or -name .gitignore \) -or -type f -print | while read file; do safe_copy "$file" "$DEPLOYDIR"/includes/"$file"; done; fi
  )
done

# Different rules for config files
for i in api social web m3 demo-apps system-apps; do
  (if [ -d "$i/config" ]; then cd "$i/config"; pwd;
   find . \( \( -name .settings -or -name .cache -or -name .svn -or -name .git \) -prune \) -or \( -name .DS_Store -or -name .gitignore \) -or -name \*.sql -print | while read file; do safe_copy "$file" "$DEPLOYDIR"/config/"$file"; done; fi
  )
done

#mkdir -p "$DEPLOYDIR"/config
#cp api/config/ringside-schema.sql "$DEPLOYDIR"/config
if [ $DRYRUN -ne 1 ]; then
  cp LocalSettings.php.sample "$DEPLOYDIR"
fi
