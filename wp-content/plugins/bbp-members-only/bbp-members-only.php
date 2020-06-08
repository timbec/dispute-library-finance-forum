<?php
/*
Plugin Name: bbPress Members Only
Description: Only registered users can view your site, non members can only see a login/home page and forums archive / forums homepage with no registration options
Version: 1.4.1
Author: Tomas Zhu
Author URI: https://bbp.design
Plugin URI: https://bbp.design
Text Domain: bbp-members-only

Copyright 2016 - 2020  Tomas Zhu  (email : support@bbp.design)

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
if (!defined('ABSPATH'))
{
	exit;
}
define('BBP_MEMBERSONLY_PLUGIN_URL', plugin_dir_url( __FILE__ ));

require_once("bbpmembersonlypagesettings.php");

add_action('admin_menu', 'bmo_tomas_bbp_members_only_option_menu');

/**** localization ****/
add_action('plugins_loaded','bmo_tomas_bbp_members_only_load_textdomain');

function bmo_tomas_bbp_members_only_load_textdomain()
{
	load_plugin_textdomain('bbp-members-only', false, dirname( plugin_basename( __FILE__ ) ).'/languages/');
}

function bmo_tomas_bbp_members_only_option_menu()
{

   add_menu_page(__('bbPress Members Only', 'bbp-members-only'), __('bbPress Members Only', 'bbp-members-only'), 'manage_options', 'bbpmemberonlyfree', 'bmo_tomas_bbp_members_only_free_setting');
   add_submenu_page('bbpmemberonlyfree', __('bbPress Members Only','bbp-members-only'), __('bbPress Members Only','bbp-members-only'), 'manage_options', 'bbpmemberonlyfree', 'bmo_tomas_bbp_members_only_free_setting');
   add_submenu_page('bbpmemberonlyfree', __('bbPress Members Only','bbp-members-only'), __('Optional Settings','bbp-members-only'), 'manage_options', 'bpmemberoptionalsettings', 'bbp_members_only_free_optional_setting');   
}

//!!!start
$bbpdisableallfeature = get_option('bbpdisableallfeature');

if ('yes' == $bbpdisableallfeature)
{
	return;
}
//!!!end

function bmo_tomas_bbp_members_only_free_setting()
{
		global $wpdb;
				
		$m_bbpmoregisterpageurl = get_option('bbpmoregisterpageurl');

		if (isset($_POST['bbpmosubmitnew']))
		{
			check_admin_referer( 'bmo_tomas_bbp_members_only_nonce' );
			if (isset($_POST['bbpmoregisterpageurl']))
			{
				$m_bbpmoregisterpageurl = esc_url($_POST['bbpmoregisterpageurl']);
			}
			else 
			{
				
			}

			update_option('bbpmoregisterpageurl',$m_bbpmoregisterpageurl);
			if (isset($_POST['bbpopenedpageurl']))
			{
				//$bbpopenedpageurl = sanitize_textarea_field($_POST['bbpopenedpageurl']);

				$bbpopenedpagechecktextarea = $_POST['bbpopenedpageurl'];
				$bbpopenedpagecheckarray = explode("\n", $bbpopenedpagechecktextarea);

				$bbpopenedpagefilteredarray = array();
				$bbpopenedpageurl = '';
				
				if ((is_array($bbpopenedpagecheckarray)) && (count($bbpopenedpagecheckarray) >0))
				{
					foreach ($bbpopenedpagecheckarray as $bbpopenedpagechecksingle)
					{
						$bbpopenedpagechecksingle = esc_url($bbpopenedpagechecksingle);
						if (strlen($bbpopenedpagechecksingle) > 0)
						{
							$bbpopenedpagefilteredarray[] = $bbpopenedpagechecksingle;
						}
					}
				}
				
				if ((is_array($bbpopenedpagefilteredarray)) && (count($bbpopenedpagefilteredarray) >0))
				{
					$bbpopenedpageurl = implode("\n",$bbpopenedpagefilteredarray);
				}
				
				if (strlen($bbpopenedpageurl) == 0)
				{
					delete_option('bbp_members_only_saved_open_page_url',$bbpopenedpageurl);
				}
				else 
				{
					update_option('bbp_members_only_saved_open_page_url',$bbpopenedpageurl);
				}
			}

			$bpmoMessageString =  __( 'Your changes has been saved.', 'bbp-members-only' );
			bmo_tomas_bbp_members_only_message($bpmoMessageString);
		}
		echo "<br />";

		$saved_register_page_url = get_option('bbpmoregisterpageurl');
		?>

		<div style='margin:10px 5px;'>
		<div style='float:left;margin-right:10px;'>

		<img src='<?php echo plugins_url('/images/new.png', __FILE__);  ?>' style='width:30px;height:30px;'>
		
		</div> 
		<div style='padding-top:5px; font-size:22px;'>bbPress Members Only Setting:</div>
		</div>
		<div style='clear:both'></div>		
			<div class="wrap">
				<div id="dashboard-widgets-wrap">
			    	<div id="dashboard-widgets" class="metabox-holder">
						<div id="post-body"  style="width:60%;">
							<div id="dashboard-widgets-main-content">
								<div class="postbox-container" style="width:98%;">
									<div class="postbox" >
										<h3 class='hndle' style='padding: 20px; !important'>
											<span>
											<?php 
												echo  __( 'Opened Pages Panel:', 'bbp-members-only' );
											?>
											</span>
										</h3>
								
										<div class="inside" style='padding-left:10px;'>
											<form id="bpmoform" name="bpmoform" action="" method="POST">
											<?php 
											wp_nonce_field('bmo_tomas_bbp_members_only_nonce');
											?>
											<table id="bpmotable" width="100%">
											<tr>
											<td width="30%" style="padding: 20px;">
											<?php 
												echo  __( 'Register Page URL:', 'bbp-members-only' );
												echo '<div style="color:#888 !important;"><i>';
												echo  __( '(or redirect url)', 'bbp-members-only' );
												echo '</i></div>';
											?>
											</td>
											<td width="70%" style="padding: 20px;">
											<input type="text" id="bbpmoregisterpageurl" name="bbpmoregisterpageurl"  style="width:500px;" size="70" value="<?php  echo esc_url($saved_register_page_url); ?>">
											</td>
											</tr>
										
											<tr style="margin-top:30px;">
											<td width="30%" style="padding: 20px;" valign="top">
											<?php 
												echo  __( 'Opened Page URLs:', 'bbp-members-only' );
											?>
											</td>
											<td width="70%" style="padding: 20px;">
											<?php 
											$urlsarray = get_option('bbp_members_only_saved_open_page_url'); 
											?>
											<textarea name="bbpopenedpageurl" id="bbpopenedpageurl" cols="70" rows="10" style="width:500px;"><?php echo sanitize_textarea_field($urlsarray); ?></textarea>
											<p><font color="Gray"><i><?php echo  __( 'Enter one URL per line please.', 'bbp-members-only' ); ?></i></p>
											<p><font color="Gray"><i><?php echo  __( 'These pages will opened for guest and guest will not be directed to register page.', 'bbp-members-only' ); ?></i></p>					
											</td>
											</tr>
											</table>
											<br />
											<input type="submit" id="bbpmosubmitnew" name="bbpmosubmitnew" value=" Submit " style="margin:1px 20px;">
											</form>
											<br />
										</div>
									</div>
								</div>
							</div>
						</div>
									<?php 
									tomas_bbpress_members_only_admin_sidebar_about();
									?>						
		    		</div>
				</div>
		</div>
		<div style="clear:both"></div>
		<br />
		<?php
}


function bmo_tomas_bbpress_only_for_members()
{
	global  $user_ID, $post;
	
	if (is_front_page()) return;
	$current_page_id = get_the_ID();
	
	if (function_exists('bp_is_register_page') && function_exists('bp_is_activation_page') )
	{
		if ( bp_is_register_page() || bp_is_activation_page() )
		{
			return;
		}
	}

	if (function_exists('bbp_is_forum_archive'))
	{
		$bbp_is_forum_archive = bbp_is_forum_archive();
		if($bbp_is_forum_archive)
		{
			return;
		}
	}

	$current_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$current_url = str_ireplace('http://','',$current_url);
	$current_url = str_ireplace('https://','',$current_url);
	$current_url = str_ireplace('ws://','',$current_url);
	$current_url = str_ireplace('www.','',$current_url);
	$saved_register_page_url = get_option('bbpmoregisterpageurl');

	$saved_register_page_url = str_ireplace('http://','',$saved_register_page_url);
	$saved_register_page_url = str_ireplace('https://','',$saved_register_page_url);
	$saved_register_page_url = str_ireplace('ws://','',$saved_register_page_url);
	$saved_register_page_url = str_ireplace('www.','',$saved_register_page_url);

	if (function_exists('is_bbpress'))
	{
		if (is_bbpress())
		{
			$is_bbp_current_forum = bbp_get_forum_id();  
		}
		else
		{
			$is_bbp_current_forum = '';
		}
	}
	else 
	{
		$is_bbp_current_forum = '';
	}
	
	if (function_exists('is_bbpress'))
	{
		$bbprestrictsbbpresssection = get_option('bbprestrictsbbpresssection');
		if (!(empty($bbprestrictsbbpresssection)))
		{
			if (is_bbpress())
			{
		
			}
			else
			{
				return;
			}
		}
	}
	
	if (stripos($saved_register_page_url,$current_url) === false)
	{

	}
	else 
	{
		return;
	}
	
	$saved_open_page_option = get_option('bbp_members_only_saved_open_page_url');

	$bbp_members_only_saved_open_page_url = explode("\n", trim($saved_open_page_option));

	if ((is_array($bbp_members_only_saved_open_page_url)) && (count($bbp_members_only_saved_open_page_url) > 0))
	{
		$root_domain = get_option('siteurl');
		foreach ($bbp_members_only_saved_open_page_url as $bbp_members_only_saved_open_page_url_single)
		{
			$bbp_members_only_saved_open_page_url_single = trim($bbp_members_only_saved_open_page_url_single); 

			if (bmo_tomas_bbp_members_only_reserved_url($bbp_members_only_saved_open_page_url_single) == true)
			{
				continue;
			}
			
			$bbp_members_only_saved_open_page_url_single = bmo_tomas_bbp_members_only_pure_url($bbp_members_only_saved_open_page_url_single);
			
			if (stripos($current_url,$bbp_members_only_saved_open_page_url_single) === false)
			{

			}
			else 
			{
				return;
			}
		}
	}

	if ( is_user_logged_in() == false )
	{
		if (empty($saved_register_page_url))
		{
			$current_url = $_SERVER['REQUEST_URI'];
			$redirect_url = wp_login_url( );
			header( 'Location: ' . $redirect_url );
			die();			
		}
		else 
		{
			$saved_register_page_url = 'http://'.$saved_register_page_url;
			header( 'Location: ' . $saved_register_page_url );
			die();
		}
	}
}

function bmo_tomas_bbp_members_only_pure_url($current_url)
{
	if (empty($current_url)) return false;
	$current_url_array = parse_url($current_url);

	$current_url = str_ireplace('http://','',$current_url);
	$current_url = str_ireplace('https://','',$current_url);
	$current_url = str_ireplace('ws://','',$current_url);
		
	$current_url = str_ireplace('www.','',$current_url);
	$current_url = trim($current_url);
	return $current_url;
}

function bmo_tomas_bbp_members_only_reserved_url($url)
{
	$home_page = get_option('siteurl');
	$home_page = bmo_tomas_bbp_members_only_pure_url($home_page);
	$url = bmo_tomas_bbp_members_only_pure_url($url);
	if ($home_page == $url)
	{
		return true;
	}
	else
	{
		return false;
	}
} 
add_action('wp','bmo_tomas_bbpress_only_for_members');


function bmo_tomas_bbp_members_only_message($p_message)
{

	echo "<div id='message' class='updated fade' style='line-height: 30px;margin-left: 0px;margin-top:10px; margin-bottom:10px;'>";

	echo $p_message;

	echo "</div>";

}

function tomas_bbpress_members_only_admin_sidebar_about()
{
	?>

					<div id="post-body"  style="width:38%; float:right;">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:90%;">

								<div class="postbox">
									<h3 class='hndle' style='padding: 20px 0px; !important'>
									<span>
									<?php 
											echo  __( 'bbPress Members Only Pro Features', 'bbp-members-only' );
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:10px;'>
									<div class="inside">
									<ul>
										<li>
										* <a class="" target="_blank" href="https://www.bbp.design/features-of-bbpress-members-only-pro-plugin/">Login redirect based on user roles, each user roles have options for redirect to the smae page before login, referrers … and so on</a>
										</li>
										<li>
										* <a class="" target="_blank" href="https://www.bbp.design/features-of-bbpress-members-only-pro-plugin/">Restricts your bbPress forums based on user roles</a>
										</li>
										<li>
										* <a class="" target="_blank" href="https://www.bbp.design/features-of-bbpress-members-only-pro-plugin/">Restricts your bbPress topics based on user roles</a>
										</li>																				
										<li>
										* <a class="" target="_blank" href="https://www.bbp.design/features-of-bbpress-members-only-pro-plugin/">Restricts your bbPress replies based on user roles</a>
										</li>
										<li>
										* <a class="" target="_blank" href="https://www.bbp.design/features-of-bbpress-members-only-pro-plugin/">Options to enable / disable restriction of your bbPress Topics, bbPress Replies, WordPress Pages / Posts</a>
										</li>
										<li>
										* <a class="" target="_blank" href="https://www.bbp.design/features-of-bbpress-members-only-pro-plugin/">In bbPress Topic editor, bbPress reply editor, post / page editor, you can choose setting it as a members only page based on user roles</a>
										</li>																				
										<li>
										* <a class="" target="_blank" href="https://www.bbp.design/features-of-bbpress-members-only-pro-plugin/">Restricts your bbPress forums to Logged in/Registered members only, you can choose which sub forum will open to guest user, or which sub forum will only opened to logged in users</a>
										</li>
										<li>
										* <a class="" target="_blank" href="https://www.bbp.design/features-of-bbpress-members-only-pro-plugin/">Restricts your bbPress topics to Logged in/Registered members only</a>
										</li>
										<li>
										* <a class="" target="_blank" href="https://www.bbp.design/features-of-bbpress-members-only-pro-plugin/">Restricts your bbPress replies to Logged in/Registered members only</a>
										</li>
										<li>
										* <a class="" target="_blank" href="https://www.bbp.design/features-of-bbpress-members-only-pro-plugin/">Enable page level protect, when you edit a post, you can choose setting it as a members only page or not. By this way, you do not need enter page URLs to Opened Pages Panel always</a>
										</li>										
										<li>
										* <a class="" target="_blank" href="https://www.bbp.design/features-of-bbpress-members-only-pro-plugin/">Options to Only Protect My bbPress Pages, so other section on your wordpress site will be open to the guest users, so you can only restrict your bbPress section, but open wordpress section to your guests, for example, blog, faq, ticket, store… and so on. </a>
										</li>										
										<li>								
											* <a class=""  target="_blank" href="https://www.bbp.design/shop/">$12, Lifetime Upgrades, Unlimited Download, Ticket Support</a>
										</li>
									</ul>
									</div>									
									
									</div>
								</div>
								
								<div class="postbox">
									<h3 class='hndle' style='padding: 20px 0px; !important'>
									<span>
									<?php 
											echo  __( 'Other bbPress Plugins Maybe You Like', 'bbp-members-only' );
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:10px;'>
									<div class="inside">
										<ul>
										<li>
											* <a class="" target="_blank" href="https://www.bbp.design/features/">bbPress Login Register Pro Plugin</a></b>
											<p> Stop brute force attacks on your bbpress forums,  make your login / register pages more beautiful via preset preset wallpapers, login and logout auto redirect based on user roles, and blocks spam-bots to protect your login form / register form / bbpress new topic form / bbpress reply form, also our plugin will detect more than 20 proxy types and stop users login your site via these proxy types.</p>
										</li>
										<li>
											* <a class="" target="_blank" href="https://www.bbp.design/product/bbpress-new-user-approve/">bbPress New User Approve</a></b>
											<p> When users register as members, they need awaiting administrator approve their account manually, at the same time when unapproved users try to login your site, they can not login your site and they will get a message that noticed they have to waiting for admin approve their access first</p>
										</li>										
										<li>
											* <a class="" target="_blank" href="https://www.bbp.design/product/bbpress-most-liked-topics-plugin/">bbPress Most Liked Topics Plugin</a></b>
											<p> The plugin add a like button to bbPress topics and replies, bbPress forum members can like topics and replies, When users View forum topic, he will find most liked replies at the top of the topic page, show most valuable replies to users is a good way to let users like and join in your forum</p>
										</li>
										<li>
											* <a class="" target="_blank" href="https://www.bbp.design/product/bbpress-woocommerce-payment-gateway-plugin/">bbPress WooCommerce Payment Gateway Plugin</a></b>
											<p> A bbPress plugin to integrate WooCommerce Payment Gateway to help webmaster charge money from users of bbPress forums.</p>
										</li>
										<li>
											* <a class="" target="_blank" href="https://www.bbp.design/product/bbpress-blacklist-whitelist-security-plugin-product/">bbPress Blacklist Plugin</a></b>
											<p> A bbPress plugin which allow you build a blacklist to prevent spammers register as your users..</p>
										</li>
										<li>
											* <a class="" target="_blank" href="https://www.bbp.design/feature-of-bbpress-google-xml-sitemaps-generator-plugin/">bbPress Google XML Sitemaps Generator Plugin</a></b>
											<p> A bbPress plugin which build bbpress google XML sitemaps instantly to increase your SEO rank.</p>
										</li>										
										<li>
											* <a class="" target="_blank" href="https://www.bbp.design/features-of-bbpress-new-user-must-to-do-plugin/">bbPress New User Must To Do Plugin</a></b>
											<p> A bbPress plugin which force newbie in your forums to do something first, for example, introduce themselves, before they can post topic, reply topics on bbPress forum.</p>
										</li>										
										<li>
											* <a class="" target="_blank" href="https://tooltips.org/wordpress-tooltip-plugin/wordpress-tooltips-demo/">WordPress Tooltip with bbPress Tooltip Addon</a></b>
											<p> WordPress tooltip pro is a tooltips plugin for wordpress, which be designed to help you create colorful, varied and graceful tooltip styles to present the content to your users, with lively and elegant animation effects, and save your valuable screen space.

When the users hover over an item, the colorful tooltip popup box will display with the animation effect. You can add video, audio, image, and even other content which generated by 3rd wordpress plugins like QR code, Amazon AD, Google Map in tooltip popup box via wordpress standard editor, it is very easy to use.</p>
										</li>										
										</ul>
									</div>									
									</div>
								</div>
																
								
								<div class="postbox">
									<h3 class='hndle' style='padding: 20px 0px; !important'>
									<span>
									<?php 
											echo  __( 'bbPress Wordpress Tips Feed:', 'bbp-members-only' );
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:10px;'>
						<?php 
							wp_widget_rss_output('https://tomas.zhu.bz/feed/', array(
							'items' => 3, 
							'show_summary' => 0, 
							'show_author' => 0, 
							'show_date' => 1)
							);
						?>
										<br />
									</div>
								</div>
							</div>
						</div>
											
					</div>
<?php
}


