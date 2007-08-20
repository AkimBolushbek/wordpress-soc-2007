<?php
/*
Plugin Name: Ishtar
Plugin URI: http://moeffju.net/p/ishtar/
Description: Translate WordPress on the fly.
Version: 0.1
Author: Matthias Bauer
Author URI: http://moeffju.net/
*/

define('ISHTAR_BASEDIR', dirname(__FILE__));
define('ISHTAR_DOMAIN', 'ishtar');

function ishtar_init() {
  add_action('admin_menu', 'ishtar_config_page');
}
add_action('init', 'ishtar_init');

function ishtar_config_page() {
  if ( function_exists('add_submenu_page') )
    add_submenu_page('plugins.php', __('Ishtar', ISHTAR_DOMAIN), __('Ishtar', ISHTAR_DOMAIN), 'manage_options', 'ishtar-main', 'ishtar_main');
}

function ishtar_is_fuzzy($data) {
  return (array_key_exists('flags', $data) && array_key_exists('fuzzy', $data['flags']) && $data['flags']['fuzzy']);
}

function ishtar_is_untranslated($data) {
  return (implode('', $data['msgstr']) === '');
}

function ishtar_html_msgctxt($data) {
  if (!array_key_exists('msgctxt', $data)) return '';
  return htmlspecialchars($data['msgctxt']);
}

function ishtar_html_msgid($data) {
  return htmlspecialchars(str_replace("\\n", "\\n\n", $data['msgid']));
}

function ishtar_html_msgstr($data) {
  return htmlspecialchars(str_replace("\\n", "\\n\n", implode("\n", $data['msgstr'])));
}

function ishtar_html_reference($data) {
  if (!array_key_exists('reference', $data)) return '';
  return htmlspecialchars(implode("<br>\n", $data['reference']));
}

function ishtar_filter($data, $filter_terms) {
  return
       (stripos($data['msgid'], $filter_terms) !== FALSE)
    || (stripos(implode('', $data['msgstr']), $filter_terms) !== FALSE);
}

function ishtar_main() {
  if ( isset($_POST['submit']) ) {
    if ( function_exists('current_user_can') && !current_user_can('manage_options') )
      die(__('Cheatin&#8217; uh?'));
  }

  include('gettext.php');

  wp_enqueue_script('prototype');
  wp_print_scripts();
  
  /* For testing */
  $hash= parse_po_file(ISHTAR_BASEDIR.'/test_fuzzy.po');
  $full_hash= $hash;
  
  if (isset($_POST['filter']) && isset($_POST['filterterms']) && !empty($_POST['filterterms'])) {
    $filter_terms= $_POST['filterterms'];
    $temp= $hash;
    $hash= array();
    foreach ($temp as $k => $data) {
      if ((stripos($data['msgid'], $filter_terms) !== FALSE) || (stripos(implode('', $data['msgstr']), $filter_terms) !== FALSE)) {
        $hash[$k] = $data;
      }
    }
    //$hash= array_filter($hash, create_function('$a', 'return ishtar_filter($a, \''.addslashes($filter_terms).'\');'));
  }
  
  if (isset($_POST['finish'])) {
    //echo "<pre>".htmlspecialchars(var_export($_POST, TRUE))."</pre>";
    foreach ($_POST['ishtar'] as $k => $data) {
      // normalize newlines
      $s= $data['msgstr'];
      $s= str_replace(array("\r\n", "\r"), array("\n", "\n"), $s);
      $s= str_replace(array("\\n\n"), array("\\n"), $s);
      $s= stripslashes($s);
      
      //echo "<pre>before = ".htmlspecialchars(var_export($full_hash[$k], TRUE))."<br>after = ".htmlspecialchars(var_export($data, TRUE))."</pre>";
      $full_hash[$k]['msgstr']= array($s);
      $hash[$k]= $full_hash[$k];
    }
    if (write_po_file($full_hash, ISHTAR_BASEDIR.'/test_fuzzy.po')) {
      $message = __('Successfully saved file.', ISHTAR_DOMAIN);
      $message_color = '#ddf';
    }
    else {
      $message = __('Error saving file.', ISHTAR_DOMAIN);
      $message_color = '#fdd';
    }
  }
  
  $paginate_num= 5;
  $paginate_count= sizeof($hash);
  $paginate_start= (isset($_POST['ishtar_ps']) ? intval($_POST['ishtar_ps']) : 0);
  if (isset($_POST['saveprev'])) $paginate_start-=$paginate_num;
  if (isset($_POST['savenext'])) $paginate_start+=$paginate_num;
  if (isset($_POST['savego'])) $paginate_start=($paginate_num * intval($_POST['savegopage']));
  $paginate_start= min(max($paginate_start, 0), $paginate_count-1);
  $paginate_end= min($paginate_start + $paginate_num, $paginate_count);

  $controls= array();
  if ($paginate_start > 0)
    $controls[]= '<input type="submit" name="saveprev" value="'.sprintf(__('Save and show previous %d', ISHTAR_DOMAIN), $paginate_num).'">';
  if ($paginate_start + $paginate_num < $paginate_count)
    $controls[]= '<input type="submit" name="savenext" value="'.sprintf(__('Save and show next %d', ISHTAR_DOMAIN), min($paginate_num, $paginate_end)).'">';
  if ($paginate_count > $paginate_num)
    $controls[]= __('Save and go to page:', ISHTAR_DOMAIN).' <input type="text" name="savegopage" size="3" value="'.floor($paginate_start / $paginate_num).'"><input type="submit" name="savego" value="'.__('Go', ISHTAR_DOMAIN).'">';
  $controls[]= __('Filter:', ISHTAR_DOMAIN).' <input type="text" name="filterterms" value="'.htmlspecialchars($filter_terms).'"><input type="submit" name="filter" value="'.__('Filter', ISHTAR_DOMAIN).'">';
  $controls[]= '<input type="submit" name="finish" value="'.__('Finish', ISHTAR_DOMAIN).'">';
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
<?php if ($message): ?>
<div id="message" class="updated fade"><p><strong><?php echo $message; ?></strong></p></div>
<?php endif; ?>
<div class="wrap ishtar">
<h2><?php _e('Ishtar', ISHTAR_DOMAIN); ?></h2>
<p><?php _e('Alt-N: Go to <u>n</u>ext untranslated or fuzzy', ISHTAR_DOMAIN); ?></p>
<p><?php _e(sprintf('Displaying entries %1$d &ndash; %2$d of %3$d.', $paginate_start, $paginate_end, $paginate_count), ISHTAR_DOMAIN); ?></p>
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
<tr><td colspan="4"> <?php echo implode(' ', $controls); ?> </td></tr>
<tr> <th><?php _e('reference', ISHTAR_DOMAIN) ?></th> <th><?php _e('msgid', ISHTAR_DOMAIN); ?></th> <th><?php _e('msgstr', ISHTAR_DOMAIN); ?></th> <th><?php _e('fuzzy', ISHTAR_DOMAIN) ?></th> </tr>
</thead>
<tbody>
<?php
foreach (array_slice($hash, $paginate_start, $paginate_num) as $data):
  $i= $data['idx'];
  $c= array();
  if (ishtar_is_fuzzy($data)) $c[]= 'ishtar-f';
  if (ishtar_is_untranslated($data)) $c[]= 'ishtar-u';
?>
<tr<?php if (sizeof($c) > 0) echo ' class="'.implode(' ', $c).'"'; ?>> <td><span class="ishtar-ctx"><?php echo ishtar_html_msgctxt($data); ?></span><br><span class="ishtar-ref"><?php echo ishtar_html_reference($data); ?></span></td> <td><?php echo ishtar_html_msgid($data); ?></td> <td><textarea name="ishtar[<?php echo $i; ?>][msgstr]"><?php echo ishtar_html_msgstr($data); ?></textarea></td> <td class="ishtar-fuz"><?php if (ishtar_is_fuzzy($data)): ?><label for="ishtar_<?php echo $i; ?>_fuzzy"><input type="checkbox" name="ishtar[<?php echo $i; ?>][flags]" id="ishtar_<?php echo $i; ?>_fuzzy" value="fuzzy" checked="checked"></label><?php else: ?>-<?php endif; ?></td> </tr>
<?php
  $i++;
endforeach;
?>
</tbody>
<?php if (0): ?>
<tfoot>
<tr><td colspan="4"> <?php echo implode(' ', $controls); ?> </td></tr>
</tfoot>
<?php endif; ?>
</table>
</form>
</div>
<?php
}

?>
