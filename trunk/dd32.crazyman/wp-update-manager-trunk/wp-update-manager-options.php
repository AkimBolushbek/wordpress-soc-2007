<?php
if( isset($_POST['submit_general']) ){
	$items = array();
	foreach((array)$_POST['item'] as $key=>$val){
		if( $key === '$ID')
			continue;
		//Create a slug if non provided:
		if( empty($val['slug']) )
			$val['slug'] = $val['name'];
		$val['slug'] = sanitize_title_with_dashes($val['slug']);

		//This resets all ID's to 0...n
		$items[] = $val;
	}
	update_option('wpum_items',$items);
	//update_option('wpum_items',array()); //Clear the Items
	echo '<div class="updated"><p>'.__('Items Saved').'</p></div>';
}

$items = get_option('wpum_items');

	
?>
<style type="text/css">
	#container .tab-nav{
		display:inline;
		list-style:none;
		margin-right:0;
	}
	#container .tab-nav li{
		display:inline;
		margin-left: 5px;
		padding: 2px;
		border:1px solid #000000;
		border-bottom: none;
	}
	#container .tab-nav li:after{
		content:" ";
	}
	#container a,
	#container a:link,
	#container a:visited,
	#container a:hover{
		color:#000000;
		font-weight:bold;
		border-bottom:none;
	}
	#container li.activeTab{
		background-color:#999999;
		font-weight:bold;
		color:#FFFFFF;
	}
	#container .activeTab a{
		color: #FFFFFF;
	}
	#container .controlbox{
		float:right;
		background-color:#CBCBCB;
	}
	#container .Item{
		border: 1px solid #000000;
		background-color:#CCCCCC;
		padding: 3px;
	}
	.subsection {
		border: thun solid #AAA;
		padding: 5px;
		margin-left:10px;
	}
</style>
<script type="text/javascript">
/* <![CDATA[ */
	$.tabs = {
		tabContainer: [],
		init: function(HTMLcontainer,container){
			this.tabContainer[container] = HTMLcontainer;
			this.addTabs(container);
			this.hideTabs(container);
			this.openTab(null,container);
		},
		hideTabs: function(container){
			$(this.tabContainer[container] + '>div').hide();
			$(this.tabContainer[container] + '>ul>li').removeClass("activeTab");
		},
		openTab: function(tab,container){
			if( tab == null )
				tab = $(this.tabContainer[container] + '>div:first');

			this.hideTabs(container);
			$(tab).show();
			$(this.tabContainer[container] + '>ul>li>a[@href=#' + $(tab).attr('id') + ']').parent().addClass("activeTab");
		},
		addTabs: function(container){
			$(this.tabContainer[container] + '>ul>li>a').bind('click', {'container': container}, 
			function(){
				if( this.href.indexOf('#') > -1){
					var tabname = this.href.substr(this.href.indexOf('#'));
					$.tabs.openTab(tabname,event.data.container);
					return false;
				}
			 });
		},
		addTab: function(tab,container){
		$(this.tabContainer[container] + '>ul>li>a[@href=#' + tab + ']').bind('click', {'container': container},
			function(){
				if( this.href.indexOf('#') > -1){
					var tabname = this.href.substr(this.href.indexOf('#'));
					$.tabs.openTab(tabname,event.data.container);
					return false;
				}
			});
			this.openTab('#' + tab,container);
		},
		removeCurrent: function(container){
			$(this.tabContainer[container] + '>ul>li.activeTab').remove();
		}
	};
/* ]]> */
</script>
<script type="text/javascript">
/* <![CDATA[ */
	function addNewItem(){
		var ItemNumberId = NextItemId;
		var ItemName = "Item " + ItemNumberId;
		var ItemId = ItemName.replace(' ','-');
		
		NextItemId++;
		
		$('#container>ul').append('<li><a href="#' + ItemId + '" tabindex="1">' + ItemName + '</a></li>');
		
		var div = $('#Item-New').clone();
		$(div).html( $(div).html().replace(/\$ID/g,ItemNumberId) );
		$(div).attr('id',ItemId);
		$(div).attr('class','Item');
		$('#container').append( div );

		$.tabs.addTab(ItemId,'main');
		$.tabs.init('#Requirements-' + ItemNumberId,'Requirements-' + ItemNumberId);
		
		NextRequirementId['Requirements-' + ItemNumberId] = 0;
		
		return false;
	}
	function addNewItemRequirement(id){
		var container = 'Requirements-' + id;
		
		var RequirementNumberId = NextRequirementId[container] ? NextRequirementId[container] : 0;
		var RequirementName = 'Requirement ' + RequirementNumberId;
		var RequirementId = id + '-' + RequirementName.replace(' ','-');
		
		NextRequirementId[container] = RequirementNumberId+1;
		
		$('#' + container + '>ul').append('<li><a href="#' + RequirementId + '" tabindex="1">' + RequirementName + '</a></li>');
		
		var div = $('#Requirement-New').clone();
		$(div).html( $(div).html().replace(/\$ID/g,id).replace(/\$RID/g,RequirementNumberId) );
		$(div).attr('id',RequirementId);
		$(div).attr('class','Item');
		$('#' + container).append( div );

		$.tabs.addTab(RequirementId,container);

		return false;
		
	}
	function removeItem(ItemId){
		var ans = window.confirm('Are you sure you wish to remove' + ItemId + '?');
		if( ! ans )
			return  false;
		$.tabs.removeCurrent('main');
		$('#' + ItemId).remove();
		$.tabs.openTab(null,'main');
		return false;
	}
	function removeItemRequirement(ItemId,RequirementId){
		var ans = window.confirm('Are you sure you wish to remove' + RequirementId + '?');
		if( ! ans )
			return  false;
			
		$.tabs.removeCurrent('Requirements-' + ItemId);
		$('#' + ItemId + 'Requirement-' + RequirementId).remove();
		$.tabs.openTab(null,'Requirements-' + ItemId);
		return false;
	}
	$(document).ready(function() {
		$.tabs.init('#container','main');
	});
/* ]]> */
</script>
<script type="text/javascript">
		var NextItemId = <?php echo count($items); ?>;
		var NextRequirementId = [];
</script>
<div class="wrap">
	<h2>Plugins &amp; Themes</h2>
	<form name="wpupdate-manbager-options" method="post" action="options-general.php?page=wp-update-manager/wp-update-manager-options.php">
	<div id="container">
		<ul class="tab-nav">
		<?php 
			if( !empty($items) ){
				foreach((array)$items as $id=>$item){
					echo '<li><a href="#Item-'.$id.'" tabindex="1">' . $item['name']. '</a></li>';
				}
			}
		?>
		</ul>
		<a href="#" onclick="return addNewItem();" tabindex="1" accesskey="n" style="display:inline; margin-left:5px;">[+] New Item</a>
		<?php if( !empty($items) ){
		foreach((array)$items as $id=>$item){ ?>		
		<div id="Item-<?php echo $id; ?>" class="Item" style="display:none;">
			<div class="controlbox">
				<b>-</b> <a href="#remove" onclick="return removeItem('Item-<?php echo $id; ?>');">Remove Item</a><br />
			</div>
			<strong>Name:</strong><input type="text" name="item[<?php echo $id; ?>][name]" value="<?php echo attribute_escape($item['name']); ?>" />
			<strong>Slug:</strong><input type="text" name="item[<?php echo $id; ?>][slug]" value="<?php echo attribute_escape($item['slug']); ?>" /><br />
			<strong>Type:</strong>
			<input type="radio" name="item[<?php echo $id; ?>][type]" value="plugin"<?php checked('plugin',$item['type']); ?> />Plugin
			<input type="radio" name="item[<?php echo $id; ?>][type]" value="theme"<?php checked('theme',$item['type']); ?> />Theme<br />
			<strong>Version:</strong><input type="text" name="item[<?php echo $id; ?>][version]" value="<?php echo attribute_escape($item['version']); ?>" />&nbsp; 
			<strong>Last Updated:</strong><input type="text" name="item[<?php echo $id; ?>][lastupdated]" value="<?php echo attribute_escape($item['lastupdated']); ?>" />(yyy-mm-dd format please)<br />
			<strong>Author:</strong><input type="text" name="item[<?php echo $id; ?>][author]" value="<?php echo attribute_escape($item['author']); ?>" /><br />
			<strong>Author Homepage:</strong><input type="text" name="item[<?php echo $id; ?>][authorhome]" value="<?php echo attribute_escape($item['authorhome']); ?>" /><br />
			<strong>Plugin Homepage:</strong><input type="text" name="item[<?php echo $id; ?>][pluginhome]" value="<?php echo attribute_escape($item['pluginhome']); ?>" /><br />
			<strong>Download Link:</strong><input type="text" name="item[<?php echo $id; ?>][download]" value="<?php echo attribute_escape($item['download']); ?>" /><br />
			<strong>Requirements:</strong>
			<div id="Requirements-<?php echo $id; ?>" class="subsection">
				<script type="text/javascript">
					$(document).ready(function() {
						$.tabs.init('#Requirements-<?php echo $id; ?>','Requirements-<?php echo $id; ?>');
					});
					NextRequirementId['Requirements-<?php echo $id; ?>'] = <?php echo count($item['requirements']); ?>;
				</script>
				<ul class="tab-nav">
				<?php if( !empty($item['requirements']) ){
					foreach((array)$item['requirements'] as $rid=>$req){
						echo '<li><a href="#'.$id.'-Requirement-'.$rid.'" tabindex="1">' . $req['type'] . ' ' . $req['min']. '</a></li>';
				}} ?>
				</ul>
				<a href="#" onclick="return addNewItemRequirement('<?php echo $id; ?>');" tabindex="1" accesskey="n" style="display:inline; margin-left:5px;">[+] New Requirement</a>
				<?php if(is_array($item['requirements']) && !empty($item['requirements']) ){
				foreach((array)$item['requirements'] as $rid=>$req){ ?>
					<div id="<?php echo $id; ?>-Requirement-<?php echo $rid; ?>" class="Item">
						<div class="controlbox">
							<b>-</b> <a href="#remove" onclick="return removeItemRequirement('<?php echo $id; ?>','<?php echo $rid; ?>');">Remove Item</a><br />
						</div>
						<strong>Type:</strong>
						<select name="item[<?php echo $id; ?>][requirements][<?php echo $rid; ?>][type]">
							<option value="WordPress"<?php selected('WordPress',$req['type']); ?>>WordPress</option>
							<option value="PHP"<?php selected('PHP',$req['type']); ?>>PHP</option>
							<option value="MySQL"<?php selected('MySQL',$req['type']); ?>>MySQL</option>
							<option value="Plugin"<?php selected('Plugin',$req['type']); ?>>WordPress Plugin</option>
							<option value="PHPExt"<?php selected('PHPExt',$req['type']); ?>>PHP Extension</option>
						</select><br />
						<strong>Name:</strong>
						<input type="text" name="item[<?php echo $id; ?>][requirements][<?php echo $rid; ?>][name]" value="<?php echo attribute_escape($req['name']) ?>" /><br />
						<strong>Minimum Version:</strong>
						<input type="text" name="item[<?php echo $id; ?>][requirements][<?php echo $rid; ?>][min]" value="<?php echo attribute_escape($req['min']) ?>" /><br />
						<strong>Maximum Tested:</strong>
						<input type="text" name="item[<?php echo $id; ?>][requirements][<?php echo $rid; ?>][tested]" value="<?php echo attribute_escape($req['tested']) ?>" />
					</div>
				<?php }} ?>
			</div>
		</div>
		<?php }} ?>
	</div>
	<p class="submit">
		<input type="submit" name="submit_general" value="<?php _e('Save Options &raquo;'); ?>" />
	</p>
	</form>
</div>

<?php if( !empty($items) ){ ?>
<div class="wrap">
	<h2>Update URIs</h2>
	<table class="widefat plugins">
		<thead>
		<tr>
			<th>Name</th>
			<th>Update URI</th>
			<th>Update URI(with Pretty permalinks)</th>
		</tr>
		</thead>
	<?php foreach((array)$items as $id=>$item){ 
			$style = ('class="alternate"' == $style|| 'class="alternate active"' == $style) ? '' : 'alternate';
		?>
		<tr <?php echo $style; ?>>
			<td><?php echo $item['name']; ?></td>
			<td><?php 
					$url = get_bloginfo('siteurl');
					if( substr($url,-1) != '/' )
						$url .= '/';
					$url .= '?pluginupdate=' . $item['slug']; 
					echo "<a href='$url'>$url</a>";
					?></td>
			<td><?php 
					$url = get_bloginfo('siteurl') . '/pluginupdate/' . $item['slug'] . '/'; 
					echo "<a href='$url'>$url</a>";
				?></td>
		</tr>
	<?php } ?>
	</table>
</div>
<?php } ?>

<div id="Item-New" class="Item" style="display:none">
	<div class="controlbox">
		<b>-</b> <a href="#remove" onclick="return removeItem('Item-$ID');">Remove Item</a><br />
	</div>
	<strong>Name:</strong><input type="text" name="item[$ID][name]" value="Item $ID" />
	<strong>Slug:</strong><input type="text" name="item[$ID][slug]" value="" /><br />
	<strong>Type:</strong>
				<input type="radio" name="item[$ID][type]" value="plugin" checked="checked" />Plugin
				<input type="radio" name="item[$ID][type]" value="theme" />Theme<br />
	<strong>Version:</strong><input type="text" name="item[$ID][version]" value="" />&nbsp;
	<strong>Last Updated:</strong><input type="text" name="item[$ID][lastupdated]" />(yyy-mm-dd format please)<br />
	<strong>Author:</strong><input type="text" name="item[$ID][author]" value="<?php $user = wp_get_current_user(); echo $user->nickname;  ?>" /><br />
	<strong>Author Homepage:</strong><input type="text" name="item[$ID][authorhome]" value="<?php echo $user->user_url;  ?>" /><br />
	<strong>Plugin Homepage:</strong><input type="text" name="item[$ID][pluginhome]" value="<?php echo $user->user_url;  ?>" /><br />
	<strong>Download Link:</strong><input type="text" name="item[$ID][download]" value="" /><br />
	<strong>Requirements:</strong>
		<div id="Requirements-$ID" class="subsection">
			<ul class="tab-nav">
			</ul>
			<a href="#" onclick="return addNewItemRequirement('$ID');" tabindex="1" accesskey="n" style="display:inline; margin-left:5px;">[+] New Requirement</a>
		</div>
</div>
<div id="Requirement-New" class="Item" style="display:none">
	<div class="controlbox">
		<b>-</b> <a href="#remove" onclick="return removeItemRequirement('$ID','$RID');">Remove Item</a><br />
	</div>
	<strong>Type:</strong>
		<select name="item[$ID][requirements][$RID][type]">
			<option value="WordPress">WordPress</option>
			<option value="PHP">PHP</option>
			<option value="MySQL">MySQL</option>
			<option value="Plugin">WordPress Plugin</option>
			<option value="PHPext">PHP Extension</option>
		</select><br />
	<strong>Name:</strong><input type="text" name="item[$ID][requirements][$RID][name]" /><br />
	<strong>Minimum Version:</strong><input type="text" name="item[$ID][requirements][$RID][min]" /><br />
	<strong>Maximum Tested:</strong><input type="text" name="item[$ID][requirements][$RID][tested]" />
</div>