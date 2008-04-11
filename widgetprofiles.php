<?php
/*
Plugin Name: Widget Profiles
Plugin URI: http://iheartwp.com/
Description: This little plugin allows you to save widget profiles and builds.  Look under the 'Design' tab for 'Widget Profiles'.  If you can donate please do.  
Author: Dubayou, Will Wharton, Zax, Michael Zaxby
Version: 1.1
Author URI: http://iheartwp.com/

Please if you can donate to my development it would be most appreciated, I'm just a poor college student attending the University of North Carolina at Wilmington. 
http://iheartwp.com/donate-to-development/
Even $1 dollar helps.

Changelog:

April 09, 2008: Initial Release. Can save/load/ and undo widget profiles.  Very helpful.

FAQ:

None Yet; Lets Talk WordPress! iheartwpMZ@gmail.com

Features to Come:

I'm a little lazy have haven't coded this yet, but I want it to warn you if you load a widget build for a theme it doesn't work on.  And/Or convert/update for new sidebar setups?

*/




// hooks
register_activation_hook(__FILE__,'widgetprofiles_install');
add_action('init', 'widgetprofiles_init');
add_action('admin_head', 'widgetprofiles_head');
//
// hook code
//
function widgetprofiles_init() {
	add_action('admin_menu', 'widgetprofiles_add_menus');
}
function widgetprofiles_add_menus() {
	add_submenu_page('themes.php', 'Widgets Profiles', 'Widgets Profiles', 8, __FILE__, 'widgetprofiles_run');
}
function widgetprofiles_head() {
	global $wp_db_version;
	// Provide css based on wordpress version. Version 2.3.3 and below:
	if ($wp_db_version <= 6124) {
		echo '<link rel="stylesheet" type="text/css" href="http://iheartwp.com/inc/widgetprofiles.css" />';
		// Include JQUERY where needed
		/*
		if( strpos($_SERVER['REQUEST_URI'], 'post.php')
		|| strstr($_SERVER['PHP_SELF'], 'page-new.php')
		|| $_GET['page']=="Downloads"
		|| strstr($_SERVER['PHP_SELF'], 'post-new.php') )
		{
			echo '<script type="text/javascript" src="../wp-includes/js/jquery/jquery.js"></script>';
		}
		*/
	} else {
		// 2.5 + with new interface
		echo '<link rel="stylesheet" type="text/css" href="http://iheartwp.com/inc/widgetprofiles.2.5.css" />';
	}
}

//
// the install
//
function widgetprofiles_install() {
//make old_sidebars_widgets options fore reverting
//make saved_sidebars_widgets option for saving arrangements
	  add_option("old_sidebars_widgets", "");
	  add_option("saved_sidebars_widgets", "");
}


//
//the main display code
//
function widgetprofiles_run(){
	$act = "display";
	if(isset($_GET['dowhat']))
		$act = $_GET['dowhat'];

	echo '<div class="wrap">';
	if($act=="savebuild"){
		widgetprofiles_widgetprofilesbuild();
	}
	if($act=="restore"){
		widgetprofiles_changeto($_GET['widgetid']);
	}
	if($act=="remove"){
		widgetprofiles_removeprofile($_GET['widgetid']);
	}
	if($act=="undo"){
		widgetprofiles_undo();
	}
	if($act=="display"){
		widgetprofiles_display();
	}
echo '</div>';
}

//
//the functions and such ;)
//

//widgetprofiles_undo()
//allow the user to undo back to last widget build
function widgetprofiles_undo(){
	update_option('sidebars_widgets', get_option("old_sidebars_widgets"));
	echo "Your site has been reverted back!<br>";
widgetprofiles_display();
	//echo "undo code missing. Lame";
	//set sidebars_widgets to old_sidebars_widgets
}

//widgetprofiles_changeto($num)
//$num is index of widgetbuild in old_sidebars_widgets
//change/update current widgets
function widgetprofiles_changeto($num){
update_option('old_sidebars_widgets', get_option("sidebars_widgets"));
$allwidgets = get_option("saved_sidebars_widgets");
update_option('sidebars_widgets', $allwidgets[$num]['widgets']);
	//put current sidebars_widgets into old_sidebars_widgets

	//open saved_sidebars_widgets
	//get array item for 
	//update sidebars_widgets with item
echo "In case your widges arn't looking good, you can <a href=\"themes.php?page=widgetprofiles.php&dowhat=undo\">Undo</a> this.<br>";
widgetprofiles_display();
	//display undo link
}


//widgetprofiles_display($theme)
//$theme is the current theme or defaults to ""
//display list of currently saved widgets
function widgetprofiles_display($theme=""){	
echo "<h2>Save current Widget Arrangement</h2><br>";			
?>
<form enctype="multipart/form-data" action="?page=widgetprofiles.php&dowhat=savebuild" method="post" id="widgetprofiles" name="savewidgetprofile"> 
Profile Note: <input type="text" name="desc" value="A quick description..."><input type="submit" value="Save Current Widget Arrangement">
</form>
<?php
			
			
		echo "<br><h2>Your Saved Widget Builds</h2><br>";

	$allwidgets = get_option("saved_sidebars_widgets");  //open saved_sidebars_widgets
	?>
	<table class="widefat"> 
			<thead>
				<tr>
				<th scope="col" style="text-align:center"><a href="?page=WidgetProfiles"><?php _e('ID',"wp-widgetprofile"); ?></a></th>				
				<th scope="col"><a href="?page=WidgetProfiles"><?php _e('Theme',"wp-widgetprofile"); ?></a></th>
				<th scope="col" style="text-align:center"><?php _e('Description',"wp-widgetprofile"); ?></th>
				<th scope="col"><a href="?page=WidgetProfiles"><?php _e('Date Saved',"wp-widgetprofile"); ?></a></th>								
				<th scope="col"><?php _e('Action',"wp-widgetprofile"); ?></th>
				</tr>
			</thead>						
		<?php	


				if (!empty($allwidgets)) {
					echo '<tbody id="the-list">';
					for($i=0;$i<count($allwidgets);$i++){ 
						$date = date("jS M Y", strtotime($allwidgets[$i]['date']));

						echo ('<tr class="alternate">');
						echo '<td style="text-align:center">'.$i.'</td>						
						<td>'.$allwidgets[$i]['theme'].'</td>
						<td style="text-align:left">'.$allwidgets[$i]['desc'].'</td>
						<td style="text-align:left">'.$allwidgets[$i]['date'].'</td>
						<td><a href="?page=widgetprofiles.php&dowhat=restore&widgetid='.$i.'"><img src="http://iheartwp.com/inc/edit.png" alt="Restore" title="Restore" /></a> <a href="?page=widgetprofiles.php&dowhat=remove&widgetid='.$i.'"><img src="http://iheartwp.com/inc/cross.png" alt="Delete" title="Delete" /></a></td>';

					}
					echo '</tbody>';
				} else echo '<tr><th colspan="6">'.__('No Saved Profiles.',"wp-widgetprofile").'</th></tr>'; // FIXED: 1.6 - Colspan changed
		?>			
		</table>
	<?php
	
	
	/*
echo "<table><tr><td><b>id</b></td><td><b>theme</b></td><td><b>description</b></td><td><b>actions</b></td></tr>";
	for($i=0;$i<count($allwidgets);$i++){   //loop array of widgetcode,theme,desc
		echo "<tr><td>".$i."</td><td>";
		echo $allwidgets[$i]['theme']."</td><td>";
		echo $allwidgets[$i]['desc']."</td><td>";
		echo "<a href=\"themes.php?page=widgetprofiles.php&dowhat=restore&widgetid=".$i."\">Restore</a> / <a href=\"themes.php?page=widgetprofiles.php&dowhat=remove&widgetid=".$i."\">Remove</a></td></tr>";


	}
echo "</table>";
*/
	
	
}


//widgetprofiles_widgetprofilesbuild
//save the current arrangement
function widgetprofiles_widgetprofilesbuild() {
	$allwidgets = array();
	$currentwidgets = array();
	$currentwidgets = get_option("sidebars_widgets");   //get current value from sidebars_widgets
	$allwidgets = get_option("saved_sidebars_widgets");


	$theme =  get_option("current_theme");  //and template name from current_theme

	$tempsaved = array();           //pack data into array with widgetcode,theme,desc,
	$tempsaved['theme'] = $theme;
	$tempsaved['desc'] = $_POST['desc'];
	$tempsaved['widgets'] = $currentwidgets;
	$tempsaved['date'] = time();
	
	
	$allwidgets[] = $tempsaved;


	update_option('saved_sidebars_widgets', $allwidgets);  //update saved_sidebars_widgets with append

	echo "A new widget profile for the theme ".$theme." has been created.<br>";
	
	widgetprofiles_display();	
}

//widgetprofiles_removeprofile
//$id is the profile id
//remove profile id
function widgetprofiles_removeprofile($id) {
	$newwidgets= array();
	$allwidgets = get_option("saved_sidebars_widgets");
	
	for($i=0;$i<count($allwidgets);$i++){
		if($i==$id){
			//kill it. skip it.
		}else{
			$newwidgets[] = $allwidgets[$i];
		}	
	}

	update_option('saved_sidebars_widgets', $newwidgets);  //update saved_sidebars_widgets with append

	widgetprofiles_display();	
}
?>
