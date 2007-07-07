<?php
if( !get_option('update_theme_search_enable') ){
	echo '<div class="error"><h1>Not Enabled</h1></div>';
	return;
}
	
require_once('includes/wp-update-class.php');
require_once('includes/wp-update-functions.php');

global $wpupdate;
$wpupdate = new WP_Update;

$tags = array( 	1 => '1 column',
				2 => '2 columns',
				3 => '3 columns',
				4 => '4 columns',
				6 => 'Red',
				7 => 'Green',
				8 => 'Blue',
				9 => 'Violet',
				10 => 'Orange',
				11 => 'Brown ',
				12 => 'Pink',
				13 => 'Salmon',
				14 => 'Gray',
				15 => 'Black',
				16 => 'White',
				22 => 'Fixed width',
				23 => 'Fluid width',
				24 => 'Plugins required',
				25 => 'Widget ready',
				26 => 'Options page',
				27 => 'Rounded corners',
				28 => 'Left sidebar',
				29 => 'Right sidebar',
				30 => 'No images',
				31 => 'Yellow' ); //17~21 do not exist.
?>
<div class="wrap">
<h2>Search Themes</h2>
<form id="searchoptions" name="sortoptions" method="post" action="<?php echo $pagenow . '?page=' . $_GET['page']; ?>">
<table align="center">
	<tr>
		<th>Columns</th>
		<th colspan="2">Colour</th>
		<th>Advanced</th>
	</tr>
	<tr valign="top">
		<td>
		<p>
		<input type="checkbox" name="cats[]" value="1" <?php if(is_array($_POST['cats']) && in_array(1,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('1 column') ?><br />
		<input type="checkbox" name="cats[]" value="2" <?php if(is_array($_POST['cats']) && in_array(2,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('2 columns') ?><br />
		<input type="checkbox" name="cats[]" value="3" <?php if(is_array($_POST['cats']) && in_array(3,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('3 columns') ?><br />
		<input type="checkbox" name="cats[]" value="4" <?php if(is_array($_POST['cats']) && in_array(4,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('4 columns') ?><br />
		<br />
		<input type="checkbox" name="cats[]" value="22" <?php if(is_array($_POST['cats']) && in_array(22,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Fixed width') ?><br />
		<input type="checkbox" name="cats[]" value="23" <?php if(is_array($_POST['cats']) && in_array(23,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Fluid width') ?><br />
		</p>
		</td>
		<td>
		<p>
		<input type="checkbox" name="cats[]" value="6" <?php if(is_array($_POST['cats']) && in_array(6,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Red') ?><br />
		<input type="checkbox" name="cats[]" value="7" <?php if(is_array($_POST['cats']) && in_array(7,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Green') ?><br />
		<input type="checkbox" name="cats[]" value="8" <?php if(is_array($_POST['cats']) && in_array(8,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Blue') ?><br />
		<input type="checkbox" name="cats[]" value="9" <?php if(is_array($_POST['cats']) && in_array(9,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Violet') ?><br />
		<input type="checkbox" name="cats[]" value="10" <?php if(is_array($_POST['cats']) && in_array(10,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Orange') ?><br />
		<input type="checkbox" name="cats[]" value="11" <?php if(is_array($_POST['cats']) && in_array(11,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Brown') ?>
		</p>
		</td>
		<td>
		<p>
		<input type="checkbox" name="cats[]" value="12" <?php if(is_array($_POST['cats']) && in_array(12,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Pink') ?><br />
		<input type="checkbox" name="cats[]" value="13" <?php if(is_array($_POST['cats']) && in_array(13,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Salmon') ?><br />
		<input type="checkbox" name="cats[]" value="14" <?php if(is_array($_POST['cats']) && in_array(14,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Gray') ?><br />
		<input type="checkbox" name="cats[]" value="15" <?php if(is_array($_POST['cats']) && in_array(15,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Black') ?><br />
		<input type="checkbox" name="cats[]" value="16" <?php if(is_array($_POST['cats']) && in_array(16,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('White') ?><br />
		<input type="checkbox" name="cats[]" value="31" <?php if(is_array($_POST['cats']) && in_array(31,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Yellow') ?><br />
		</p>
		</td>
		<td>
		<p>
		<input type="checkbox" name="cats[]" value="24" <?php if(is_array($_POST['cats']) && in_array(24,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Plugins required') ?><br />
		<input type="checkbox" name="cats[]" value="25" <?php if(is_array($_POST['cats']) && in_array(25,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Widget ready') ?><br />
		<input type="checkbox" name="cats[]" value="26" <?php if(is_array($_POST['cats']) && in_array(26,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Options page') ?><br />
		<input type="checkbox" name="cats[]" value="27" <?php if(is_array($_POST['cats']) && in_array(27,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Rounded corners') ?><br />
		<input type="checkbox" name="cats[]" value="28" <?php if(is_array($_POST['cats']) && in_array(28,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Left sidebar') ?><br />
		<input type="checkbox" name="cats[]" value="29" <?php if(is_array($_POST['cats']) && in_array(29,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('Right sidebar') ?><br />
		<input type="checkbox" name="cats[]" value="30" <?php if(is_array($_POST['cats']) && in_array(30,$_POST['cats'])){ echo 'checked="checked" '; } ?>/><?php _e('No images') ?><br />
		</p>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<select name='sortby' class='postform'>
				<option value='date'><?php _e('Sort by') ?></option>
				<option value='date' <?php if(isset($_POST['sortby']) && 'date' == $_POST['sortby']) { echo 'selected="selected"'; } ?>><?php _e('Date') ?></option>
				<option value='title' <?php if(isset($_POST['sortby']) && 'title' == $_POST['sortby']) { echo 'selected="selected"'; } ?>><?php _e('Title') ?></option>
				<option value='dlcount' <?php if(isset($_POST['sortby']) && 'dlcount' == $_POST['sortby']) { echo 'selected="selected"'; } ?>><?php _e('Downloads') ?></option>
				<option value='category' <?php if(isset($_POST['sortby']) && 'category' == $_POST['sortby']) { echo 'selected="selected"'; } ?>><?php _e('Tag') ?></option>
			</select>
			<select name='order' class='postform'>
				<option value='DESC'><?php _e('Order') ?></option>
				<option value='ASC' <?php if(isset($_POST['order']) && 'ASC' == $_POST['order']) { echo 'selected="selected"'; } ?>>ASC</option>
				<option value='DESC' <?php if(isset($_POST['order']) && 'DESC' == $_POST['order']) { echo 'selected="selected"'; } ?>>DESC</option>
			</select>
			<select name='andor' class='postform'>
				<option value='AND'>ANY or ALL</option>
				<option value='OR' <?php if(isset($_POST['andor']) && 'OR' == $_POST['andor']) { echo 'selected="selected"'; } ?>>ANY</option>
				<option value='AND' <?php if(isset($_POST['andor']) && 'AND' == $_POST['andor']) { echo 'selected="selected"'; } ?>>ALL</option>
			</select>
			<input type="submit" name="submit" value="<?php _e('Search') ?>"  />
		</td>
	</tr>
</table>
</form>
<script type="text/javascript">
//<!CDATA[[
	function loadMore(){
		searchOptions.paged++;
		$('#loading-image').show();
		$.post("<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-update/wp-update-ajax.php?action=themeSearch",
			  searchOptions,
			  function(data){
				$('#load-more').before( data );
				$('#loading-image').hide();
				if( searchOptions.paged == searchOptions.pages ){
					$('#load-more').hide();
				}
			  }
			);
		return false;
	}
//]]>
</script>
<?php
if( isset($_POST['submit']) && 'Search' == $_POST['submit'] ){

$taglist = array();
foreach( (array)$_POST['cats'] as $id){
	$taglist[] = __($tags[$id]);
}
$taglist = implode(', '.$_POST['andor'].' ',$taglist);
?>
<h2><?php _e('Search Results') ?></h2>
<p><?php _e('Search results for Themes tagged with ') ?><strong><span id='taglist'><?php _e($taglist) ?></span></strong></p>
<?php 
$searchResults = $wpupdate->search('themes',$_POST); 
?>
<style type="text/css">
	.themeinfo{
		display:inline;
		margin-right:20px;
		width:160px;
		text-align:center;
	}
	.themeinfo span{
		display:inline-block;
		border:thin solid #CCCCCC;
		margin-bottom:40px;
	}
	
</style>
<div id="searchresults">
<?php
if( !isset($searchResults['results']) || empty($searchResults['results']) ){
	_e('Sorry there were no search results');
} else {
	?>
	<script type="text/javascript">
		//<!CDATA[[
			var searchOptions = {"cats": "<?php echo join(',',$_POST['cats']); ?>",
								"sortby": "<?php echo $_POST['sortby']; ?>",
								"order": "<?php echo $_POST['order']; ?>",
								"andor": "<?php echo $_POST['andor']; ?>",
								"paged":<?php echo $searchResults['info']['page']; ?>.
								"pages":<?php echo $searchResults['info']['pages']; ?>};
		//]]>
	</script>
	<?php
	foreach($searchResults['results'] as $theme)
		echo wpupdate_themeSearchHTML($theme);

	if( $searchResults['info']['page'] < $searchResults['info']['pages'] )
		echo '&nbsp;<div class="themeinfo" id="load-more"><span><img style="display:none" src="'.get_option('siteurl'). '/wp-content/plugins/wp-update/images/loading.gif" id="loading-image" /><br/><a href="#load-more" onclick="loadMore()">'.__('Next Page').'</a></span></div>';
}
?>
</div>
<?php
} //end if Search
?>
</div>