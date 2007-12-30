MARKDOWN ?= Markdown.pl

PLUGIN_NAME = renard
PLUGIN_VERSION = 0.2.1
PLUGIN_SPACER = -

GZIP = gzip
GZIPFLAGS =

include functions.mk

all: readme.html $(PLUGIN_NAME)/docs/readme.html renard.php
	cd $(PLUGIN_NAME) && $(MAKE)

readme.html:
	echo '<meta http-equiv="refresh" content="0; $(PLUGIN_NAME)/docs/readme.html">' > $@

$(PLUGIN_NAME)/docs/readme.html: readme.txt
	sed -r \
		-e 's/(Donate\s+link:\s*)(.*)/\1[Donate](\2)/g' \
		-e 's/(\w+:.*)/\1<br>/g' \
		-e 's/===\s+(.*)\s+===/<title>\1<\/title>\n# \1 #/g' \
		-e 's/==\s+(.*)\s+==/## \1 ##/g' \
		-e 's/=\s+(.*)\s+=/### \1 ###/g' \
		$< | $(MARKDOWN) --html4tags > $@

renard.php: renard.php.in
	$(call IN_TO_PHP)

clean:
	cd $(PLUGIN_NAME) && $(MAKE) clean
	rm -f readme.html
	rm -f renard.php
	rm -f *.zip *.tar.gz *.tar.bz2

zip: clean all
	zip -r $(PLUGIN_NAME)$(PLUGIN_SPACER)$(PLUGIN_VERSION).zip *

tarball: clean all
	tar cf $(PLUGIN_NAME)$(PLUGIN_SPACER)$(PLUGIN_VERSION).tar ../$(PLUGIN_NAME)
	$(GZIP) $(GZIPFLAGS) $(PLUGIN_NAME)$(PLUGIN_SPACER)$(PLUGIN_VERSION).tar

gz: tarball

bz2:
	$(MAKE) tarball GZIP=bzip2

soc:
	$(MAKE) PLUGIN_NAME=2007_wordpress PLUGIN_VERSION=bowes_keith PLUGIN_SPACER=_ gz
