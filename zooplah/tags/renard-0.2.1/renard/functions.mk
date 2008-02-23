IN_TO_PHP = $(shell sed -e 's/@header-start@/\/\*/' -e 's/@header-end@/\*\//' -e 's/@VERSION@/$(PLUGIN_VERSION)/' renard.php.in > renard.php)
