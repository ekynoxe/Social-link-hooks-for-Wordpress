<?php
/* 
Plugin Name: Social Links Hooks
Plugin URI: http://www.ekynoxe.com/
Version: v1.00
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
		function SocialLinksHooks() { //constructor
		}
		function init() {
			$this->getAdminOptions();
		}
		//Returns an array of admin options
		function getAdminOptions() {
			$socialLinksHooksAdminOptions = array('twitter' => '',
				'facebook'	=> '', 
				'flickr'	=> '', 
				'youtube'	=> '',
				'vimeo'		=> '');
			
			$devOptions = get_option($this->adminOptionsName);
			if (!empty($devOptions)) {
				foreach ($devOptions as $key => $option)
					$socialHooksAdminOptions[$key] = $option;
			}				
			update_option($this->adminOptionsName, $socialHooksAdminOptions);
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
				if('' != $option){
					echo $options['before'] . '<a href="'.$option.'"><img src="'. SOCIAL_HOOKS_PLUGIN_URL.'/css/img/favicon-'.$key.'.png" alt="on '.$key.'">'.($options['show_labels']?$key:'').'</a>'. $options['after'];
				}
			}
		}
		
		function printAdminPage() {
			if(!is_admin()){
				return;
			}
			$devOptions = $this->getAdminOptions();

			if (isset($_POST['update_socialLinksHooksSettings'])) { 
				if (isset($_POST['slh_twitter'])) {
					$devOptions['twitter'] = apply_filters('content_save_pre', $_POST['slh_twitter']);
				}
				if (isset($_POST['slh_facebook'])) {
					$devOptions['facebook'] = apply_filters('content_save_pre', $_POST['slh_facebook']);
				}
				if (isset($_POST['slh_flickr'])) {
					$devOptions['flickr'] = apply_filters('content_save_pre', $_POST['slh_flickr']);
				}
				if (isset($_POST['slh_youtube'])) {
					$devOptions['youtube'] = apply_filters('content_save_pre', $_POST['slh_youtube']);
				}
				if (isset($_POST['slh_vimeo'])) {
					$devOptions['vimeo'] = apply_filters('content_save_pre', $_POST['slh_vimeo']);
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
			<tr valign="top">
				<th scope="row" class="twitter">Enter your twitter url:</th>
				<td>
					<input name="slh_twitter" type="text" id="social_hooks_twitter_data"
					value="<?php _e(apply_filters('format_to_edit',$devOptions['twitter']), 'SocialLinksHooks') ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="facebook">Enter your facebook url:</th>
				<td>
					<input name="slh_facebook" type="text" id="social_hooks_facebook_data"
					value="<?php _e(apply_filters('format_to_edit',$devOptions['facebook']), 'SocialLinksHooks') ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="flickr">Enter your flickr url:</th>
				<td>
					<input name="slh_flickr" type="text" id="social_hooks_flickr_data"
					value="<?php _e(apply_filters('format_to_edit',$devOptions['flickr']), 'SocialLinksHooks') ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="youtube">Enter your youtube url:</th>
				<td>
					<input name="slh_youtube" type="text" id="social_hooks_youtube_data"
					value="<?php _e(apply_filters('format_to_edit',$devOptions['youtube']), 'SocialLinksHooks') ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="vimeo">Enter your vimeo url:</th>
				<td>
					<input name="slh_vimeo" type="text" id="social_hooks_vimeo_data"
					value="<?php _e(apply_filters('format_to_edit',$devOptions['vimeo']), 'SocialLinksHooks') ?>" />
				</td>
			</tr>

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
?>