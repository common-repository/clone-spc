<?php
/*
Plugin Name: Clone S.P.C.
Plugin URI: http://www.zetrider.ru
Description: Plugin to create individual templates Single, Page и Category
Version: 2.1
Author: ZetRider
Author URI: http://www.zetrider.ru
Author Email: ZetRider@bk.ru
*/
/*  Copyright 2011  zetrider  (email: zetrider@bk.ru)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

load_plugin_textdomain('clone-spc', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)). '/lang/');

function clone_spc(){
	add_options_page('Clone SPC', 'Clone SPC', 8, 'setting_clone_spc', 'setting_clone_spc');
}
add_action('admin_menu', 'clone_spc');

function setting_clone_spc() {
	$WPCSPC_PLUGIN_URL = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	$WPCSPC_PATCH_THEME = $_SERVER['DOCUMENT_ROOT']."/wp-content/themes/".get_option('template')."/"; 

?>
<div class="wrap">
<link rel="stylesheet" href="<?php echo $WPCSPC_PLUGIN_URL; ?>style.css" type="text/css" media="screen" />
			<a href="http://wordpress.org/extend/plugins/clone-spc/" target="_blank"><img src="<?php echo $WPCSPC_PLUGIN_URL; ?>images/wpo.jpg"></a>
			<a href="http://www.zetrider.ru/" target="_blank"><img src="<?php echo $WPCSPC_PLUGIN_URL; ?>images/zwd.jpg"></a><br style="clear:both;">
			<a href="http://www.ttweb.ru/" target="_blank"><img src="<?php echo $WPCSPC_PLUGIN_URL; ?>images/stt.jpg"></a>
			<a href="http://www.zetrider.ru/donate" target="_blank"><img src="<?php echo $WPCSPC_PLUGIN_URL; ?>images/dwy.jpg"></a>
<h2><?php _e("Cloning theme files", "clone-spc"); ?></h2>
<?php echo __("Around the errors? So the problem with CHMOD, click ", "clone-spc")."<a href='options-general.php?page=setting_clone_spc&action=chmodspc'>".__("here to update the CHMOD - Dir: 0755; File: 0644 (should help)", "clone-spc")."</a>"; ?>
<br>

<?php
$old_name_file = $WPCSPC_PATCH_THEME.$_POST['old_name_file'];
$new_name_file = $WPCSPC_PATCH_THEME.$_POST['new_name_file'];

if (isset($_POST['clon'])){
	if (empty($_POST['new_name_file'])) {
		$error .= __("The field can not be empty!<br>", "clone-spc");
	}
	elseif (file_exists($new_name_file)) {
		$error .= __("Filename ", "clone-spc").$_POST['new_name_file']." ".__(" already exists! Think more!", "clone-spc");
	}
	elseif(ereg("[а-яА-Я]+", $_POST['new_name_file'])){
		$error .= __("You can not use Russian characters!", "clone-spc");
	}

}
echo '<span style="font-weight:bold; color:red;">'.$error.'</span>';
	
if (isset($_POST['clon']) && !$error) {
	$basename = basename ($_POST['new_name_file'],".php");
	fopen($old_name_file, "r");
	copy($old_name_file, $new_name_file);
	$fopen=fopen($new_name_file,"r+");
	if ($_POST['templatename'] == "1"){ $add = "<?php \n /* \n Template Name: ".$basename."  \n */ \n?>\n"; }
	$add .= fread($fopen,filesize($new_name_file));
	fseek($fopen,0,SEEK_SET);
	fwrite($fopen,$add);
	fclose($fopen);
	echo '<span style="font-weight:bold; color:green;">'.__("File ", "clone-spc").$_POST['new_name_file'].__(" successfully cloned!", "clone-spc").'</span>';
}
if (isset($_POST['del'])) {
	unlink($old_name_file); 
}

echo '<div class="clone_file radius_spc" style="background-color:#fff;"><h3>'.__("The original name", "clone-spc").'</h3> <input type="text" value="'.__("NEW NAME", "clone-spc").'" class="newname" disabled> <img src="'.$WPCSPC_PLUGIN_URL.'images/clon.png"> - '.__("clone", "clone-spc").' <img src="'.$WPCSPC_PLUGIN_URL.'images/del.png"> - '.__("remove", "clone-spc").' </div>'."\n";

$dir = opendir($WPCSPC_PATCH_THEME);
while(($file = readdir($dir)) != false) {
	$expansion = substr(strrchr($file, '.'), 1);

	if ($expansion == "php") {
		echo '<form method="POST">';
		echo '<div class="clone_file radius_spc"><h3>'.$file.'</h3> <input type="hidden" value="'.$file.'" name="old_name_file"> <input type="text" value="'.$file.'" name="new_name_file" class="newname"> <input type="submit" value="" name="clon" class="clon"> <input type="submit" value="" name="del" class="del"> <label>'.__("Add Template Name?", "clone-spc").' <input type="checkbox" name="templatename" value="1"></label></div>'."\n";
		echo '</form>';
	}
	
}
closedir($dir);
?>

</div>

<?php
if ($_GET['action'] == 'chmodspc'){
	function chmodspc($spc) {
		global $WPCSPC_PATCH_THEME;
		$chmod_file = 0644;
		$chmod_folde = 0755;
		if (file_exists($spc)) {
			if (is_dir($spc)) {
				if ($spc !=$WPCSPC_PATCH_THEME) { chmod($spc,$chmod_folde); }
				$opendir = opendir($spc);
				while($filename = readdir($opendir))
				if ($filename != "." && $filename != "..") chmodspc($spc."/".$filename);
				closedir($opendir);
			} else {
				chmod($spc, $chmod_file);
			}
		}
	}
	chmodspc($WPCSPC_PATCH_THEME);
}

}
?>