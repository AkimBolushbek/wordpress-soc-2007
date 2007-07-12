#!/bin/sh

MSGFMT="$(which msgfmt)"
PHPMSGFMT="../msgfmt.php"

echo "Comparing results of gettext msgfmt and php-msgfmt"
echo ""

for i in test*.po ; do
  echo -n "$i ... "
  
  o="${i/%.po/}"
  "$MSGFMT" --no-hash "$i" -o "${o}_ref.mo"
  php "$PHPMSGFMT" "$i" -o "${o}.mo"
  if ! diff -q "${o}.mo" "${o}_ref.mo" ; then
    echo "FAILED!"
  else
    echo "ok"
    rm -f "${o}.mo" "${o}_ref.mo"
  fi
done

