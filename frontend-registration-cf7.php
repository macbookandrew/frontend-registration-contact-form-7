<?php
/**
 * Plugin Name: Frontend Registration - Contact Form 7
 * Plugin URL: http://www.wpbuilderweb.com/frontend-registration-contact-form-7/
 * Description:  This plugin will convert your Contact form 7 in to registration form for WordPress. PRO Plugin available now with New Features. <strong>PRO Version is also available with New Features.</strong>. 
 * Version: 3.0
 * Author: David Pokorny
 * Author URI: http://www.wpbuilderweb.com
 * Developer: Pokorny David
 * Developer E-Mail: pokornydavid4@gmail.com
 * Text Domain: contact-form-7-freg
 * Domain Path: /languages
 * 
 * Copyright: © 2009-2015 izept.com.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
/**
 * 
 * @access      public
 * @since       1.1
 * @return      $content
*/
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

require_once (dirname(__FILE__) . '/frontend-registration-opt-cf7.php');

function cf7fr_editor_panels_reg ( $panels ) {
		
		$new_page = array(
			'Error' => array(
				'title' => __( 'Registration Settings', 'contact-form-7' ),
				'callback' => 'cf7fr_admin_reg_additional_settings'
			)
		);
		
		$panels = array_merge($panels, $new_page);
		
		return $panels;
		
	}
	add_filter( 'wpcf7_editor_panels', 'cf7fr_editor_panels_reg' );

add_filter('plugin_row_meta',  'my_register_plugins_link', 10, 2);
function my_register_plugins_link ($links, $file) {
   $base = plugin_basename(__FILE__);
   if ($file == $base) {
       $links[] = '<a href="http://www.wpbuilderweb.com/frontend-registration-contact-form-7/">' . __('PRO Version') . '</a>';
       $links[] = '<a href="http://www.wpbuilderweb.com/shop">' . __('More Plugins by David Pokorny') . '</a>';
       //$links[] = '<a href="http://www.wpbuilderweb.com/payment/">' . __('Donate') . '</a>';
   }
   return $links;
}
function cf7fr_admin_reg_additional_settings( $cf7 )
{
	
	$post_id = sanitize_text_field($_GET['post']);
	$tags = $cf7->scan_form_tags();
	$enable = get_post_meta($post_id, "_cf7fr_enable_registration", true);
	$cf7fru = get_post_meta($post_id, "_cf7fru_", true);
	$cf7fre = get_post_meta($post_id, "_cf7fre_", true);
	$cf7frr = get_post_meta($post_id, "_cf7frr_", true);
	$enablemail = get_post_meta($post_id, "_cf7fr_enablemail_registration", true);
	$selectedrole = $cf7frr;
	if(!$selectedrole)
	{
		$selectedrole = 'subscriber';
	}
	if ($enable == "1") { $checked = "CHECKED"; } else { $checked = ""; }
	if ($enablemail == "1") { $checkedmail = "CHECKED"; } else { $checkedmail = ""; }
	
	$selected = "";
	$admin_cm_output = "";
	
	$admin_cm_output .= "<div id='additional_settings-sortables' class='meta-box'><div id='additionalsettingsdiv'>";
	$admin_cm_output .= "<div class='handlediv' title='Click to toggle'><br></div><h3 class='hndle ui-sortable-handle'><span>Frontend Registration Settings</span></h3>";
	$admin_cm_output .= "<div class='inside'>";
	
	$admin_cm_output .= "<div class='mail-field'>";
	$admin_cm_output .= "<input name='enable' value='1' type='checkbox' $checked>";
	$admin_cm_output .= "<label>Enable Registration on this form</label>";
	$admin_cm_output .= "</div>";

	$admin_cm_output .= "<br />";
	$admin_cm_output .= "<div class='mail-field'>";
	$admin_cm_output .= "<input name='enablemail' value='' type='checkbox' $checkedmail>";
	$admin_cm_output .= "<label>Skip Contact Form 7 Mails ?</label>";
	$admin_cm_output .= "</div>";

	$admin_cm_output .= "<br /><table>";
	
	$admin_cm_output .= "<tr><td>Selected Field Name For User Name :</td></tr>";
	$admin_cm_output .= "<tr><td><select name='_cf7fru_'>";
	$admin_cm_output .= "<option value=''>Select Field</option>";
	foreach ($tags as $key => $value) {
		if($cf7fru==$value['name']){$selected='selected=selected';}else{$selected = "";}			
		$admin_cm_output .= "<option ".$selected." value='".$value['name']."'>".$value['name']."</option>";
	}
	$admin_cm_output .= "</select>";
	$admin_cm_output .= "</td></tr>";

	$admin_cm_output .= "<tr><td>Selected Field Name For Email :</td></tr>";
	$admin_cm_output .= "<tr><td><select name='_cf7fre_'>";
	$admin_cm_output .= "<option value=''>Select Field</option>";
	foreach ($tags as $key => $value) {
		if($cf7fre==$value['name']){$selected='selected=selected';}else{$selected = "";}
		$admin_cm_output .= "<option ".$selected." value='".$value['name']."'>".$value['name']."</option>";
	}
	$admin_cm_output .= "</select>";
	$admin_cm_output .= "</td></tr><tr><td>";
	$admin_cm_output .= "<input type='hidden' name='email' value='2'>";
	$admin_cm_output .= "<input type='hidden' name='post' value='$post_id'>";
	$admin_cm_output .= "</td></tr>";
	$admin_cm_output .= "<tr><td>Selected User Role:</td></tr>";
	$admin_cm_output .= "<tr><td>";
	$admin_cm_output .= "<select name='_cf7frr_'>";
	$editable_roles = get_editable_roles();
    foreach ( $editable_roles as $role => $details ) {
     $name = translate_user_role($details['name'] );
         if ( $selectedrole == $role ) // preselect specified role
             $admin_cm_output .= "<option selected='selected' value='" . esc_attr($role) . "'>$name</option>";
         else
             $admin_cm_output .= "<option value='" . esc_attr($role) . "'>$name</option>";
    }
    $admin_cm_output .="</select>";
	$admin_cm_output .= "</td></tr>";
	$admin_cm_output .="</table>";
	$admin_cm_output .= "</div>";
	$admin_cm_output .= "</div>";
	$admin_cm_output .= "</div>";

	echo $admin_cm_output;
	
}
// hook into contact form 7 admin form save
add_action('wpcf7_save_contact_form', 'cf7_save_reg_contact_form');

function cf7_save_reg_contact_form( $cf7 ) {

		$tags = $cf7->scan_form_tags();
	
		$post_id = sanitize_text_field($_POST['post']);
		
		if (!empty($_POST['enable'])) {
			$enable = sanitize_text_field($_POST['enable']);
			update_post_meta($post_id, "_cf7fr_enable_registration", $enable);
		} else {
			update_post_meta($post_id, "_cf7fr_enable_registration", 0);
		}
		if (isset($_POST['enablemail'])) {
			update_post_meta($post_id, "_cf7fr_enablemail_registration", 1);
		} else {
			update_post_meta($post_id, "_cf7fr_enablemail_registration", 0);
		}

		$key = "_cf7fru_";
		$vals = sanitize_text_field($_POST[$key]);
		update_post_meta($post_id, $key, $vals);

		$key = "_cf7fre_";
		$vals = sanitize_text_field($_POST[$key]);
		update_post_meta($post_id, $key, $vals);	

		$key = "_cf7frr_";
		$vals = sanitize_text_field($_POST[$key]);
		update_post_meta($post_id, $key, $vals);	
}
?>