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
	function upsmart_page_create_nav($n,$sn=null) {
		$out = "<ol class='steps'>
			<li".($n==1?" class='active'":"").">1. Sign In</li>
			<li".($n==2?" class='active'":"").">2. Client Info</li>
			<li".($n==3?" class='active'":"").">3. Business Info</li>
			<li".($n==4?" class='active'":"").">4. Technical Info</li>
		</ol><br style='clear: both'/>";
		
		if($sn != null) {
			$out .= "<ol class='steps substeps'>
				<li".($sn==1?" class='active'":"").">a. Basic Info</li>
				<li".($sn==2?" class='active'":"").">b. The Team</li>
				<li".($sn==3?" class='active'":"").">c. About the Company</li>
				<li".($sn==4?" class='active'":"").">d. Products</li>
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
		global $wpdb;
		
		upsmart_require_login();
		
		$post->post_content = upsmart_page_create_nav(2).'<h2>Client Information</h2>';
		
		if(!empty($_POST)) {
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
			
			if($result === false) wp_die("An error has occurred.");
		}
		
		if($wpdb->get_var("SELECT count(*) FROM upsmart_contact WHERE wordpress_id=".get_current_user_id())) {
			wp_redirect(home_url('create/3'));
			exit();
		}
		
		
		$post->post_content .= <<<EOHTML
		We'd like to know more about you and your company. Just fill out the fields below (everything is required).
		<h3>Identification</h3>
		<form method='post'>
		<table>
			<tr><th colspan='2'><h4>Contact</h4></th></tr>
			<tr><th>Name</th><td class='twoinput'><input name='contact_firstname' placeholder='First'/><input name='contact_lastname' placeholder='Last'/></td></tr>
			<tr><th>Email</th><td><input name='contact_email' type='email' placeholder='Email'/></td></tr>
			<tr><th>Phone Number</th>
				<td class='normalinput'>
					(<input name='contact_phone1' type='number' size='3' placeholder='000'/>) -
					<input name='contact_phone2' type='number' size='3' placeholder='000'/> -
					<input name='contact_phone3' type='number' size='4' placeholder='0000'/>
				</td>
			</tr>
			<tr><th>Address</th>
				<td class='normalinput'>
					<input name='contact_street' placeholder='Street/PO Box' style='width: 100%'/><br/>
					<input name='contact_city' placeholder='City'/> <input name='contact_state' placeholder='State'/><br/>
					<input name='contact_zip' size='5' placeholder='Zip Code'/><br/>
				</td>
			</tr>
			<tr><th colspan='2'><input type='submit' value='Save and Next'/></th></tr>
		</table>
		</form>
EOHTML;
		
		return $post;
	}
	
	function upsmart_page_create_business_basic($post) {
		global $wpdb;
		
		upsmart_require_login();
		
		$post->post_content = upsmart_page_create_nav(3,1).'<h2>Business Information</h2>';
		
		if(!empty($_POST)) {
			$result = $wpdb->query($wpdb->prepare("REPLACE INTO upsmart_business
							(wordpress_id,name,incorporated,url)
							VALUES(%d,%s,%d,%s)",
							array(
								get_current_user_id(),
								$_POST['project_name'],
								($_POST['project_incorporated']=='yes')?1:0,
								($_POST['project_hassite']=='yes')?$_POST['project_site']:''
							)
			));
			
			if($result === false) wp_die("An error has occurred.");
		}
		
 		if($wpdb->get_var("SELECT count(*) FROM upsmart_business WHERE wordpress_id=".get_current_user_id())) {
 			wp_redirect(home_url('create/4'));
 			exit();
 		}
		
		
		$post->post_content .= <<<EOHTML
		We'd like to know more about you and your company. Just fill out the fields below (everything is required).
		<form method='post'>
		<h3>Project</h3>
		<table>
			<tr><th>Company/Project Name</th><td><input name='project_name' placeholder='Company/Project Name'/></td></tr>
			<tr><td>Has your company been incorporated?</td>
				<td>
					<input type='radio' name='project_incorporated' value='yes'/> Yes<br/>
					<input type='radio' name='project_incorporated' value='no'/> No
				</td>
			</tr>
			<tr><td>Do you have a web site?</td>
				<td>
					<input type='radio' name='project_hassite' value='yes'/> Yes<br/>
					<input type='radio' name='project_hassite' value='no'/> No
				</td>
			</tr>
			<tr><th>Web Site URL</th><td><input name='project_site' placeholder='http://www.example.com/'/></td></tr>
			<tr><th colspan='2'><input type='submit' value='Save and Next'/></th></tr>
		</table>
		</form>
EOHTML;
		
		return $post;
	}
	
	function upsmart_page_create_business_people($post) {
		global $wpdb;
		
		upsmart_require_login();
		
		$post->post_content = upsmart_page_create_nav(3,2).'<h2>Business Information</h2>';
		
		if(!empty($_POST)) {
			$data = json_decode(stripslashes($_POST['json']));
			foreach($data as $p) {
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
				
				if($result === false) wp_die("An error has occurred.");
			}
			
		}
		
 		if($wpdb->get_var("SELECT count(*) FROM upsmart_people WHERE wordpress_id=".get_current_user_id())) {
 			wp_redirect(home_url('create/5'));
 			exit();
  		}

		$js_url = plugins_url('js/create_business_people.js',__FILE__);
		
		$post->post_content .= <<<EOHTML
		We'd like to know more about you and your company. Just fill out the fields below (everything is required).
		
		<h3>The Team</h3>
		Introducing the people behind the idea is important. Feel free to send in snapshots of each person. Putting a face to the product is surprisingly good at making an idea "come to life" in the minds of others.
		<div id='team'>
		<div id='new'>+</div></div>
		<br/>
		<div id='dialog'></div>
		<form method='post' id="form">
			<input type='hidden' name='json'/>
			<input type='submit' value='Save and Next'/>
		</form>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		<script src="$js_url"></script>
		<style type='text/css'>
			@import url("http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css");
			#dialog{
				display: none;
			}
			#team {
				background: #EEE;
				padding: 1em;
			}
			#team>div {
				cursor: pointer;
				background: #FFF;
				width: 150px;
				height: 150px;
				display: inline-block;
				position: relative;
				vertical-align: middle;
			}
			#team>div>img {
				max-width: 150px;
				max-height: 150px;
			}
			#team>div>.label {
				position: absolute;
				bottom: 0px;
				background: rgba(0,0,0,0.7);
				color: #FFF;
				text-shadow: 0 -1px 0 rgba(0,0,0,0.7);
				font-weight: 600;
				text-align: center;
				width: 150px;
				height: 40px;
				line-height: 20px;
			}
			#new {
				font-size: 150px;
				line-height: 150px;
				text-align: center;
				color: #888;
			}
			input,textarea {
				vertical-align: top;
			}
		</style>
EOHTML;
		
		return $post;
	}
	
	function upsmart_page_create_business_desc($post) {
		global $wpdb;
		
		upsmart_require_login();
		
		$post->post_content = upsmart_page_create_nav(3,3).'<h2>Business Information</h2>';
		
		if(!empty($_POST)) {
			foreach($_FILES as $f) {
				if(substr($f['type'],0,6) != 'image/') {
					wp_die("Files of type {$f['type']} are not acceptable.");
				}
				if(substr($f['name'],-4) != '.png' && substr($f['name'],-4) != '.jpg') {
					wp_die("Only PNG and JPG images can be uploaded, not ".substr($f['name'],-4).".");
				}
			}
			
			$result = $wpdb->query($wpdb->prepare("REPLACE INTO upsmart_profile
							(wordpress_id,mission,about,history)
							VALUES(%d,%s,%d,%s)",
							array(
								get_current_user_id(),
								$_POST['mission'],
								$_POST['about'],
								$_POST['history'],
							)
			));
			if($result === false) wp_die("An error has occurred.");
			
			//Save the provided images.
			$dir = WP_CONTENT_DIR.'/uploads/upsmart/'.get_current_user_id().'/';
			@mkdir($dir,0777,true);
			move_uploaded_file($_FILES['logo']['tmp_name'],$dir.'logo'.substr($_FILES['logo']['name'],-4,4));
			move_uploaded_file($_FILES['media1']['tmp_name'],$dir.'media1'.substr($_FILES['logo']['name'],-4,4));
			move_uploaded_file($_FILES['media2']['tmp_name'],$dir.'media2'.substr($_FILES['logo']['name'],-4,4));
		}
		
 		if($wpdb->get_var("SELECT count(*) FROM upsmart_profile WHERE wordpress_id=".get_current_user_id())) {
 			wp_redirect(home_url('create/6'));
 			exit();
 		}
		
		
		$post->post_content .= <<<EOHTML
		We'd like to know more about you and your company. Just fill out the fields below (everything is required).
		<form method='post' enctype='multipart/form-data'>
		<h3>Business Information</h3>
		<h4>About the Company</h4>
	<h5>Branding</h5>
	<br/>
	<table>
		<tr><th>Logo</th><td><input type='file' name='logo'/></tr>
		<tr><td colspan='2'>The bigger the better&mdash;don't worry, we'll scale this down for you. This image should have a transparent background and be suitable for display on a colored background.</td></tr>
		
		<tr><th><br/>Media</th></tr>
		<tr><td colspan='2'>Now upload the "coolest" piece of media you have. This will be used on the top fold of your generated site to draw users in.
		<br/><input type='file' name='media1'/><br/>
		<ul><li>The bigger the better&mdash;don't worry, we'll scale this down for you.</li><li>The uploaded file should have a solid background.</li><li>Accepted Formats: JPG, PNG</li></ul></td></tr>
		
		<tr><td colspan='2'>While you're at it, give us the second coolest picture you have as well. Who knows when you might need it?
		<br/><input type='file' name='media2'/>
		</td></tr>
	</table>
	<h5>About</h5>
	<br/>
	<table>
		<tr><th colspan='2'>Mission Statement</th></tr>
		<tr><td colspan='2'>Your mission statement should include ...</td></tr>
		<tr><td colspan='2'><textarea rows='10' name='mission'></textarea></td></tr>
		
		<tr><th colspan='2'>"About Us"</th></tr>
		<tr><td colspan='2'>A good "About Us" writeup should answer the following questions: <ul><li>Who are you? (Think back to the people biographies you just submitted)</li><li>What are you doing?</li><li>Why is what you're doing awesome?</li></ul></td></tr>
		<tr><td colspan='2'><textarea rows='10' name='about'></textarea></td></tr>
		
		<tr><th colspan='2'>Company History</th></tr>
		<tr><td colspan='2'>Let us know your background. Some things to think about: ...</td></tr>
		<tr><td colspan='2'><textarea rows='10' name='history'></textarea></td></tr>
		
		<tr><th colspan='2'><input type='submit' value='Save and Next'/></th></tr>
	</table>
			
		</table>
		</form>
EOHTML;
		
		return $post;
	}
	
	function upsmart_page_create_products($post) {
		global $wpdb;
		
		upsmart_require_login();
		
		if(!empty($_POST)) {
			$data = json_decode(stripslashes($_POST['json']));
			foreach($data as $p) {
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
				
				if($result === false) wp_die("An error has occurred.");
			}
			
		}
		
 		if($wpdb->get_var("SELECT count(*) FROM upsmart_products WHERE wordpress_id=".get_current_user_id())) {
 			wp_redirect(home_url('create/7'));
 			exit();
  		}
		
		$js_url = plugins_url('js/create_business_products.js',__FILE__);
		$post->post_content .= <<<EOHTML
		We'd like to know more about you and your company. Just fill out the fields below (everything is required).
		<h3>Products</h3>
		Now it's time to learn more about your company's product(s). Click on the plus button below to add one.
		<div id='products'>
		<div id='new'>+</div></div>
		<br/>
		<div id='dialog'></div>
		<form method='post' id="form">
			<input type='hidden' name='json'/>
			<input type='submit' value='Save and Next'/>
		</form>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		<script src="$js_url"></script>
		<style type='text/css'>
			@import url("http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css");
			#dialog{
				display: none;
			}
			#products {
				background: #EEE;
				padding: 1em;
			}
			#products>div {
				cursor: pointer;
				background: #FFF;
				width: 150px;
				height: 150px;
				display: inline-block;
				position: relative;
				vertical-align: middle;
			}
			#products>div>img {
				max-width: 150px;
				max-height: 150px;
			}
			#products>div>.label {
				position: absolute;
				bottom: 0px;
				background: rgba(0,0,0,0.7);
				color: #FFF;
				text-shadow: 0 -1px 0 rgba(0,0,0,0.7);
				font-weight: 600;
				text-align: center;
				width: 150px;
				height: 40px;
				line-height: 20px;
			}
			#new {
				font-size: 150px;
				line-height: 150px;
				text-align: center;
				color: #888;
			}
			input,textarea {
				vertical-align: top;
			}
			textarea {
				width: 100%;
			}
		</style>
EOHTML;
		
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
			$company = array();
			$company['contact'] = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_contact WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
			$company['profile'] = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_profile WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
			$company['business'] = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_business WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
			$company['people'] = $wpdb->get_results($wpdb->prepare("SELECT * FROM upsmart_people WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
			$company['products'] = $wpdb->get_results($wpdb->prepare("SELECT * FROM upsmart_products WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
			
			$company['site'] = array(
				'theme' => $_POST['theme'],
				'domain' => ($_POST['use_domain'] == 'yes')?$_POST['domain']:($_POST['subdomain'].'upsmart.com'),
			);
			
			/*Create their site here*/
				//http://phpxref.ftwr.co.uk/wordpress/nav.html?wp-includes/ms-functions.php.html
				if(get_blog_id_from_url($company['site']['domain'],'/') != 0) {
					wp_die("That domain is already in use.");
				}
				
				//Site options go here.
				$meta = array(
					'template' => $company['site']['theme'],
					'stylesheet' => $company['site']['theme'],
					
					'home' => 'http://'.$company['site']['domain'].'/';
					'siteurl' => 'http://'.$company['site']['domain'].'/';
					'show_on_front' => 'page',
				);
				$id = wpmu_create_blog($company['site']['domain'],'/',$company['business']['name'],get_current_user_id(),$meta);
				$wpdb->query($wpdb->prepare("UPDATE upsmart_business SET site_id=%d,url='http://%s/' WHERE wordpress_id=%d",$id,$company['site']['domain'],get_current_user_id()));
				
				//This appears necessary for wordpress to understand how to route requests.
				$wpdb->query($wpdb->prepare("INSERT INTO wp_site (id,domain,path) VALUES(%d,'%s','/')",$id,$company['site']['domain']));
				
				//CODE HERE TO SET UP BIND TO SERVE THE SITE.
				
				//Switch blogs so everything we do is in the context of the new site.
				switch_to_blog($id);
				
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
					require $file;
					$class = $template_set.'_'.substr(basename($file),0,-4);
					$template = new $class($company);
					
					//Create a page for the template
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
						update_option("page_on_front",$post_id);
					}
				}
			
			//Done? Send them to the "YOU'RE DONE" page.
			wp_redirect(home_url('create/8'));
		}
		
 		$themes = "";
 		$theme_list = wp_get_themes(array('allowed'=>'network'));
 		foreach($theme_list as $n => $t) {
			//var_dump($t);
			$themes .= "<div class='theme' style='float: left;'><img src='".content_url('themes/'.$n.'/screenshot.png')."' style='width: 150px'/><br/><input type='radio' name='theme' value='$n'/> {$t->Name}</div>";
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
					{$themes}<br style='clear: both'/>
				</td></tr>
				<tr><th colspan='2'><input type='submit' value='Save and Create'/></th></tr>
				</tbody>
			</table>
		</form>
EOHTML;
		
		return $post;
	}
	
	function upsmart_page_create_done($post) {
		global $wpdb;
		
		upsmart_require_login();
		
		$lol = $wpdb->get_var("SELECT url FROM upsmart_business WHERE wordpress_id=".get_current_user_id());
		
		
		$post->post_content = upsmart_page_create_nav(4).'<h2>Done!</h2>';
		$post->post_content .= <<<EOHTML
		
		<p>Your site has been created. <a href='{$lol}'>Click here</a> to check it out.</p>
EOHTML;
		
		return $post;
	}