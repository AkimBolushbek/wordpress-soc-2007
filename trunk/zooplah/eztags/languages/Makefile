%.po: eztags.pot
	msgmerge -Uq $@ $<

%.mo: %.po
	msgfmt -o $@ $<

all:
	$(foreach pos, $(wildcard *.po), $(MAKE) $(pos))
	$(foreach pos, $(wildcard *.po), $(MAKE) $(subst .po,.mo, $(pos)))

clean:
	rm -f *.mo
