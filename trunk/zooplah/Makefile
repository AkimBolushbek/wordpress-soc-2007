MARKDOWN ?= Markdown.pl

PLUGIN_NAME = eztags
PLUGIN_VERSION = renard
PLUGIN_SPACER = -

GZIP = gzip
GZIPFLAGS =

all: readme.html eztags/docs/readme.html
	cd eztags && $(MAKE)

readme.html:
	echo '<meta http-equiv="refresh" content="0; eztags/docs/readme.html">' > $@

eztags/docs/readme.html: readme.txt
	sed -r \
		-e 's/(Donate\s+link:\s*)(.*)/\1[Donate](\2)/g' \
		-e 's/(\w+:.*)/\1<br>/g' \
		-e 's/===\s+(.*)\s+===/<title>\1<\/title>\n# \1 #/g' \
		-e 's/==\s+(.*)\s+==/## \1 ##/g' \
		-e 's/=\s+(.*)\s+=/### \1 ###/g' \
		$< | $(MARKDOWN) --html4tags > $@

clean:
	cd eztags && $(MAKE) clean
	rm -f readme.html
	rm -f *.zip *.tar.gz *.tar.bz2

zip: clean all
	zip -r $(PLUGIN_NAME)$(PLUGIN_SPACER)$(PLUGIN_VERSION).zip *

tarball: clean all
	tar cf $(PLUGIN_NAME)$(PLUGIN_SPACER)$(PLUGIN_VERSION).tar *
	$(GZIP) $(GZIPFLAGS) $(PLUGIN_NAME)$(PLUGIN_SPACER)$(PLUGIN_VERSION).tar

gz: tarball

bz2:
	$(MAKE) tarball GZIP=bz2

soc:
	$(MAKE) PLUGIN_NAME=2007_wordpress PLUGIN_VERSION=bowes_keith PLUGIN_SPACER=_ gz
