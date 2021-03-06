<?php
	//TODO: Instead of get_current_user_id(), pass in an ID, so other users can be edited.
	function upsmart_create_contact_form() {
		global $wpdb;
		
		$data = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_contact WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		$data['phone'] = explode('-',$data['phone']);
		
		return <<<EOHTML
			<form method='post'>
			<table>
				<tr><th colspan='2'><h4>Contact</h4></th></tr>
				<tr><th>Name</th><td class='twoinput'><input name='contact_firstname' value='{$data['firstname']}' placeholder='First'/><input name='contact_lastname' value='{$data['lastname']}' placeholder='Last'/></td></tr>
				<tr><th>Email</th><td><input name='contact_email' type='email' value='{$data['email']}' placeholder='Email'/></td></tr>
				<tr><th>Phone Number</th>
					<td class='normalinput'>
						(<input name='contact_phone1' type='number' size='3' value='{$data['phone'][0]}' placeholder='000'/>) -
						<input name='contact_phone2' type='number' size='3' value='{$data['phone'][1]}' placeholder='000'/> -
						<input name='contact_phone3' type='number' size='4' value='{$data['phone'][2]}' placeholder='0000'/>
					</td>
				</tr>
				<tr><th>Address</th>
					<td class='normalinput'>
						<input name='contact_street' placeholder='Street/PO Box' value='{$data['street']}' style='width: 100%'/><br/>
						<input name='contact_city' value='{$data['city']}' placeholder='City'/> <input name='contact_state' value='{$data['state']}' placeholder='State'/><br/>
						<input name='contact_zip' size='5' value='{$data['zip']}' placeholder='Zip Code'/><br/>
					</td>
				</tr>
				<tr><th colspan='2'><input type='submit' value='Save'/></th></tr>
			</table>
			</form>
EOHTML;
	}
	function upsmart_create_contact_save() {
		global $wpdb;
		$result = $wpdb->query($wpdb->prepare("REPLACE INTO upsmart_contact
		                                      (wordpress_id,firstname,lastname,email,phone,street,city,state,zip)
		                                      VALUES(%d,%s,%s,%s,%s,%s,%s,%s,%s)",
		                                      array(
		                                          get_current_user_id(),
		                                          $_POST['contact_firstname'],
		                                          $_POST['contact_lastname'],
		                                          $_POST['contact_email'],
		                                          $_POST['contact_phone1'].'-'.$_POST['contact_phone2'].'-'.$_POST['contact_phone3'],
		                                          $_POST['contact_street'],
		                                          $_POST['contact_city'],
		                                          $_POST['contact_state'],
		                                          $_POST['contact_zip'],
		                                      )
		));
		
		if($result === false) return false;
		return true;
	}
	
	
	function upsmart_create_business_form() {
		global $wpdb;
		$data = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_business WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		
		$inc = array(
			'yes' => ($data['incorporated']==1)?'checked="checked"':'',
			'no' => ($data['incorporated']!=1)?'checked="checked"':'',
		);
		
		$site = array(
			'yes' => ($data['has']==1)?'checked="checked"':'',
			'no' => ($data['incorporated']!=1)?'checked="checked"':'',
		);
		
		return <<<EOHTML
			<form method='post'>
			<h3>Project</h3>
			<table>
				<tr><th>Company/Project Name</th><td><input name='project_name' value='{$data['name']}' placeholder='Company/Project Name'/></td></tr>
				<tr><th>Company/Project Slogan</th><td><input name='project_slogan' value='{$data['slogan']}' placeholder='We do awesome stuff.'/></td></tr>
				<tr><td>Has your company been incorporated?</td>
					<td>
						<input type='radio' name='project_incorporated' {$inc['yes']} value='yes'/> Yes
						&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' name='project_incorporated' {$inc['no']} value='no'/> No
					</td>
				</tr>
				<tr><th>Web Site URL</th><td><input name='project_site' value='{$data['url']}' placeholder='http://www.example.com/'/></td></tr>
				<tr><th colspan='2'><input type='submit' value='Save'/></th></tr>
			</table>
			</form>
EOHTML;
	}
	function upsmart_create_business_save() {
		global $wpdb;
		$result = $wpdb->query($wpdb->prepare("REPLACE INTO upsmart_business
		                                      (wordpress_id,name,slogan,incorporated,url)
		                                      VALUES(%d,%s,%s,%d,%s)",
		                                      array(
		                                          get_current_user_id(),
		                                          $_POST['project_name'],
		                                          $_POST['project_slogan'],
		                                          ($_POST['project_incorporated']=='yes')?1:0,
		                                          ($_POST['project_hassite']=='yes')?$_POST['project_site']:''
		                                      )
		));
		
		if($result === false) return false;
		return true;
	}
	
	
	function upsmart_create_profile_scripts() {
		//Scripts/CSS needed for media uploader.
		wp_enqueue_script(array('jquery', 'thickbox', 'media-upload'));
		wp_enqueue_style('thickbox');
		$media_url = admin_url("media-upload.php?type=image&amp;TB_iframe=true");
	}
	function upsmart_create_profile_form() {
		global $wpdb;
		$data = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_profile WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		
		$media_url = admin_url("media-upload.php?type=image&amp;TB_iframe=true");
		
		$out = <<<EOHTML
		<form method='post' enctype='multipart/form-data'>
	<h4>Branding</h4>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery("#logo_upload").click(function() {
				tb_show("", "{$media_url}");
				window.send_to_editor = function(html) {
					imgurl = jQuery("img",html).attr("src");
					jQuery("#logo_field").val(imgurl);
					tb_remove();
				}
				return false;
			});
		
			jQuery("#media1_upload").click(function() {
				tb_show("", "{$media_url}");
				window.send_to_editor = function(html) {
					imgurl = jQuery("img",html).attr("src");
					jQuery("#media1_field").val(imgurl);
					tb_remove();
				}
				return false;
			});
			
			jQuery("#media2_upload").click(function() {
				tb_show("", "{$media_url}");
				window.send_to_editor = function(html) {
					imgurl = jQuery("img",html).attr("src");
					jQuery("#media2_field").val(imgurl);
					tb_remove();
				}
				return false;
			});
		});
	</script>
	<table>
		<tr><th>Logo</th><td><input type='text' id="logo_field" value="{$data['logo']}" name='logo'/>
		<input type='button' id='logo_upload' value='Open Media Library'/></tr>
		<tr><td colspan='2'>The bigger the better&mdash;don't worry, we'll scale this down for you. This image should have a transparent background and be suitable for display on a colored background.</td></tr>
		
		<tr><th><br/>Media</th></tr>
		<tr><td colspan='2'>Now upload the "coolest" piece of media you have. This will be used on the top fold of your generated site to draw users in.
		<br/>
		<input type='text' id="media1_field" value="{$data['media1']}" name='media1'/>
		<input type='button' id='media1_upload' value='Open Media Library'/>
		<tr><td colspan='2'>While you're at it, give us the second coolest piece of media you have as well. Who knows when you might need it?
		<br/>
		<input type='text' id="media2_field" value="{$data['media2']}" name='media2'/>
		<input type='button' id='media2_upload' value='Open Media Library'/>
		</td></tr>
		<tr><td colspan='2'><br/>
		<ul><li>The bigger the better&mdash;don't worry, we'll scale these down for you.</li><li>The uploaded files should have solid backgrounds.</li></ul><br/></td></tr>
	</table>
	<h4>About</h4>
	<table>
		<tr><th colspan='2'>Mission Statement</th></tr>
		<tr><td colspan='2'>Your mission statement should include ...</td></tr>
		<tr><td colspan='2'>
EOHTML;
	$out .= upsmart_get_editor($data['mission'],'mission');
	$out .= <<<EOHTML
		</td></tr>
		
		<tr><th colspan='2'>"About Us"</th></tr>
		<tr><td colspan='2'>A good "About Us" writeup should answer the following questions: <ul><li>Who are you? (Think back to the people biographies you just submitted)</li><li>What are you doing?</li><li>Why is what you're doing awesome?</li></ul></td></tr>
		<tr><td colspan='2'>
EOHTML;
	$out .= upsmart_get_editor($data['about'],'about');
	$out .= <<<EOHTML
		</td></tr>
		
		<tr><th colspan='2'>Company History</th></tr>
		<tr><td colspan='2'>Let us know your background. Some things to think about: ...</td></tr>
		<tr><td colspan='2'>
EOHTML;
	$out .= upsmart_get_editor($data['history'],'history');
	$out .= <<<EOHTML
		</td></tr>
		
		<tr><th colspan='2'><input type='submit' value='Save'/></th></tr>
	</table>
			
		</table>
		</form>
EOHTML;
		return $out;
	}
	
	function upsmart_create_get() {
		global $wpdb;
		$company = array();
		$company['contact'] = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_contact WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		$company['profile'] = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_profile WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		$company['business'] = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_business WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		$company['people'] = $wpdb->get_results($wpdb->prepare("SELECT * FROM upsmart_people WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		$company['products'] = $wpdb->get_results($wpdb->prepare("SELECT * FROM upsmart_products WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		return $company;
	}
	
	function upsmart_create_profile_save() {
		global $wpdb;
		$result = $wpdb->query($wpdb->prepare("REPLACE INTO upsmart_profile
		                                      (wordpress_id,logo,media1,media2,mission,about,history)
		                                      VALUES(%d,%s,%s,%s,%s,%s,%s)",
		                                      array(
		                                          get_current_user_id(),
		                                          $_POST['logo'],
		                                          $_POST['media1'],
		                                          $_POST['media2'],
		                                          $_POST['mission'],
		                                          $_POST['about'],
		                                          $_POST['history'],
		                                      )
		));
		
		if($result === false) return false;
		return true;
	}
	
	function upsmart_create_people_scripts() {
		//Scripts/CSS needed for media uploader.
		wp_enqueue_script("create_business_people",plugins_url('js/create_business_people.js',__FILE__),array('jquery','jquery-ui-core','jquery-ui-dialog'));
		wp_enqueue_style("create_business_people",plugins_url('css/create_business_people.css',__FILE__));
		wp_enqueue_style('wp-jquery-ui-dialog');
		
		wp_enqueue_script(array('jquery', 'thickbox', 'media-upload'));
		wp_enqueue_style('thickbox');
	}
	function upsmart_create_people_form() {
		global $wpdb;
		
		$people = $wpdb->get_results($wpdb->prepare("SELECT * FROM upsmart_people WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		
		$out = '';
		
		if(count($people) > 0) {
			$out .= "<script type='text/javascript'>jQuery(document).ready(function() {\n";
			foreach($people as $i => $p) {
				$p['id'] = $i;
				unset($p['wordpress_id']);
				unset($p['person_id']);
				$out .= "upsmart.people.finishAddPerson(".json_encode($p).");\n";
			}
			$out .= "upsmart.people.pcounter = ".($i+1).";});</script>";
		}
		
		$media_url = admin_url("media-upload.php?type=image&amp;TB_iframe=true");
		$out .= <<<EOHTML
		<script type="text/javascript">
			media_input = "#photo_upload";
			function open_media_library() {
				tb_show("", "{$media_url}");
				window.send_to_editor = function(html) {
					imgurl = jQuery("img",html).attr("src");
					jQuery(media_input).val(imgurl);
					tb_remove();
				}
				return false;
			}
		</script>
		<style type="text/css">
			#TB_overlay {z-index: 2000 !important;}
			#TB_window {z-index: 2002 !important;}
		</style>
		<div id='team'>
		<div id='new'>+</div></div>
		<br/>
		<div id='dialog'></div>
		<form method='post' id="form">
			<input type='hidden' name='json'/>
			<input type='submit' value='Save'/>
		</form>
EOHTML;
		return $out;
	}
	function upsmart_create_people_save() {
		global $wpdb;
		
		$data = json_decode(stripslashes($_POST['json']));
		
		$wpdb->query($wpdb->prepare("DELETE FROM upsmart_people WHERE wordpress_id = %d",array(get_current_user_id())));
		
		foreach($data as $p) {
			if($p == null) continue;
			$result = $wpdb->query($wpdb->prepare("INSERT INTO upsmart_people
							(wordpress_id,fname,lname,title,bio,photo)
							VALUES(%d,%s,%s,%s,%s,%s)",
							array(
								get_current_user_id(),
								$p->fname,
								$p->lname,
								$p->title,
								$p->bio,
								$p->photo,
							)
			));
			
			if($result === false) return false;
		}
		
		return true;
	}
	
	function upsmart_create_products_scripts() {
		//Scripts/CSS needed for media uploader.
		wp_enqueue_script("create_business_products",plugins_url('js/create_business_products.js',__FILE__),array('jquery','jquery-ui-core','jquery-ui-dialog'));
		wp_enqueue_style("create",plugins_url('css/create.css',__FILE__));
		wp_enqueue_style('wp-jquery-ui-dialog');
		
		wp_enqueue_script(array('jquery', 'thickbox', 'media-upload'));
		wp_enqueue_style('thickbox');
	}
	function upsmart_create_products_form() {
		global $wpdb;
		
		$people = $wpdb->get_results($wpdb->prepare("SELECT * FROM upsmart_products WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		
		$out = '';
		
		if(count($people) > 0) {
			$out .= "<script type='text/javascript'>jQuery(document).ready(function() {\n";
			foreach($people as $i => $p) {
				$p['id'] = $i;
				unset($p['wordpress_id']);
				unset($p['product_id']);
				$out .= "upsmart.products.finishAddProduct(".json_encode($p).");\n";
			}
			$out .= "upsmart.products.pcounter = ".($i+1).";});</script>";
		}
		
		$media_url = admin_url("media-upload.php?type=image&amp;TB_iframe=true");
		$out .= <<<EOHTML
		<script type="text/javascript">
			media_input = "#photo_upload";
			function open_media_library() {
				tb_show("", "{$media_url}");
				window.send_to_editor = function(html) {
					imgurl = jQuery("img",html).attr("src");
					jQuery(media_input).val(imgurl);
					tb_remove();
				}
				return false;
			}
		</script>
		<style type="text/css">
			#TB_overlay {z-index: 2000 !important;}
			#TB_window {z-index: 2002 !important;}
		</style>
		<div id='products'>
		<div id='new'>+</div></div>
		<br/>
		<div id='dialog'></div>
		<form method='post' id="form">
			<input type='hidden' name='json'/>
			<input type='submit' value='Save'/>
		</form>
EOHTML;
		return $out;
	}
	function upsmart_create_products_save() {
		global $wpdb;
		
		$data = json_decode(stripslashes($_POST['json']));
		
		$wpdb->query($wpdb->prepare("DELETE FROM upsmart_products WHERE wordpress_id = %d",array(get_current_user_id())));
		
		foreach($data as $p) {
			if($p == null) continue;
			$result = $wpdb->query($wpdb->prepare("INSERT INTO upsmart_products
							(wordpress_id,name,shortdesc,longdesc,photo)
							VALUES(%d,%s,%s,%s,%s)",
							array(
								get_current_user_id(),
								$p->name,
								$p->shortdesc,
								$p->longdesc,
								$p->photo,
							)
			));
			
			if($result === false) return false;
		}
		
		return true;
	}
	
	function upsmart_create_get_template_set($company) {
		switch_to_blog($company['business']['site_id']);
		$theme = get_option('template');
		restore_current_blog();
		
		//Figure out what templates to use.
		if(file_exists(plugin_dir_path(__FILE__).'templates/'.$theme)) {
			return $theme;
		} else {
			return 'default';
		}
	}
	
	function upsmart_create_generate_page($site_id,$company,$page,$template_set='default') {
		switch_to_blog($site_id);
		
		$template_dir = plugin_dir_path(__FILE__).'templates/'.$template_set;
		$file = $template_dir.'/'.$page.'.php';
		require_once $file;
		$class = $template_set.'_'.$page;
		
		$template = new $class($company);
		
		if((string)($template) == '') return 0;
		
		//Delete the page if it exists.
		$args=array(
			'name' => $template->getName(),
			'post_type' => 'page',
			'post_status' => 'publish',
			'numberposts' => 1
		);
		$page = get_posts($args);
		
		if(count($page)) wp_delete_post($page[0]->ID, true);
					
		//Create the page.
		$post = array(
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_name' => $template->getName(),
			'post_title' => $template->getTitle(),
			'post_content' => (string)($template),
		);
		$post_id = wp_insert_post($post);
		
		//Set the home page.
		if($template->getName() == 'home') {
			update_option('show_on_front','page');
			update_option("page_on_front",$post_id);
		}
		
		restore_current_blog();
		
		return 1;
	}
	
	function upsmart_create_get_pages($template_set,$type) {
		$output = array();
		
		$files = glob(plugin_dir_path(__FILE__).'templates/default/*.php');
		foreach($files as $file) {
			$page = substr(basename($file),0,-4);
			require_once $file;
			$class = 'default_'.$page;
			$types = $class::getUsedData();
			if(array_search($type,$types) !== false) $output[] = $page;
		}
		
		return $output;
	}