<?php
/* 
Plugin Name: Social Links Hooks
Plugin URI: http://www.ekynoxe.com/
Version: v1.01
Author: <a href="http://mathieudavy.com/">Mathieu Davy</a>
Description: A plugin to have available social links to use on your website
 
Copyright 2010  Mathieu Davy  (email : mat t [a t] ek yn o xe [d ot] c om)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'SOCIAL_HOOKS_PLUGIN_BASENAME' ) )
	define( 'SOCIAL_HOOKS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'SOCIAL_HOOKS_PLUGIN_NAME' ) )
	define( 'SOCIAL_HOOKS_PLUGIN_NAME', trim( dirname( SOCIAL_HOOKS_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'SOCIAL_HOOKS_PLUGIN_URL' ) )
	define( 'SOCIAL_HOOKS_PLUGIN_URL', WP_PLUGIN_URL . '/' . SOCIAL_HOOKS_PLUGIN_NAME );

if (!class_exists("SocialLinksHooks")) {
	class SocialLinksHooks {
		
		var $adminOptionsName = "SocialLinksHooksAdminOptions";
		/* definitions as an array of services, each one being:
		array(
			type			=> 'text','checkbox', // This is going to be fed into the type="XXX" of an input tag! Be careful!
			default_value	=> '','on|off',
			label			=> 'free text'
		)
		*/
		var $optionsDefinitions = array(
			'twitter' => array('text','','Enter your twitter url:'),
			'facebook'	=> array('text','','Enter your facebook url:'),
			'flickr'	=> array('text','','Enter your flickr url:'),
			'youtube'	=> array('text','','Enter your youtube url:'),
			'vimeo'		=> array('text','','Enter your vimeo url:'),
			'skype'		=> array('text','','Enter your skype url:'),
			'plusone'	=> array('checkbox',false,"Check this box if you want the +1 button to be activated for the canonical doman of your site (this will only share your site's root url)"),
			'linkedin'	=> array('text','','Enter your linkedin url:')
			);
			
		function SocialLinksHooks() { //constructor
		}
		function init() {
			$this->getAdminOptions();
		}
		//Returns an array of admin options
		function getAdminOptions() {
			$devOptions = get_option($this->adminOptionsName);
			
			if (!empty($devOptions)) {
				foreach ($devOptions as $key => $option)
					$socialHooksAdminOptions[$key] = $option;
			}
			return $socialHooksAdminOptions;
		}
		
		public function printLinks($options=array()){
			
			if(!array_key_exists('before',$options))
				$options['before'] = '<dd>';
			if(!array_key_exists('after',$options))
				$options['after'] = '</dd>';
			if(!array_key_exists('show_labels',$options))
				$options['show_labels'] = true;
				
			$devOptions = get_option($this->adminOptionsName);
			
			foreach ($devOptions as $key => $option){
					if ($key == 'plusone') :
		?>
				<g:plusone size="small" annotation="bubble"></g:plusone>
		<?php
					else :
						if('' != $option){
							echo $options['before'] . '<a href="'.$option.'"><img src="'. SOCIAL_HOOKS_PLUGIN_URL.'/css/img/favicon-'.$key.'.png" alt="on '.$key.'">'.($options['show_labels']?$key:'').'</a>'. $options['after'];
						}
					endif;
			}
		}
		
		function printAdminPage() {
			if(!is_admin()){
				return;
			}
			$devOptions = $this->getAdminOptions();

			if (isset($_POST['update_socialLinksHooksSettings'])) {
				
				foreach($this->optionsDefinitions as $key => $def){
					switch($def[0]){
						case 'text':
							if (isset($_POST['slh_'.$key])) {
								$new_value = $_POST['slh_'.$key];
							}
						break;
						case 'checkbox':
							
							if (isset($_POST['slh_'.$key])) {
								$new_value = $_POST['slh_'.$key];
							} else {
								$new_value = '';
							}
						break;
					}
					
					$devOptions[$key] = apply_filters('content_save_pre', $new_value);
				}
				update_option($this->adminOptionsName, $devOptions);
				
		?>
		<div class="updated"><p><strong><?php _e("Settings Updated.", "SocialLinksHooks");?></strong></p></div>
		<?php
			}
		?>
		<div class="wrap">
		<h2>Social Links Hooks Options Page</h2>
		
		<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<?php wp_nonce_field('update-options'); ?>
		
		<table id="social-links-hooks-form">
			
			<?php
			foreach($this->optionsDefinitions as $key => $def) :
			?>
					<tr valign="top">
						<th scope="row" class="<?php echo $key; ?>"><?php echo $def[2]; ?></th>
						<td>
			<?php
			
				switch($def[0]){
					case 'text':
			?>
					<input name="slh_<?php echo $key; ?>" type="<?php echo $def[0]; ?>" id="social_hooks_<?php echo $key; ?>_data"
					value="<?php _e(apply_filters('format_to_edit',$devOptions[$key]), 'SocialLinksHooks') ?>" />
			<?php
					break;
					case 'checkbox':
			?>
					<input name="slh_<?php echo $key; ?>" type="checkbox" id="social_hooks_<?php echo $key; ?>_data"
					<?php if($devOptions[$key]) { echo 'checked="checked"'; } ?> />
			<?php
					break;
				}
			?>
						</td>
					</tr>
			<?php
			endforeach;
			?>
		</table>

		<input type="hidden" name="update_socialLinksHooksSettings" value="update" />

		<p>
		<input type="submit" value="<?php _e('Save Changes') ?>" />
		</p>

		</form>
		</div>
		<?php
		} //End function printAdminPage()
	}
}
 //End Class SocialLinksHooks

if (class_exists("SocialLinksHooks")) {
	$sl_hooks = new SocialLinksHooks();
}

//Initialize the admin panel
if (!function_exists("SocialLinksHooks_ap")) {
	function SocialLinksHooks_ap() {
		global $sl_hooks;
		if (!isset($sl_hooks)) {
			return;
		}
		if (function_exists('add_options_page')) {
			add_options_page('Social Links Hooks', 'Social Links Hooks', 9, basename(__FILE__), array(&$sl_hooks, 'printAdminPage'));
		}
	}	
}

//Actions and Filters
if (isset($sl_hooks)) {
	//Actions
	add_action('admin_menu', 'SocialLinksHooks_ap');
	add_action('activate_social-links-hooks/social-links-hooks.php',  array(&$sl_hooks, 'init'));
}

function admin_register_head() {
	$siteurl = get_option('siteurl');
	$url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/social-links-hooks.css';
	echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}
add_action('admin_head', 'admin_register_head');

function register_head() {
	/*
	HERE WE NEED TO CHECK IF ANY OF THE GOOGLE PLUS OPTION IS ACTIVE TO INCLUDE THE SCRIPT TAG
	ADDING BY DEFAULT FOR NOW
	*/
	echo '<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>';
}
add_action('wp_head', 'register_head');
?>