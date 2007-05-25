<?php
if( !get_option('update_install_enable') ){
	echo '<div class="error"><h1>Not Enabled</h1></div>';
	return;
}
require_once('includes/wp-update-class.php');
global $wpupdate;
$wpupdate = new WP_Update;
$step = isset($_GET['step']) ? (int)$_GET['step'] : ( isset($_POST['step']) ? (int)$_POST['step'] : 1);

if( isset($_POST['submit']) || isset($_GET['url']) ){
	
	switch($step - 1){ //-1 as we want to process for the step we just completed.
		case 0:
			break;
		case 1:
			if( isset($_GET['url']) ){
				//Download file
			} else {
				//Handle file upload
				var_dump($_FILES);
				if( $_FILES['themefile']['tmp_name'] )
					$wpupdate->installTheme($_FILES['themefile']['tmp_name'],$_FILES['themefile'] );
			}
			break;	
		case 2:
			break;
	}
	//Install from Local File
	echo '<div class="error fade">';
	var_dump($_POST);
	var_dump($_FILES);
	echo '</div>';
}
?>
<style>
	.section{
		margin-left:2em;
	}
</style>
<div class="wrap">
	<h2>Install a Theme</h2>
<?php
	switch($step){
		case 4:
			step4();
			break;
		case 3:
			step3();
			break;
		case 2:
			step2();
			break;
		case 1:
		default:
			step1();
	}
?>
<?php 
function step1(){
	global $wpupdate,$error;
 ?>
	<h3>Step 1:</h3>
		<div class="section">
			<h4>Upload file</h4>
			<p>
			<form enctype="multipart/form-data" name="installlocalfile" method="POST">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo ((int)ini_get('post_max_size'))*1024*1024; ?>" />
			<input type="hidden" name="step" value="2" />
			Select File: <input type="file" name="themefile" />&nbsp;<input type="submit" name="submit" value="Upload &raquo;" /><br />
			<strong>Max filesize:</strong><?php echo ini_get('post_max_size'); ?>
			</form>
			OR
			<h4>Via a Search</h4>
			<p>To install themes directly without uploading them yourself, Please use the <a href="themes.php?page=wp-update/wp-update-themes-search.php">Theme Search</a> tab, and select "Install" on the item.</p>
			</p>
		</div>
<?php } ?>
<?php 
function step2(){ 
	global $wpupdate;
?>
	<h3>Step 2: Installing</h3>
	<div class="section">
		<p>
			<strong>Filename:</strong> <?php echo $filename; ?><br />
			<strong>Valid Theme:</strong> <?php echo $validtheme; ?><br />
		</p>
	</div>
<?php } ?>
</div>