MARKDOWN ?= Markdown.pl

PLUGIN_NAME = eztags
PLUGIN_VERSION = carver
PLUGIN_SPACER = -

GZIP = gzip
GZIPFLAGS =

all:
	cd eztags && $(MAKE)
	echo '<meta http-equiv="refresh" content="0; eztags/docs/readme.html">' > readme.html
	sed -r \
		-e 's/(Donate\s+link:\s*)(.*)/\1[Donate](\2)/g' \
		-e 's/(\w+:.*)/\1<br>/g' \
		-e 's/===\s+(.*)\s+===/<title>\1<\/title>\n# \1 #/g' \
		-e 's/==\s+(.*)\s+==/## \1 ##/g' \
		-e 's/=\s+(.*)\s+=/### \1 ###/g' \
		readme.txt | \
		$(MARKDOWN) --html4tags > eztags/docs/readme.html

clean:
	cd eztags && $(MAKE) clean
	rm -f readme.html
	rm -f *.zip *.tar.gz *.tar.bz2

zip: clean all
	zip -r $(PLUGIN_NAME)$(PLUGIN_SPACER)$(PLUGIN_VERSION).zip *

tarball: clean all
	tar cf $(PLUGIN_NAME)$(PLUGIN_SPACER)$(PLUGIN_VERSION).tar *
	$(GZIP) $(GZIPFLAGS) $(PLUGIN_NAME)$(PLUGIN_SPACER)$(PLUGIN_VERSION).tar

gz:
	$(MAKE) tarball

bz2:
	$(MAKE) tarball GZIP=bz2

soc:
	$(MAKE) gz PLUGIN_NAME=2007_wordpress PLUGIN_VERSION=bowes_keith PLUGIN_SPACER=_
