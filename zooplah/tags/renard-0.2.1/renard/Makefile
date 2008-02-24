include ../functions.mk

all: renard.php
	cd docs && $(MAKE)
	cd languages && $(MAKE)

renard.php: renard.php.in
	$(call IN_TO_PHP)

clean:
	cd docs && $(MAKE) clean
	cd languages && $(MAKE) clean
	rm -f renard.php
