<?php
/*
Plugin Name: Landing Page Language
Plugin URI: http://solid-code.co.uk/2011/06/landing-page-language/
Description: Re-directs users to specific language landing pages that have been created by you.
Version: 1.0.0
Author: Dagan Lev
Author URI: http://solid-code.co.uk

Copyright 2011 Solid Code  (email : dagan@solid-code.co.uk)

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
if (!class_exists("lpl_landing_page")) {
	class lpl_landing_page{
		//the constructor that initializes the class
		function lpl_landing_page() {
		}
	}
	
	//initialize the class to a variable
	$lpl_landing_page = new lpl_landing_page();
	
	//Actions and Filters	
	if (isset($lpl_landing_page)) {
		session_start();
		//Actions
		add_action('admin_init', 'lpl_landing_page_register' );
		add_action('admin_menu', 'lpl_landing_page_settings_box');
		//add the redirect hook to check when page loads if there are any language pages
		add_action('template_redirect', 'lpl_landing_page_redirect');
	}
	
	/**
	 * Paint settings box
	 */
	function lpl_landing_page_settings_box(){
		add_options_page('Landing Page Language Defaults', 'Landing Page Language Defaults', 'manage_options', 'lpl_language_defaults', 'lpl_language_defaults');
	}

	/**
	 * Paints the settings page
	 */	 
	function lpl_language_defaults(){
		?>
		<div class="wrap">
		<h2>Landing Page Language Defaults</h2>
		<p>If you have your language landing pages not on the root please specify in the box below on what folder/path you have them on</p>
		<p>Currently the system will look for <b><?php bloginfo('siteurl');
		if(get_option('lpl_url')!=''){
			echo '/' . get_option('lpl_url');
		}
		?>/en/</b></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'lpl_landing_page' ); ?>
			
			<input size="50" type="text" name="lpl_url" value="<?php echo get_option('lpl_url'); ?>" />
			
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
		</div>		
		<?php
	}
	
	/**
	 * register settings
	 */
	function lpl_landing_page_register(){
		register_setting( 'lpl_landing_page', 'lpl_url' );
	}
	
	/**
	 * When page load check to see if there are any language pages and if we need to redirect
	 */
	function lpl_landing_page_redirect(){
		if(is_home()||is_front_page()){
			//first extract the default language from the browser
			if(preg_match('/([^-,]*)/',$_SERVER['HTTP_ACCEPT_LANGUAGE'],$browselang)){
				if(strlen($browselang[1])==2){
					//now extract blog language and make sure it is not the same (as we don't need to redirect if it is)
					if(preg_match('/([^-]*)/',get_bloginfo('language'),$wordpresslang)){
						if(strlen($wordpresslang[1])==2){
							if($browselang[1]!=$wordpresslang[1]){
								//now check to see if the language page exists by testing it against the page titles
								if(get_option('lpl_url')!=''){
									$lplpage = get_page_by_path(get_option('lpl_url') . '/' . $browselang[1]);
								}else{
									$lplpage = get_page_by_path($browselang[1]);	
								}
								if($lplpage&&$_SESSION['lpl_page_redirected']!=1){
									if($lplpage->post_status=='publish'){
										$_SESSION['lpl_page_redirected'] = 1;
										header( 'Location: ' . get_permalink($lplpage->ID) );	
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
?>