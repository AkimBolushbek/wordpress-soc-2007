=== Easier Template Tags ===
Contributors: 
Donate link: 
Tags: theming
Requires at least: 2.1
Tested up to: 2.2.2
Stable tag: trunk

Converts between PHP template tags and HTML-like easier template
tags for easier theme editing.

== Description ==

When the plugin is activated, it creates an "Easier Theme Editor"
subpanel in the Presentation panel of the Administration area
(from following the "Site Admin" link in most themes).  If the
files are writable (which is very easy to do in most FTP clients),
you can edit the files with easier tags.  The plugin will try to
automatically convert as many PHP tags to easier tags as possible,
but may not be completely accurate (though it's getting better all
the time).  When you save the files, the easier tags are converted
back into PHP tags, and these tags will be converted back to
easier tags the next time you view the file through the easier
theme editor.

== Installation ==

1. Upload the eztags directory to the wp-content/plugins directory
   on your server.  You can also upload the contents of the eztags
   directory to wp-contents/plugins, but that's far more likely to
   create a clutter.
1. Activate the plugin ("Easier Template Tags").
1. Go to Presentation in the Site Admin, then into Easier Theme
   Editor, select the file you want to edit, and edit it.
1. Push Save to update your template file.

GETTING A NEWER VERSION
You can get the absolute latest code from
http://wordpress-soc-2007.googlecode.com/svn/trunk/zooplah/.
Snapshots are available from
http://code.google.com/p/wordpress-soc-2007/downloads/list.
Keep in mind that it's in testing right now.

== Frequently Asked Questions ==

= Question 1 =

Be the first to ask a question and it might get put here.

== Screenshots ==

1. TODO:  Create a screenshot and describe it here.

== Use ==

See docs/tags.txt.

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
That's a very real possibility.  Two courses of action are
possible.  One is to submit a bug.  However, if this thing only
affects one theme, it probably will be easier to do the second:
change the theme to a more coherent format.  That will help on
both our sides.

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
