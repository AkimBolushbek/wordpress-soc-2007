%.po: eztags.pot
	msgmerge -Uq $@ $<

%.mo: %.po
	msgfmt -o $@ $<

all: $(wildcard *.po) $(subst .po,.mo, $(wildcard *.po))

clean:
	rm -f *.mo
