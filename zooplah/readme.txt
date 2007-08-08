=== Easier Template Tags ===
Requires at least: 2.1
Tested up to: 2.2.2

Converts between PHP template tags and HTML-like easier template
tags for easier theme editing.

== Installation ==
Drop the contents of the eztags directory into your
wp-content/plugins directory or a subdirectory thereof.

GETTING A NEWER VERSION
You can get the absolute latest code from
http://wordpress-soc-2007.googlecode.com/svn/trunk/zooplah/.
Snapshots are available from
http://code.google.com/p/wordpress-soc-2007/downloads/list.
Keep in mind that it's in testing right now.

== Use ==
See docs/tags.txt.  It's actually quite easy.

== Development ==
IMPROVING THE PLUGIN
See docs/specs.txt for how the plugin works.  If you want to make it
better, then by all means*...  Note:  For everyone's sake, please
wait until after Summer of Code 2007 (let's say, until September).
The plugin won't be done before then and I'm sure my mentor won't
like it not being completely my work.  But after that, I'll be
glad if someone likes my plugin, finds it useful and wants to
improve it.

IF A THEME DOESN'T WORK
That's a very real possibility.  Please don't report bugs or do
anything to your theme until after Summer of Code 2007.  Before
then, the plugin will be considered unfinished and subject to
major modifications, improvements, etc.  However, after that
time, two courses of action are possible.

One is to submit a bug.  However, if this thing only affects one
theme, it probably will be easier to change the theme to a more
coherent format.  That will help on both our sides.

* LICENSING
This plugin is licensed under the GPL.  It had to be.  I
personally like the BSD-style licenses better.  They give you
more freedom, I think, and require only that you indicate the
origin of your code (that I wrote the original code).  But I
wasn't given a choice in the matter.

The full text is available in your WordPress install, on gnu.org,
and if you use third-party free software, probably in hundreds of
places on your computer.

== Known problems and bugs ==
* The easier tags are case sensitive (title, TITLE, and Title are
  different entities).
* Not all PHP will be converted to an easier format.  Just ignore
  the parts that you don't understand.

See eztags/eztags-caveats.html for more issues.
