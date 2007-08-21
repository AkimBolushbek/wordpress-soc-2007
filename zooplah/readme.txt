=== Easier Template Tags ===
Contributors: 
Donate link: #donate
Tags: theming
Requires at least: 2.1
Tested up to: 2.2.2
Stable tag: trunk

Converts between PHP template tags and HTML-like easier template tags for easier theme editing.

== Description ==

When the plugin is activated, it creates an *Easier Theme Editor* subpanel in the *Presentation* panel of the Administration area *Site Admin* link in most themes).  After you [make the files writable with your FTP client](http://codex.wordpress.org/Changing_File_Permissions#Using_an_FTP_Client), you can edit the files with easier tags.  The plugin will try to automatically convert as many PHP tags to easier tags as possible, but may not be completely accurate (though it's getting better all the time).  When you save the files, the easier tags are converted back into PHP tags, and these tags will be converted back to easier tags the next time you view the file through the easier theme editor.

== Installation ==

1. Upload the eztags directory to the wp-content/plugins directory on your server.  You can also upload the contents of the eztags directory to wp-contents/plugins, but that's far more likely to create a clutter.
1. Activate the plugin (*Easier Template Tags*).

= Getting a Newer Version =

You can get checkout the absolute latest code from the [Google Code Subversion repository](http://code.google.com/p/wordpress-soc-2007/source).  [Snapshots](http://code.google.com/p/wordpress-soc-2007/downloads/list) are also available, but pretty much guaranteed to be outdated.  I expect to have a release on [my site](http://kechjo.cogia.net) in September.

Keep in mind that it's in testing right now.

== Frequently Asked Questions ==

= What's so great about *Easier Template Tags*? =

*Easier Template Tags* is made with patent pending foksi technology that makes it 48% better than eating a Big Mac while standing on your head.

= Why are FAQs always littered with such useless questions? =

You know, I haven't figured that one out myself yet.  But as they say, if you can't beat 'em, join 'em.

== Screenshots ==

1. Editing the index.php template file (*Main Index Template*) of my theme with the *Easier Theme Editor*.

== Notes on the following sections ==

The relative links to the documentation will only work if you're viewing this page through an installation on your WordPress blog.  If you're viewing this page in a plugin repository, you'll have to install the plugin and read this page from there for the links to work.

== Use ==

1. Go to the *Site Admin* area.
1. Go to the *Presentation* panel.
1. Go to the *Easier Theme Editor* subpanel.
1. Select the template file you want to edit.
1. See [tags.txt](tags.txt) for a list of tags that you can use.
1. Edit the file using those tags.
1. Push *Save* to update your template file.

== Development ==

= Improving the plugin =

See [specs.txt](specs.txt) for how the plugin works.  If you want to make it better, then by all means*...  **Note**:  For everyone's sake, please wait until after Summer of Code 2007 (let's say, until September). The plugin won't be done before then and I'm sure my mentor won't like it not being completely my work.  But after that, I'll be glad if someone likes my plugin, finds it useful and wants to improve it.

= If it doesn't work with a theme =

That's a very real possibility.  Two courses of action are possible.  One is to submit a bug.  However, if this thing only affects one theme, it probably will be easier to do the second: change the theme to a more coherent format.  That will help on both our sides.

= Adding tags to your plugin =

eztags/includes/eztags-functions-public.php contains the `eztags_bind()` function for that.  Here's an example.

<pre><code>
function mytheme_foo(&$content, $myparam)
{
&nbsp;&nbsp;&nbsp;&nbsp;$in_str = $content;
&nbsp;&nbsp;&nbsp;&nbsp;$new_tag = eztags_add("<\$MyFoo:$myparam\$>");
&nbsp;&nbsp;&nbsp;&nbsp;$content = preg_replace('/foo\(\);?/', $new_tag, $content);
&nbsp;&nbsp;&nbsp;&nbsp;if ( $content == $in_str )
&nbsp;&nbsp;&nbsp;&nbsp;{
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$content = str_replace($new_tag, '&lt;?php foo(); ?&gt;', $content);
&nbsp;&nbsp;&nbsp;&nbsp;}
}

$myvar = 'Bar';

eztags_bind('mytheme_foo', $myvar);
</code></pre>

= * Licensing =

This plugin is licensed under the [GPL](http://www.gnu.org/copyleft/gpl.html "General Public License").  It had to be.  I personally like the [BSD](http://www.opensource.org/licenses/bsd-license.php "Berkeley Software Distribution")-style licenses better.  They give you more freedom, I think, and require only that you indicate the origin of your code (that I wrote the original code) without requiring you to release your full modified code.  However, part of [The Agreement](http://groups.google.com/group/wordpress-soc-2007/web/welcome?hl=en) was to license the code under the GPL.

== Current caveats ==

* The easier tags are case sensitive (title, TITLE, and Title are different entities).
* In many themes, not all PHP can be converted to an easier format.  Just ignore the parts that you don't understand.

See [caveats.html](caveats.html) for more.

== Donate ==

<div id="donate">
If you've found Renard helpful and want to show your appreciate and help further its development, please consider donating.

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
</form>
</div>
