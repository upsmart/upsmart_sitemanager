<?php
	/**
	  * @package UpSmart_SiteManager
	  * @author T.J. Lipscomb
	  * Shortcode for displaying the N most recent company profiles.
	  */

	function profiles_shortcode($atts) {
		global $wpdb;
		
		$atts = shortcode_atts( array(
			'count' => 6,
			'excerpt' => "no",
			'logo' => "no",
		), $atts );
		
		$result = $wpdb->get_results("SELECT B.wordpress_id as id, B.name, B.url, P.about, P.logo  
								FROM upsmart_business B, upsmart_profile P  
								WHERE B.wordpress_id = P.wordpress_id LIMIT {$atts['count']}"
								, ARRAY_A);
		
		if($result === false) wp_die("An error has occurred: Unable to fetch companies from database.");
		
		$out = '<ul class="company-profile-shortcode">';
		
		foreach($result as $row) {
			$excerpt = wpautop(wptexturize(ellistr($row['about'],200))); 			
			$link = home_url('profiles/9?id=' . $row['id']);
			
			$out .= "<li>";
			if($atts['logo'] == "yes") $out .= "<img src='{$row['logo']}'/>";
			$out .= "<h3><a href='{$link}'>{$row['name']}</a></h3>";
			if($atts['excerpt'] == "yes") $out .= "$excerpt &hellip;<a href='$link'>read more</a>";
			$out .= "</li>";
		}
		$out .= "</ul>";
		
		return $out;
	}
	add_shortcode('upsmart_companies','profiles_shortcode');
