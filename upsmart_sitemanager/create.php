<?php
	/**
	  * @package UpSmart_SiteManager
	  */

	function upsmart_require_login() {
		if(!get_current_user_id()) {
			wp_redirect(home_url('create/1'));
			exit();
		}
	}
	
	
	function upsmart_get_editor($content,$id='content',$settings=array()) {
		ob_start();
		wp_editor($content,$id,$settings);
		return ob_get_clean();
	}
	
	function upsmart_page_create_nav($n,$sn=null) {
		$base = home_url('create/');
		$out = "<ol class='steps'>
			<li".($n==1?" class='active'":"").">1. Sign In</li>
			<li".($n==2?" class='active'":"")."><a href='{$base}2'>2. Client Info</a></li>
			<li".($n==3?" class='active'":"")."><a href='{$base}3'>3. Business Info</a></li>
			<li".($n==4?" class='active'":"")."><a href='{$base}7'>4. Technical Info</a></li>
		</ol><br style='clear: both'/>";
		
		if($sn != null) {
			$out .= "<ol class='steps substeps'>
				<li".($sn==1?" class='active'":"")."><a href='{$base}3'>a. Basic Info</a></li>
				<li".($sn==2?" class='active'":"")."><a href='{$base}4'>b. The Team</a></li>
				<li".($sn==3?" class='active'":"")."><a href='{$base}5'>c. About the Company</a></li>
				<li".($sn==4?" class='active'":"")."><a href='{$base}6'>d. Products</a></li>
			</ol><br style='clear: both'/>";
		}
		
		return $out;
	}
	  
	function upsmart_page_create($post) {
		$post->post_title = 'Hosting Signup';
		switch(upsmart_handle_page_info) {
			default:
			case '/1': $post = upsmart_page_create_home($post); break;
			case '/2': $post = upsmart_page_create_clientinfo($post); break;
			case '/3': $post = upsmart_page_create_business_basic($post); break;
			case '/4': $post = upsmart_page_create_business_people($post); break;
			case '/5': $post = upsmart_page_create_business_desc($post); break;
			case '/6': $post = upsmart_page_create_products($post); break;
			case '/7': $post = upsmart_page_create_technical($post); break;
			case '/8': $post = upsmart_page_create_done($post); break;
			case '/1111': $post = upsmart_page_create_clean($post); break;
			case '/2222': $post = upsmart_page_create_delete($post); break;
		}
		return $post;
	}

	function upsmart_page_create_home($post) {
		$post->post_content = upsmart_page_create_nav(1).'<h2>Login</h2>';
		
		if(get_current_user_id()) {
			wp_redirect(home_url('create/2'));
			exit();
		} else {
			$login_form = wp_login_form(array('echo' => false));
			$reg_link = wp_register('','',false);
			$post->post_content .= <<<EOHTML
				<div id='twocol'>
					<div>
					<h3>Sign In</h3>
					Already have an account? Sign in below:
					$login_form
					</div>
					<div>
					<h3>Sign Up</h3>
					Need to make an account? It'll only take a moment.
					$reg_link
EOHTML;
		}
		
		return $post;
	}
	
	function upsmart_page_create_clientinfo($post) {
		global $wpdb,$user_email;
		
		upsmart_require_login();
		
		$post->post_content = upsmart_page_create_nav(2).'<h2>Client Information</h2>';
		
		if(!empty($_POST)) {
			$result = upsmart_create_contact_save();
			if($result === false) wp_die("An error has occurred.");
			wp_redirect(home_url('create/3'));
 			exit();
		}
		
		get_currentuserinfo();
		
		$post->post_content .= <<<EOHTML
		We'd like to know more about you and your company. Just fill out the fields below (everything is required).
		<h3>Identification</h3>
EOHTML;
		$post->post_content .= upsmart_create_contact_form();
		
		return $post;
	}
	
	function upsmart_page_create_business_basic($post) {
		global $wpdb;
		
		upsmart_require_login();
		
		$post->post_content = upsmart_page_create_nav(3,1).'<h2>Business Information</h2>';
		
		if(!empty($_POST)) {
			$result = upsmart_create_business_save();
			if($result === false) wp_die("An error has occurred.");
			wp_redirect(home_url('create/4'));
 			exit();
		}
		
		$post->post_content .= "We'd like to know more about you and your company. Just fill out the fields below (everything is required).";
		$post->post_content .= upsmart_create_business_form();
		
		return $post;
	}
	
	function upsmart_page_create_business_people($post) {
		global $wpdb;
		
		upsmart_require_login();
		
		$post->post_content = upsmart_page_create_nav(3,2).'<h2>Business Information</h2>';
		
		if(!empty($_POST)) {
			$result = upsmart_create_people_save();
			if($result === false) wp_die("An error has occurred.");
			
			wp_redirect(home_url('create/5'));
 			exit();
		}

		upsmart_create_people_scripts();
		
		$post->post_content .= <<<EOHTML
		We'd like to know more about you and your company. Just fill out the fields below (everything is required).
		
		<h3>The Team</h3>
		Introducing the people behind the idea is important. Feel free to send in snapshots of each person. Putting a face to the product is surprisingly good at making an idea "come to life" in the minds of others.
EOHTML;
		$post->post_content .= upsmart_create_people_form();
		
		return $post;
	}
	
	function upsmart_page_create_business_desc($post) {
		global $wpdb;
		
		upsmart_require_login();
		
		$post->post_content = upsmart_page_create_nav(3,3).'<h2>Business Information</h2>';
		
		if(!empty($_POST)) {
			$result = upsmart_create_profile_save();
			if($result === false) wp_die("An error has occurred.");
			wp_redirect(home_url('create/6'));
 			exit();
		}
		
		upsmart_create_profile_scripts();
		
		$post->post_content .= <<<EOHTML
		We'd like to know more about you and your company. Just fill out the fields below (everything is required).
		<h3>About the Company</h3>
EOHTML;
		$post->post_content .= upsmart_create_profile_form();
		return $post;
	}
	
	function upsmart_page_create_products($post) {
		global $wpdb;
		
		upsmart_require_login();
		
		$post->post_content = upsmart_page_create_nav(3,4).'<h2>Business Information</h2>';
		
		if(!empty($_POST)) {
			$result = upsmart_create_products_save();
			if($result === false) wp_die("An error has occurred.");
			wp_redirect(home_url('create/7'));
 			exit();
		}
		
		//Load the javascript
		upsmart_create_products_scripts();
		
		$post->post_content .= <<<EOHTML
		We'd like to know more about you and your company. Just fill out the fields below (everything is required).
		<h3>Products</h3>
		Now it's time to learn more about your company's product(s). Click on the plus button below to add one.
EOHTML;
		
		$post->post_content .= upsmart_create_products_form();
		
		return $post;
	}
	
	function upsmart_page_create_technical($post) {
		global $wpdb;
		
		upsmart_require_login();
		
		$post->post_content = upsmart_page_create_nav(4).'<h2>Tech Stuff</h2>';
		
		if($wpdb->get_var("SELECT count(*) FROM upsmart_business WHERE site_id IS NOT NULL AND wordpress_id=".get_current_user_id())) {
 			wp_redirect(home_url('create/8'));
 			exit();
 		}
		
		if(!empty($_POST)) {
			/*Fetch all of their data into a nice array.*/
			$company = upsmart_create_get();
			
			if($_POST['use_domain'] == 'yes') {
				if(strpos($_POST['domain'], '://') !== false) $_POST['domain'] = substr($_POST['domain'],strpos($_POST['domain'], '://')+3);
				if(substr($_POST['domain'],0,4) == 'www.') $_POST['domain'] = substr($_POST['domain'],4);
				
				if(!preg_match('#^[a-z0-9\-]+\.[a-z0-9\-\.]{2,8}$#i',$_POST['domain'])) wp_die("Invalid domain name \"".htmlentities($_POST['domain']).".\"");
			}
			
			$company['site'] = array(
				'theme' => $_POST['theme'],
				'domain' => ($_POST['use_domain'] == 'yes')?$_POST['domain']:($_POST['subdomain'].'.upsmart.com'),
			);
			
			/*Create their site here*/
				//http://phpxref.ftwr.co.uk/wordpress/nav.html?wp-includes/ms-functions.php.html
				if(get_blog_id_from_url($company['site']['domain'],'/') != 0) {
					wp_die("That domain is already in use.");
				}
				
				require_once(ABSPATH . 'wp-admin/includes/media.php');
				require_once(ABSPATH . 'wp-admin/includes/file.php');
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				
				//Site options go here.
				$meta = array(
					'template' => $company['site']['theme'],
					'stylesheet' => $company['site']['theme'],
					
					'blogdescription' => $company['business']['slogan'],
					
					'home' => 'http://'.$company['site']['domain'].'/',
					'siteurl' => 'http://'.$company['site']['domain'].'/',
					'show_on_front' => 'page',
				);
				$id = wpmu_create_blog($company['site']['domain'],'/',$company['business']['name'],get_current_user_id(),$meta);
				$wpdb->query($wpdb->prepare("UPDATE upsmart_business SET site_id=%d,url=%s WHERE wordpress_id=%d",$id,"http://{$company['site']['domain']}/",get_current_user_id()));
				
				//Contact form setup.
				//This sets the settings for the "Contact Us Form" plugin (http://www.kenmoredesign.com/)
				update_option('contact_us_form',array (
					'to_email' => $company['contact']['email'],
					'from_email' => '',
					'css' => '',
					'msg_ok' => 'Thank you! Your message was sent successfully.',
					'msg_err' => 'Sorry. An error occured while sending the message!',
					'submit' => '',
					'captcha' => 0,
					'captcha_label' => '',
					'captcha2' => 0,
					'captcha2_question' => '',
					'captcha2_answer' => '',
					'subpre' => '',
					'field_1' => '',
					'field_2' => '',
					'field_3' => '',
					'field_4' => '',
					'field_5' => '',
					'hideform' => 0,
				));
				
				
				//This appears necessary for wordpress to understand how to route requests.
				$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}site (id,domain,path) VALUES(%d,'%s','/')",$id,$company['site']['domain']));
				$wpdb->query($wpdb->prepare("INSERT INTO {$wpdb->prefix}domain_mapping (blog_id,domain,active) VALUES(%d,'%s',1)",$id,$company['site']['domain']));
				
				//BUDDYPRESS: SET UP GROUP
				$new_group = new BP_Groups_Group();
					$new_group->creator_id = get_current_user_id();
					$new_group->name = $company['business']['name'];
					$new_group->slug = strtolower(str_replace(" ","",$company['business']['name']));
					$new_group->description = '';
					$new_group->news = '';
					$new_group->status = 'public';
					$new_group->is_invitation_only = 0;
					$new_group->enable_wire = 1;
					$new_group->enable_forum = 1;
					$new_group->enable_photos = 1;
					$new_group->photos_admin_only = 1;
					$new_group->date_created = current_time('mysql');
					$new_group->total_member_count = 1;
					$new_group->avatar_thumb = $business['profile']['logo'];
					$new_group->avatar_full = $business['profile']['logo'];
			
				$new_group -> save(); //this does the database insert
				
				//BUDDYPRESS: SET UP EARLY ADOPTERS GROUP
				$new_group = new BP_Groups_Group();
					$new_group->creator_id = get_current_user_id();
					$new_group->name = $company['business']['name']." Early Adopters";
					$new_group->slug = strtolower(str_replace(" ","",$company['business']['name'])). "EA";
					$new_group->description = '';
					$new_group->news = '';
					$new_group->status = 'public';
					$new_group->is_invitation_only = 1;
					$new_group->enable_wire = 1;
					$new_group->enable_forum = 1;
					$new_group->enable_photos = 1;
					$new_group->photos_admin_only = 1;
					$new_group->date_created = current_time('mysql');
					$new_group->total_member_count = 1;
					$new_group->avatar_thumb = $business['profile']['logo'];
					$new_group->avatar_full = $business['profile']['logo'];
			
				$new_group -> save(); //this does the database insert
				
				//CODE HERE TO SET UP BIND TO SERVE THE SITE.
				if($_POST['use_domain'] == 'yes') {
					//Create the zone file.
					$zone = file_get_contents(plugin_dir_path(__FILE__).'bind/template.hosts');
					$zone = str_replace('$$DOMAIN$$',$company['site']['domain'],$zone);
					file_put_contents(plugin_dir_path(__FILE__).'bind/'.$company['site']['domain'].'.hosts',$zone);
					
					$named_conf = file_get_contents(plugin_dir_path(__FILE__).'bind/named.conf.template');
					$named_conf = str_replace('$$DOMAIN$$',$company['site']['domain'],$named_conf);
					$f = fopen(plugin_dir_path(__FILE__).'bind/named.conf.upsmart','a');
					fwrite($f,$named_conf);
					fclose($f);
					
					shell_exec('/usr/sbin/rndc -k '.plugin_dir_path(__FILE__).'bind/rndc.key reload');
				}
				
				//Switch blogs so everything we do is in the context of the new site.
				switch_to_blog($id);
				
				wp_delete_post(1); //"Hello World" blog post
				wp_delete_post(2); //"Sample Page" page.
				
				//Figure out what templates to use.
				if(file_exists(plugin_dir_path(__FILE__).'templates/'.$company['site']['theme'])) {
					$template_set = $company['site']['theme'];
					$template_dir = plugin_dir_path(__FILE__).'templates/'.$company['site']['theme'];
				} else {
					$template_set = 'default';
					$template_dir = plugin_dir_path(__FILE__).'templates/default';
				}
				
				//Evaluate all of the templates.
				$templates = glob($template_dir.'/*.php');
				foreach($templates as $file) {
					$file = substr(basename($file),0,-4);
					upsmart_create_generate_page($id,$company,$file,$template_set);
				}
				
				if(!empty($company['profile']['logo'])) media_sideload_image($company['profile']['logo'],null,"Company logo");
				if(!empty($company['profile']['media1'])) media_sideload_image($company['profile']['media1'],null);
				if(!empty($company['profile']['media2'])) media_sideload_image($company['profile']['media2'],null);
				foreach($company['people'] as $p) {
					media_sideload_image($p['photo'],3);
				}
				foreach($company['products'] as $p) {
					media_sideload_image($p['photo'],3);
				}
			
			//Done? Send them to the "YOU'RE DONE" page.
			switch_to_blog(1);
			wp_redirect(home_url('create/8'));
		}
		
		require 'wp-admin/includes/theme.php';
		
		$themes = "";
 		$theme_list = wp_get_themes(array('allowed'=>'network'));
 		foreach($theme_list as $n => $t) {
			if(file_exists('wp-content/themes/'.$n.'/screenshot.png')) $ext = 'png';
			else $ext = 'jpg';
			
			$themes .= "<div class='theme' data-tags='".implode(' ',$t->Tags)."'><img src='".content_url('themes/'.$n.'/screenshot.'.$ext)."' style='width: 150px'/><br/><input type='radio' name='theme' value='$n'/> {$t->Name}</div>";
 		}
 		
 		$feature_list = get_theme_feature_list();
		
		$feature_list_output = '';
		foreach($feature_list as $feature_name => $features) {
			$feature_name = esc_html( $feature_name );
			
			$feature_list_output .= "<div class='feature-container'><div class='feature-name'>{$feature_name}</div>";
			$feature_list_output .= "<ol class='feature-group'>";
			
			foreach ( $features as $key => $feature ) {
				$feature_name = $feature;
				$feature_name = esc_html( $feature_name );
				$feature = esc_attr( $feature );
				$feature_list_output .= "<li><input type='checkbox' name='features[]' id='feature-id-$key' value='$key' /><label for='feature-id-{$key}'>{$feature_name}</label></li>";
			}
			
			$feature_list_output .= "</ol></div>";
		}
		
		$post->post_content .= <<<EOHTML
		You're almost there! Just a few technical questions and we'll get you set up.
		<form method='post'>
			<table>
				<tbody id='hosting'>
				<tr><th colspan='2'><h4>Hosting Info</h4></th></tr>
				<tr><td>URL Choice</td>
				<td>
					<input type='radio' name='use_domain' style='display: inline' value='yes'/> Use my own domain name: <input style='width: auto' name='domain' placeholder='example.com'/><br/>
					<input type='radio' name='use_domain' style='display: inline' value='no'/> Use a sub-domain: <input style='width: auto' name='subdomain' placeholder='example'/>.upsmart.com
				</td>
				</tbody>
				<tbody id="theme">
				<tr><th colspan='2'><h4>Theme</h4></th></tr>
				<tr><td colspan='2'>
					<div id="feature-list">
					<a id='filter-link' href='javascript:void(0);'>Filter Themes</a>
					{$feature_list_output}
					</div>
					{$themes}<br style='clear: both'/>
				</td></tr>
				<tr><th colspan='2'><input type='submit' value='Save and Create'/></th></tr>
				</tbody>
			</table>
		</form>
		<script type="text/javascript">
			(function($) {
				function refilter() {
					$(".theme").show();
					
					var tags = '';
					$(".feature-container input:checked").each(function() {
						//tags.push($(this).attr("value"));
						tags = tags+"[data-tags~="+$(this).attr("value")+"]";
					});
					$(".theme:not("+tags+")").hide();
				}
			
				$(document).ready(function() {
					$("#filter-link").click(function() {
						$("#feature-list").toggleClass("expanded");
					});
					$(".feature-container input").change(refilter);
					
					$(".theme").click(function() {
						$(this).children("input").prop("checked",true);
					});
					
					refilter();
					if($(".feature-container input:checked").length > 0) {
						$("#feature-list").addClass("expanded");
					}
				});
			})(jQuery);
		</script>
EOHTML;
		
		return $post;
	}
	
	function upsmart_page_create_done($post) {
		global $wpdb;
		
		upsmart_require_login();
		
		$lol = $wpdb->get_var("SELECT url FROM upsmart_business WHERE wordpress_id=".get_current_user_id());
		
		$post->post_content = upsmart_page_create_nav(4).'<h2>Done!</h2>';
		$post->post_content .= <<<EOHTML
		
		<p><h4>Your site has been created. <a href='{$lol}'>Click here</a> to check it out.</h4><br/>
		<strong>Know a fellow entrepreneur who could use a free site? <br/>Click below to share this service!</strong><br/></p> 
		<div>
		<a href="mailto:?subject=Free web tools for Entrepreneurs&amp;body=Hey! Thought you might find this useful.  UpSmart provides startups with free, guided web design and free hosting.  Check it out: [ga-share-url]" class="upsmart-share-tool" title="Share by Email"><img class="upsmart-share-tool" src="http://png-2.findicons.com/files/icons/573/must_have/48/mail.png"/></a>
		<a href="https://twitter.com/share" class="twitter-share-button upsmart-share-tool" data-url="[ga-share-url]" data-text="#UpSmart provides #entrepreneurs guided web design and hosting...for free!" data-size="large" data-count="none">Tweet</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</div>
		<div class="fb-send" data-href="[ga-share-url]" data-font="arial"></div>
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=408066509285816";
		  fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));</script>
		<br/><strong>Spread the word!</strong>
EOHTML;
		
		return $post;
	}

	function upsmart_page_create_clean($post) {
		global $wpdb;
		
		require_once './wp-admin/includes/ms.php';
		
		$business = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_business WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		
		if($business['site_id'] != 0) wpmu_delete_blog($business['site_id'],true);
		
		$wpdb->query("DELETE FROM upsmart_business WHERE wordpress_id=".get_current_user_id());
		$wpdb->query("DELETE FROM upsmart_contact WHERE wordpress_id=".get_current_user_id());
		$wpdb->query("DELETE FROM upsmart_people WHERE wordpress_id=".get_current_user_id());
		$wpdb->query("DELETE FROM upsmart_products WHERE wordpress_id=".get_current_user_id());
		$wpdb->query("DELETE FROM upsmart_profile WHERE wordpress_id=".get_current_user_id());
		
		wp_redirect(home_url('create/1'));
		return $post;
	}
	
	function upsmart_page_create_delete($post) {
		global $wpdb;
		
		require_once './wp-admin/includes/ms.php';
		
		$business = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_business WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		
		if($business['site_id'] != 0) wpmu_delete_blog($business['site_id'],true);
		
		wp_redirect(home_url('create/7'));
		return $post;
	}
