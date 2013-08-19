<?php
	/**
	  * @package UpSmart_SiteManager
	  * 
	  */

	add_action('user_admin_menu', 'register_upsmart_create_admin');
	add_action('admin_menu', 'register_upsmart_create_admin');
	add_action( 'wp_dashboard_setup', 'upsmart_create_dashboard_setup' );


function register_upsmart_create_admin() {
	add_menu_page( 'UpSmart Profile', 'UpSmart Profile', 'manage_options', 'upsmart_create', 'upsmart_create_admin_home' ); 
	add_submenu_page( 'upsmart_create', 'Edit Contact Info', 'Contact Info', 'manage_options', 'upsmart_edit_contact', 'upsmart_create_admin_contact' );
	add_submenu_page( 'upsmart_create', 'Edit Business Information', 'Business', 'manage_options', 'upsmart_edit_profile', 'upsmart_create_admin_profile' );
	add_submenu_page( NULL, 'Regenerate Pages', NULL, 'manage_options', 'upsmart_regenerate', 'upsmart_create_admin_regenerate' );
	
	$page = add_submenu_page( 'upsmart_create', 'Edit Business Profile', 'Profile', 'manage_options', 'upsmart_edit_profile', 'upsmart_create_admin_profile' );
	add_action('admin_print_scripts-'.$page, 'upsmart_create_profile_scripts');
	
	$page = add_submenu_page( 'upsmart_create', 'Edit People', 'People', 'manage_options', 'upsmart_edit_people', 'upsmart_create_admin_people' );
	add_action('admin_print_scripts-'.$page, 'upsmart_create_people_scripts');
	
	$page = add_submenu_page( 'upsmart_create', 'Edit Products', 'Products', 'manage_options', 'upsmart_edit_products', 'upsmart_create_admin_products' );
	add_action('admin_print_scripts-'.$page, 'upsmart_create_products_scripts');
}

function upsmart_create_dashboard_setup() {
	wp_add_dashboard_widget(
		'upsmart-create-dashboard-widget',
		'UpSmart Site Manager',
		'upsmart_create_dashboard',
		$control_callback = null
	);
}

function upsmart_create_dashboard() {
    global $wpdb;
	$company = upsmart_create_get();
	
	echo "<div class='us-tool-box'><a href='./admin.php?page=upsmart_edit_contact' class='edit-link'>Edit</a><h4 class='title'>Contact Information</h4>";
	echo "<div>Name: <b>{$company['contact']['firstname']} {$company['contact']['lastname']}</b></div>";
	echo "<div>Email: <b>{$company['contact']['email']}</b></div>";
	echo "<div>Phone: <b>{$company['contact']['phone']}</b></div>";
	echo "</div>";
	
	echo "<div class='us-tool-box'><a href='./admin.php?page=upsmart_edit_business' class='edit-link'>Edit</a><h4 class='title'>Business Information</h4>";
	echo "<div>Name: <b>{$company['business']['name']}</b></div>";
	echo "<div>Slogan: <b>{$company['business']['slogan']}</b></div>";
	echo "</div>";
	
	echo "<div class='us-tool-box'><a href='./admin.php?page=upsmart_edit_profile' class='edit-link'>Edit</a><h4 class='title'>Company Profile</h4>";
	echo "<div><b>Mission Statement:</b>".wpautop(wptexturize(ellistr($company['profile']['mission'],200)))."</div>";
	echo "<div><b>About Us:</b>".wpautop(wptexturize(ellistr($company['profile']['about'],200)))."</div>";
	echo "<div><b>History:</b>".wpautop(wptexturize(ellistr($company['profile']['history'],200)))."</div>";
	echo "</div>";
	
	echo "<div class='us-tool-box'><a href='./admin.php?page=upsmart_edit_people' class='edit-link'>Edit</a><h4 class='title'>People</h4><ul>";
	foreach($company['people'] as $p) {
		echo "<li>{$p['fname']} {$p['lname']}</li>";
	}
	echo "</ul></div>";
	
	echo "<div class='us-tool-box'><a href='./admin.php?page=upsmart_edit_products' class='edit-link'>Edit</a><h4 class='title'>Products</h4><ul>";
	foreach($company['products'] as $p) {
		echo "<li>{$p['name']}</li>";
	}
	echo "</ul></div>";
	
	echo "<style type='text/css'>.edit-link {float: right} .us-tool-box{padding-left: 4px}.us-tool-box p {margin: 0;}.us-tool-box .title {border-bottom: 1px solid #ddd;margin-left:-4px !important;}</style>";
}

function upsmart_create_admin_home() {
	global $wpdb;
	$company = upsmart_create_get();
	
	echo "<h2>Upsmart Site Profile Manager</h2>";
	
	echo "<div class='tool-box'><a href='./admin.php?page=upsmart_edit_contact' class='edit-link'>Edit</a><h3 class='title'>Contact Information</h3>";
	echo "<div>Name: <b>{$company['contact']['firstname']} {$company['contact']['lastname']}</b></div>";
	echo "<div>Email: <b>{$company['contact']['email']}</b></div>";
	echo "<div>Phone: <b>{$company['contact']['phone']}</b></div>";
	echo "</div>";
	
	echo "<div class='tool-box'><a href='./admin.php?page=upsmart_edit_business' class='edit-link'>Edit</a><h3 class='title'>Business Information</h3>";
	echo "<div>Name: <b>{$company['business']['name']}</b></div>";
	echo "<div>Slogan: <b>{$company['business']['slogan']}</b></div>";
	echo "</div>";
	
	echo "<div class='tool-box'><a href='./admin.php?page=upsmart_edit_profile' class='edit-link'>Edit</a><h3 class='title'>Company Profile</h3>";
	echo "<div><b>Mission Statement:</b><br/>".wpautop(wptexturize(ellistr($company['profile']['mission'],200)))."</div>";
	echo "<div><b>About Us:</b><br/>".wpautop(wptexturize(ellistr($company['profile']['about'],200)))."</div>";
	echo "<div><b>History:</b><br/>".wpautop(wptexturize(ellistr($company['profile']['history'],200)))."</div>";
	echo "</div>";
	
	echo "<div class='tool-box'><a href='./admin.php?page=upsmart_edit_people' class='edit-link'>Edit</a><h3 class='title'>People</h3><ul>";
	foreach($company['people'] as $p) {
		echo "<li>{$p['fname']} {$p['lname']}</li>";
	}
	echo "</ul></div>";
	
	echo "<div class='tool-box'><a href='./admin.php?page=upsmart_edit_products' class='edit-link'>Edit</a><h3 class='title'>Products</h3><ul>";
	foreach($company['products'] as $p) {
		echo "<li>{$p['name']}</li>";
	}
	echo "</ul></div>";
	
	//if($
	echo "<div class='tool-box'><h3 class='title'>Regenerate Pages</h3>";
	echo "<p>If you've edited your profile and want to update your site, check which pages you want to regenerate below.</p>";
	echo "<form method='post' action='./admin.php?page=upsmart_regenerate'>";
	
	switch_to_blog($company['business']['site_id']);
	$template_set = upsmart_create_get_template_set();
	$template_dir = plugin_dir_path(__FILE__).'templates/'.$template_set;
	switch_to_blog(1);
	
	$templates = glob($template_dir.'/*.php');
	foreach($templates as $file) {
		require_once $file;
		$class = $template_set.'_'.substr(basename($file),0,-4);
		
		echo "<input type='checkbox' name='regenerate[".substr(basename($file),0,-4)."]'/> {$class::getTitle()}<br/>";
	}
	
	echo "<input type='submit' value='Regenerate Pages'/>";
	echo "</form>";
	echo "</div>";
	
	echo "<style type='text/css'>.edit-link {float: right} .tool-box .title {border-bottom: 1px solid #ddd}</style>";
	
	//Debug info
	/*echo "<pre>";
	var_dump($_GET,$company);
	echo "</pre>";*/
}

function upsmart_create_admin_contact() {
	global $wpdb;
	
	echo "<h2>Upsmart Site Profile Manager</h2>";
	echo "<h3>Edit Contact Information</h3>";
	
	$company = upsmart_create_get();
	$template_set = upsmart_create_get_template_set($company);
	$pages = upsmart_create_get_pages($template_set,'contact');
	
	if(!empty($_POST)) {
		$result = upsmart_create_contact_save();
		if($result === false) wp_die("An error has occurred.");
		
		$company = upsmart_create_get();
		foreach($pages as $p) {
			upsmart_create_generate_page($company['business']['site_id'],$company,$p,$template_set);
		}
		
		echo "Your changes have been saved.";
	}
	
	if(count($pages) > 0) {
		echo "<b>Warning:</b> The following pages on your UpSmart site will be overwritten if you click save:<ul>";
		foreach($pages as $p) {
			$class = $template_set.'_'.$p;
			echo "<li>{$class::getName()} (\"{$class::getTitle()}\")</li>";
		}
		echo "</ul>";
	}
	
	echo upsmart_create_contact_form();
}

function upsmart_create_admin_business() {
	global $wpdb;
	
	echo "<h2>Upsmart Site Profile Manager</h2>";
	echo "<h3>Edit Business Information</h3>";
	
	$company = upsmart_create_get();
	$template_set = upsmart_create_get_template_set($company);
	$pages = upsmart_create_get_pages($template_set,'business');
	
	if(!empty($_POST)) {
		$result = upsmart_create_business_save();
		if($result === false) wp_die("An error has occurred.");
		
		$company = upsmart_create_get();
		foreach($pages as $p) {
			upsmart_create_generate_page($company['business']['site_id'],$company,$p,$template_set);
		}
		
		echo "Your changes have been saved.";
	}
	
	if(count($pages) > 0) {
		echo "<b>Warning:</b> The following pages on your UpSmart site will be overwritten if you click save:<ul>";
		foreach($pages as $p) {
			$class = $template_set.'_'.$p;
			echo "<li>{$class::getName()} (\"{$class::getTitle()}\")</li>";
		}
		echo "</ul>";
	}
	
	echo upsmart_create_business_form();
}

function upsmart_create_admin_profile() {
	global $wpdb;
	
	echo "<h2>Upsmart Site Profile Manager</h2>";
	echo "<h3>Edit Business Profile</h3>";
	
	$company = upsmart_create_get();
	$template_set = upsmart_create_get_template_set($company);
	$pages = upsmart_create_get_pages($template_set,'profile');
	
	if(!empty($_POST)) {
		$result = upsmart_create_profile_save();
		if($result === false) wp_die("An error has occurred.");
		
		$company = upsmart_create_get();
		foreach($pages as $p) {
			upsmart_create_generate_page($company['business']['site_id'],$company,$p,$template_set);
		}
		
		echo "Your changes have been saved.";
	}

	if(count($pages) > 0) {
		echo "<b>Warning:</b> The following pages on your UpSmart site will be overwritten if you click save:<ul>";
		foreach($pages as $p) {
			$class = $template_set.'_'.$p;
			echo "<li>{$class::getName()} (\"{$class::getTitle()}\")</li>";
		}
		echo "</ul>";
	}
	
	echo upsmart_create_profile_form();
}

function upsmart_create_admin_people() {
	global $wpdb;
	
	echo "<h2>Upsmart Site Profile Manager</h2>";
	echo "<h3>Edit People</h3>";
	
	$company = upsmart_create_get();
	$template_set = upsmart_create_get_template_set($company);
	$pages = upsmart_create_get_pages($template_set,'people');
	
	if(!empty($_POST)) {
		$result = upsmart_create_people_save();
		if($result === false) wp_die("An error has occurred.");
		
		$company = upsmart_create_get();
		foreach($pages as $p) {
			upsmart_create_generate_page($company['business']['site_id'],$company,$p,$template_set);
		}
		
		echo "Your changes have been saved.";
	}

	if(count($pages) > 0) {
		echo "<b>Warning:</b> The following pages on your UpSmart site will be overwritten if you click save:<ul>";
		foreach($pages as $p) {
			$class = $template_set.'_'.$p;
			echo "<li>{$class::getName()} (\"{$class::getTitle()}\")</li>";
		}
		echo "</ul>";
	}
	
	echo upsmart_create_people_form();
}

function upsmart_create_admin_products() {
	global $wpdb;
	
	echo "<h2>Upsmart Site Profile Manager</h2>";
	echo "<h3>Edit Products</h3>";
	
	$company = upsmart_create_get();
	$template_set = upsmart_create_get_template_set($company);
	$pages = upsmart_create_get_pages($template_set,'products');
	
	if(!empty($_POST)) {
		$result = upsmart_create_products_save();
		if($result === false) wp_die("An error has occurred.");
		
		$company = upsmart_create_get();
		foreach($pages as $p) {
			upsmart_create_generate_page($company['business']['site_id'],$company,$p,$template_set);
		}
		
		echo "Your changes have been saved.";
	}

	if(count($pages) > 0) {
		echo "<b>Warning:</b> The following pages on your UpSmart site will be overwritten if you click save:<ul>";
		foreach($pages as $p) {
			$class = $template_set.'_'.$p;
			echo "<li>{$class::getName()} (\"{$class::getTitle()}\")</li>";
		}
		echo "</ul>";
	}
	
	echo upsmart_create_products_form();
}

function upsmart_create_admin_regenerate() {
	echo "<h2>Upsmart Site Profile Manager</h2>";
	echo "<h3>Regenerate Pages</h3>";
	
	$company = upsmart_create_get();
	switch_to_blog($company['business']['site_id']);
	$template_set = upsmart_create_get_template_set();
	$template_dir = plugin_dir_path(__FILE__).'templates/'.$template_set;
	switch_to_blog(1);
	
	foreach($_POST['regenerate'] as $file => $v) {
		if($v != 'on') continue;
		
		echo "Regenerating $file...";
		$out = upsmart_create_generate_page($company['business']['site_id'],$company,$file,$template_set);
		echo "$out page".($out==1?'':'s').' regenerated.<br/>';
	}
	
	echo "<br/>Done!";
}