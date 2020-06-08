<?php
if(!defined('WPINC'))
{
	exit ('Please do not access our files directly.');
}

function bbp_members_only_free_optional_setting()
{
	global $wpdb;

	if (isset($_POST['bbpoptionsettinspanelsubmit']))
	{
		check_admin_referer( 'bbpoptionsettinspanelsubmit_free_nonce' );
		if (isset($_POST['bbprestrictsbbpresssection']))
		{
			$m_bbprestrictsbbpresssection = sanitize_text_field($_POST['bbprestrictsbbpresssection']);
			update_option('bbprestrictsbbpresssection',$m_bbprestrictsbbpresssection);
		}
		else
		{
			delete_option('bbprestrictsbbpresssection');
		}

		if (isset($_POST['bbpdisableallfeature']))
		{
			$bbpdisableallfeature = sanitize_text_field($_POST['bbpdisableallfeature']);
			update_option('bbpdisableallfeature',$bbpdisableallfeature);
		}
		else
		{
			delete_option('bbpdisableallfeature');
		}
		
		$bbpdisableallfeature = get_option('bbpdisableallfeature');
		
		$bpmoMessageString =  __( 'Your changes has been saved.', 'bbp-members-only' );
		bmo_tomas_bbp_members_only_message($bpmoMessageString);
	}
	echo "<br />";
	?>

<div style='margin:10px 5px;'>
<div style='float:left;margin-right:10px;'>
<?php
/*
<img src='<?php echo get_option('siteurl');  ?>/wp-content/plugins/bbp-members-only-pro/images/new.png' style='width:30px;height:30px;'>
*/
?>
<img src='<?php echo BBP_MEMBERSONLY_PLUGIN_URL;  ?>/images/new.png' style='width:30px;height:30px;'>
</div> 
<div style='padding-top:5px; font-size:22px;'>bbPress Members Only Optional Settings:</div>
</div>
<div style='clear:both'></div>		
		<div class="wrap">
			<div id="dashboard-widgets-wrap">
			    <div id="dashboard-widgets" class="metabox-holder">
					<div id="post-body"  style="width:60%;">
						<div id="dashboard-widgets-main-content">
							<div class="postbox-container" style="width:90%;">
								<div class="postbox">
									<h3 class='hndle' style='padding: 20px; !important'>
									<span>
									<?php 
											echo  __( 'Optional Settings Panel :', 'bbp-members-only' );
									?>
									</span>
									</h3>
								
									<div class="inside" style='padding-left:10px;'>
										<form id="bpmoform" name="bpmoform" action="" method="POST">
										<table id="bpmotable" width="100%">

										
										<tr>
										<td width="30%" style="padding: 30px 20px 20px 20px; " valign="top">
										<?php 
											echo  __( 'Only Protect My  bbPress Pages:', 'bbp-members-only' );
										?>
										</td>
										<td width="70%" style="padding: 20px;">
										<p>
										<?php
										$bbprestrictsbbpresssection = get_option('bbprestrictsbbpresssection');
										if (!(empty($bbprestrictsbbpresssection)))
										{
											echo '<input type="checkbox" id="bbprestrictsbbpresssection" name="bbprestrictsbbpresssection"  style="" value="yes"  checked="checked"> All Other Sections On Your Site Will Be Opened to Guest ';
 
										}
										else 
										{
											echo '<input type="checkbox" id="bbprestrictsbbpresssection" name="bbprestrictsbbpresssection"  style="" value="yes" > All Other Sections On Your Site Will Be Opened to Guest ';
										}
										?>
										</p>
										<p><font color="Gray"><i>
										<?php 
										echo  __( '# If you enabled this option, "opened Page URLs" setting in ', 'bbp-members-only') ;
										echo "<a  style='color:#4e8c9e;' href='".get_option('siteurl')."/wp-admin/admin.php?page=bbpmemberonlyfree' target='_blank'>Opened Pages Panel</a>";
										echo  __(' will be ignored', 'bbp-members-only' ); 
										?></i></p>
										</td>
										</tr>								
										
										<tr>
										<td width="30%" style="padding: 30px 20px 20px 20px; " valign="top">
										<?php 
											echo  __( 'Temporarily Turn Off All Featrures:', 'bbp-members-only' );
										?>
										</td>
										<td width="70%" style="padding: 20px;">
										<p>
										<?php
										$bbpdisableallfeature = get_option('bbpdisableallfeature');
										if (!(empty($bbpdisableallfeature)))
										{
											echo '<input type="checkbox" id="bbpdisableallfeature" name="bbpdisableallfeature"  style="" value="yes"  checked="checked"> Temporarily Turn Off All Featrures Of bbPress Members Only ';
 
										}
										else 
										{
											echo '<input type="checkbox" id="bbpdisableallfeature" name="bbpdisableallfeature"  style="" value="yes" > Temporarily Turn Off All Featrures Of bbPress Members Only ';
										}
										?>
										</p>
										<p><font color="Gray"><i>
										<?php 
										echo  __( '# If you enabled this option, all features of bbpress members only will be disabled, you site will open to all users', 'bbp-members-only') ;
										?></i></p>
										</td>
										</tr>
																				
										</table>
										<br />
										<?php
										//!!!
										wp_nonce_field('bbpoptionsettinspanelsubmit_free_nonce');
										?>
										<input type="submit" id="bbpoptionsettinspanelsubmit" name="bbpoptionsettinspanelsubmit" value=" Submit " style="margin:1px 20px;">
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


