<?php
/*
Plugin Name: Ishtar
Plugin URI: http://moeffju.net/p/ishtar/
Description: Translate WordPress on the fly.
Version: 0.1
Author: Matthias Bauer
Author URI: http://moeffju.net/
*/

function ishtar_init() {
	add_action('admin_menu', 'ishtar_config_page');
}
add_action('init', 'ishtar_init');

function ishtar_config_page() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __('Ishtar'), __('Ishtar'), 'manage_options', 'ishtar-main', 'ishtar_main');
}

function ishtar_is_fuzzy($data) {
  return (array_key_exists('flags', $data) && array_key_exists('fuzzy', $data['flags']) && $data['flags']['fuzzy']);
}

function ishtar_is_untranslated($data) {
  return (implode('', $data['msgstr']) === '');
}

function ishtar_html_msgctxt($data) {
  return htmlspecialchars($data['msgctxt']);
}

function ishtar_html_msgid($data) {
  return htmlspecialchars($data['msgid']);
}

function ishtar_html_msgstr($data) {
  return htmlspecialchars(implode("\n", $data['msgstr']));
}

function ishtar_main() {
	if ( isset($_POST['submit']) ) {
		if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));
	}

  wp_enqueue_script('prototype');
  wp_print_scripts();
  
  /* For testing */
  include('gettext.php');
  $hash= parse_po_file(dirname(__FILE__).'/wordpress.pot');
  $paginate_num= 100;
  $paginate_count= sizeof($hash);
  $paginate_start= (isset($_POST['ishtar_ps']) ? intval($_POST['ishtar_ps']) : 0);
  if (isset($_POST['saveprev'])) $paginate_start-=$paginate_num;
  if (isset($_POST['savenext'])) $paginate_start+=$paginate_num;
  if (isset($_POST['savego'])) $paginate_start=($paginate_num * intval($_POST['savegopage']));
  $paginate_start= min(max($paginate_start, 0), $paginate_count-1);
  $paginate_end= min($paginate_start + $paginate_num, $paginate_count-1);

?>
<script type="text/javascript" charset="utf-8">
// Guess m-width of font
var b=document.getElementsByTagName("body")[0];var d=document.createElement("div");var s=document.createElement("span");
d.appendChild(s);s.innerHTML="m";b.appendChild(d);var w=s.offsetWidth;var h=s.offsetHeight;b.removeChild(d);

var Textarea = Class.create();
Textarea.prototype = {
	initialize: function(el){
		this.el = el;
		this.rowLength = el.offsetWidth / w; //parseInt(el.getAttribute('cols'));
		this.lineHeight = h;
		this.rows = '';
		this.delta = this.lineHeight;
		this.el.setStyle({overflow: 'hidden'});
		//this.timer = setInterval(function(){this.checkValue()}.bind(this), 100);
    Event.observe(el, 'keyup', this.checkValue.bind(this));
    setTimeout(this.checkValue.bind(this), 50);
	},
	checkValue: function(){
		this.rowLength = this.el.offsetWidth / w; //parseInt(el.getAttribute('cols'));
		var value = this.el.value.split('\n');
		var rows = 0;
		for (var i=0, j=value.length; i<j; ++i) {
			var tempLength = value[i].length == 0 ? 1 : value[i].length;
			rows += Math.ceil( tempLength / this.rowLength );
		}
		if (this.rows !== rows) {
			this.rows = rows;
			this.resize();
		}
	},
	resize: function(){
		this.el.setStyle({height: this.lineHeight * this.rows + this.delta + 'px'});
	}
}

Event.observe(window, 'load', function(){
  // Init behaviors
  $$('.ishtar textarea').each(function(el){
    // init auto-resizing textarea
    new Textarea(el);
    // change listeners
    Event.observe(el, 'blur', function(){
      var c=el.up('tr');
      if (el.value == '') c.addClassName('ishtar-u'); else c.removeClassName('ishtar-u');
    });
  });
  $$('.ishtar input[type=checkbox]').each(function(el) {
    Event.observe(el, 'click', function(){
      var c=el.up('tr');
      if (el.checked) c.addClassName('ishtar-f'); else c.removeClassName('ishtar-f');
    });
  });
  $$('.ishtar textarea', '.ishtar input').each(function(el) {
    // Key listener
    Event.observe(el, 'keyup', function(e){
      if (!e) var e = window.event;
      var c = (e.keyCode ? e.keyCode : e.which);
      var ch = String.fromCharCode(c).toUpperCase();
      if (e.altKey && ch == 'N') {
        // alt-n pressed -> go to next
        var t = Event.element(e);
        var tr = t.up('tr');
        var r = $$('tr.ishtar-f', 'tr.ishtar-u');
        var ne;
        while (ne = tr.next()) { if (r.include(ne)) break; }
        if (ne) { ne.down('textarea').activate(); Event.stop(e); }
      }
    });
  });
  // Key listener
  Event.observe(document, 'keyup', function(e){
    if (!e) var e = window.event;
    var c = (e.keyCode ? e.keyCode : e.which);
    var ch = String.fromCharCode(c).toUpperCase();
    if (e.altKey && ch == 'N') {
      // alt-n pressed -> go to next
      var r = $$('tr.ishtar-f textarea', 'tr.ishtar-u textarea');
      r[0].activate();
      Event.stop(e);
    }
  });
});
</script>
<style type="text/css">
.ishtar { }
.ishtar table { width: 100%; }
.ishtar tbody textarea { width: 25em; } /* 25 "cols" */
.ishtar .ishtar-t { }
.ishtar .ishtar-f { background-color: #ffd; }
.ishtar .ishtar-u { background-color: #fee !important; }
.ishtar .ishtar-ref { font-size: 70%; }
.ishtar .ishtar-fuz { text-align: center; }
</style>
<!-- <div id="message" class="updated fade"><p><strong><?php _e('Updated.') ?></strong></p></div> -->
<div class="wrap ishtar">
<h2><?php _e('Ishtar'); ?></h2>
<p>Alt-N: Go to <u>n</u>ext untranslated or fuzzy</p>
<p>Displaying entries <?php echo $paginate_start; ?> &ndash; <?php echo $paginate_end-1; ?> of <?php echo $paginate_count-1; ?>.</p>
<form method="POST">
<input type="hidden" name="ishtar_ps" value="<?php echo $paginate_start; ?>">
<table>
<colgroup>
<col width="10%">
<col width="30%">
<col width="50%">
<col width="10%">
</colgroup>
<thead>
<!-- <tr><td colspan="4"> <input type="text" name="searchval" value=""> <input type="submit" name="search" value="Search"> </td></tr> -->
<tr><td colspan="4"> <?php if ($paginate_start > 0): ?><input type="submit" name="saveprev" value="Save and show previous <?php echo $paginate_num; ?>"> <?php endif; if ($paginate_start + $paginate_num < $paginate_count): ?><input type="submit" name="savenext" value="Save and show next <?php echo min($paginate_num, $paginate_end); ?>"><?php endif; ?> <?php if ($paginate_count > $paginate_num): ?><input type="submit" name="savego" value="Save and go to page:"><input type="text" name="savegopage" size="3" value="<?php echo floor($paginate_start / $paginate_num); ?>"><?php endif; ?><input type="submit" name="finish" value="Finish"> </td></tr>
<tr> <th>reference</th> <th>msgid</th> <th>msgstr</th> <th>fuzzy</th> </tr>
</thead>
<tbody>
<?php
$i= 0;
foreach (array_slice($hash, $paginate_start, $paginate_num) as $data):
  $c= array();
  if (ishtar_is_fuzzy($data)) $c[]= 'ishtar-f';
  if (ishtar_is_untranslated($data)) $c[]= 'ishtar-u';
?>
<tr<?php if (sizeof($c) > 0) echo ' class="'.implode(' ', $c).'"'; ?>> <td><span class="ishtar-ctx"><?php echo ishtar_html_msgctxt($data); ?></span><br><span class="ishtar-ref"><?php echo $data['reference']; ?></span></td> <td><?php echo ishtar_html_msgid($data); ?></td> <td><textarea name="ishtar[<?php echo $i; ?>][msgstr]"><?php echo ishtar_html_msgstr($data); ?></textarea></td> <td class="ishtar-fuz"><?php if (ishtar_is_fuzzy($data)): ?><label for="ishtar_<?php echo $i; ?>_fuzzy"><input type="checkbox" name="ishtar[<?php echo $i; ?>][flags]" id="ishtar_<?php echo $i; ?>_fuzzy" value="fuzzy" checked="checked"></label><?php else: ?>-<?php endif; ?></td> </tr>
<?php
  $i++;
endforeach;
?>
</tbody>
<tfoot>
<tr><td colspan="4"> <?php if ($paginate_start > 0): ?><input type="submit" name="saveprev" value="Save and show previous <?php echo $paginate_num; ?>"> <?php endif; if ($paginate_start + $paginate_num < $paginate_count): ?><input type="submit" name="savenext" value="Save and show next <?php echo min($paginate_num, $paginate_end); ?>"><?php endif; ?> <?php if ($paginate_count > $paginate_num): ?><input type="submit" name="savego" value="Save and go to page:"><input type="text" name="savegopage" size="3" value="<?php echo floor($paginate_start / $paginate_num); ?>"><?php endif; ?><input type="submit" name="finish" value="Finish"> </td></tr>
</tfoot>
</table>
</form>
</div>
<?php
}

?>
