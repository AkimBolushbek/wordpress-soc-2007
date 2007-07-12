#!/bin/sh

MSGFMT="$(which msgfmt)"
PHPMSGFMT="../msgfmt.php"
FAILED=

if ls -1 test_*.mo >/dev/null 2>/dev/null
then
  echo "There are existing test_*.mo files in this directory."
  echo "These must be deleted to make sure they don't interfere with the tests."
  echo 
  echo "Press RETURN to delete the existing files and continue, or hit Ctrl-C to cancel"
  read
  rm -f test_*.mo
fi

echo "Comparing results of gettext msgfmt and php-msgfmt"
echo ""

for i in test*.po ; do
  echo -n "$i ... "
  
  o="${i/%.po/}"
  "$MSGFMT" --no-hash "$i" -o "${o}_ref.mo" 2>/dev/null
  php "$PHPMSGFMT" "$i" -o "${o}.mo" 2>/dev/null
  if [ -r "${o}.mo" -a -r "${o}_ref.mo" ] -a ! diff -q "${o}.mo" "${o}_ref.mo" 2>/dev/null
  then
    echo "FAILED!"
    FAILED="$FAILED $o"
  else
    echo "ok"
    rm -f "${o}.mo" "${o}_ref.mo"
  fi
done

echo
if [ -z "$FAILED" ]
then
  echo "All tests succeeded."
else
  echo "Failed tests: $FAILED"
fi
