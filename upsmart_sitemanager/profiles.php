<?php
	/**
	  * @package UpSmart_SiteManager
	  * @author Aaron Tobias
	  */

	function upsmart_page_profiles($post) {
		$post->post_title = 'Company Profiles';
		$post->comment_status = 'closed';
		switch(upsmart_handle_page_info) {
			default:
			case '/1': $post = upsmart_page_profiles_company_listing($post); break;
			case '/9': $post = upsmart_page_profiles_company_home($post); break;
			case '/2': $post = upsmart_page_profiles_company_about($post); break;
			case '/3': $post = upsmart_page_profiles_company_mission($post); break;
			
			case '/12': $post = upsmart_page_profiles_company_invest($post); break;
			case '/13': $post = upsmart_page_profiles_company_invest_checkout($post); break;
			case '/14': $post = upsmart_page_profiles_company_labs($post); break;
			//Add more cases
		}
		$post->post_content .= "<br/><br/>";
		
		return $post;
	}
	
	/*added function for company profiles blog*/
	function get_category_id($cat_name){
		//$term = get_term_by('name', $cat_name, 'category');
		return 0;//$term->term_id;
	}
	
	
/***************************************************************************************
 * Company Profiles Section Template
 * << Under Construction >>
 * ************************************************************************************/


	function upsmart_page_profiles_company_listing($post) {
		global $wpdb;
		$post->post_title = "Company Profiles";
		$result = $wpdb->get_results("SELECT B.wordpress_id as id, B.name, B.url, P.about, P.logo  
								FROM upsmart_business B, upsmart_profile P  
								WHERE B.wordpress_id = P.wordpress_id"
								, ARRAY_A);
		
		if($result === false) wp_die("An error has occurred: Unable to fetch companies from database.");

		foreach($result as $row){

			$id = $row['id'];
			$cpname = $row['name']; 
			$url = $row['url'];
			$about = $row['about'];
			$logo = home_url($row['logo']);
			$excerpt = substr($about, 0, strlen($about)/3); 
			//Assuming database has a default filler company logo image, so it can never fail.
			list($width, $height) = getimagesize($logo);
			
			$link = home_url('profiles/9?id=' . $id);
			
			$post->post_content .= <<<EOHTML
			<div class="company-listing">
				<a class="image-link left" href="$link">
					<img src="$logo" width="$width" height="$height" alt="$cpname company logo"/>
				</a>
				<span class="listing-info right">
					<h3><a class="header-link" href="$link">$cpname</a></h3>
					<h4><a class="header-link" href="$url">$cpname Website</a></h4>
					<p class="listing-info-snippet">
						$excerpt &hellip;<a class="header-link" href="$link">read more</a>
					</p>
				</span>
			</div>
EOHTML;
		}
		return $post;
	}
		 
		 
		 
	function upsmart_page_profiles_company_home($post) {
		global $wpdb;
		
		$result = false;
		
		if(!empty($_GET)) {
			
			$result = $wpdb->get_row($wpdb->prepare("SELECT B.*, P.* 
						FROM upsmart_business B, upsmart_profile P  
							WHERE P.wordpress_id = B.wordpress_id AND P.wordpress_id = %d",
							array(
								$_GET['id']
							)
			), ARRAY_A);
			
			if($result === false) wp_die("An error has occurred while loading the company.");
		}
		
		$id = $_GET['id'];
		$cpName = $result['name']; 
		$companyLink = strtolower(str_replace(" ","",$cpName));
		$url = $result['url'];
		$logo = $result['logo'];
		$linkHome = home_url('profiles/9?id=' . $id);
		$adopterLink = $companyLink . '/' . $companyLink .'EA';
		
		$linkLabs = home_url('profiles/14?id=' . $id);
		$linkInvest = home_url('profiles/12?id=' . $id);
		
		$linkAbout   = home_url('profiles/2?id='.$id);
		$linkMission = home_url('profiles/3?id='.$id);
		$linkHistory = home_url('profiles/3?id='.$id."#history");
		
		$result['about'] = wpautop(wptexturize(ellistr($result['about'],200)));
		$result['mission'] = wpautop(wptexturize(ellistr($result['mission'],200)));
		$result['history'] = wpautop(wptexturize(ellistr($result['history'],200)));

		$post->post_content .= <<<EOHTML
		<div id="link-sidebar" class="left">
		    <!-- LOGO IMAGES WERE ORIGINALLY SCALED DOWN IN PHP TO FIT CONTAINER, I HAVE THE CODE IF YOU NEED IT OR YOU CAN USE CSS 3 "Contain" -->
			<a class="image-link" href="$linkHome"><img id='company-logo' src="$logo" /></a>
			<div id="company-slogan">{$result['slogan']}</div>
			<div class="button-wrapper">
				<a href="http://www.go-upsmart.com/groups/$companyLink" class="a-btn radius">
					<span class="a-btn-text">Fan Group</span> 
					<span class="a-btn-slide-text">Support!</span>
					<span class="a-btn-icon-right"><span id="bubble"></span></span>
				</a>
				<a href="http://www.go-upsmart.com/groups/$adopterLink" class="a-btn radius">
					<span class="a-btn-text">Early Adopters</span>
					<span class="a-btn-slide-text">Try it!</span>
					<span class="a-btn-icon-right"><span id="bulb"></span></span>
				</a>
				<a href="$linkLabs" class="a-btn radius">
					<span class="a-btn-text">Company Labs</span>
					<span class="a-btn-slide-text">Innovate!</span>
					<span class="a-btn-icon-right"><span id="beaker"></span></span>
				</a>
				<a href="$linkInvest" class="a-btn radius">
					<span class="a-btn-text">Invest</span>
					<span class="a-btn-slide-text">Contribute!</span>
					<span class="a-btn-icon-right"><span id="invest"></span></span>
				</a>
			</div>
EOHTML;
		//$post->post_content .= do_action('sidebar_right');
		$post->post_content .= <<<EOHTML
		</div>
		
	   <!-- I LEFT OUT THE SOCIAL LINKS SIDEBAR AS I THINK SAM WANTS TO DO SOEMTHING ELSE WITH IT OR PUT THE LINKS SOMEWHERE ELSE. TALK TO HIM ABOUT THAT UI SHIFT -->
		
	   <div id="company-main-content" class="left">
		   <h3 class = "company-page-title">$cpName</h3>
		   <div id="mediaFrame">	          
		         <!-- THIS SECTION NEEDS TO BE COMPLETED ONCE DATABASE SUPPORTS THIS SECTION -->
EOHTML;
				$mime_type = null;
				switch($mime_type)
				{
					case 'image/svg+xml': 
						$post->post_content .= '<embed src="/cp-profiles/companies/Tumalow/Tumalowpresentation.svg" type="image/svg+xml" width="485" height="280"></embed>';
					    break;
					case 'video/x-youtube': 
						$post->post_content .= '<iframe width="485" height="280" src="http://youtu.be/b21rTV_MlxQ" frameborder="0" allowfullscreen></iframe>';
						break;
					default:
						$post->post_content .= '<p class="promotional-warning">No company promotional is present at this time</p>';
						break;
				}
				
				
		$post->post_content .= <<<EOHTML
		   </div>
			<div id="company-sections">
			<div><h3><a href='{$linkAbout}'>Who We Are</a></h3>
			{$result['about']}</div>
			<div><h3><a href='{$linkMission}'>What We Do</a></h3>
			{$result['mission']}</div>
			<div><h3><a href='{$linkHistory}'>How We Got Here</a></h3>
			{$result['history']}</div>
			</div>
			<br style='clear: both'/>
			<br/>
			<h3>Recent Posts</h3>
			<div id="blog" class="clear">
EOHTML;
				$companyId = get_category_id($cpName);
				$post->post_content .= do_shortcode('[wp_cpl_sc cat_id=' . $companyId . ' sort_order="desc"]');
				$post->post_content .= <<<EOHTML
			</div>
		</div>	
EOHTML;
		return $post;
		
	}
	
	function upsmart_page_profiles_company_mission($post) {
		global $wpdb;
		
		$result = false;
		
		if(!empty($_GET)) {
						
			$result = $wpdb->get_row($wpdb->prepare("SELECT P.mission, P.history, B.name 
							FROM upsmart_profile P, upsmart_business B 
							WHERE P.wordpress_id = B.wordpress_id AND B.wordpress_id = %d",
							array(
								$_GET['id']
							)
			), ARRAY_A);

			if($result === false) wp_die("An error has occurred.");
		}
		
		$company = $result['name'];
		$mission = wpautop(wptexturize($result['mission']));
		$history = wpautop(wptexturize($result['history']));
		
		$homeurl = home_url('profiles/9?id=' . $_GET['id']);
		
		$post->post_content .= <<<EOHTML
		<h3 class = "company-page-title"><a href='{$homeurl}'>$company</a></h3>
		<h4 class = "company-page-subHead">Our Mission</h4>
		<p class = "company-page-text-block">$mission</p>	
		<a name='history'></a>
		<h4 class = "company-page-subHead">A Rich History</h4>
		<p class = "company-page-text-block">$history</p>	
EOHTML;

		return $post;
	}
	
	function upsmart_page_profiles_company_about($post) {
		global $wpdb;
		
		$aboutData = false;
		$peopleData = false;
		
		if(!empty($_GET)) {
			
			$peopleData= $wpdb->get_results($wpdb->prepare("SELECT * 
						FROM upsmart_people 
						WHERE wordpress_id = %d",
							array(
								$_GET['id']
							)
			), ARRAY_A);
						
			if($peopleData === false) wp_die("An error has occurred.");
						
			$aboutData = $wpdb->get_row($wpdb->prepare("SELECT P.about, B.name 
							FROM upsmart_profile P, upsmart_business B 
							WHERE P.wordpress_id = B.wordpress_id AND P.wordpress_id = %d",
							array(
								$_GET['id']
							)
			), ARRAY_A);
			
			if($aboutData === false) wp_die("An error has occurred.");
			
		}

		$company = $aboutData['name'];
		$aboutContent = wpautop(wptexturize($aboutData['about']));
		
		$homeurl = home_url('profiles/9?id=' . $_GET['id']);
		
		$post->post_content .= <<<EOHTML
		<h3 class = "company-page-title"><a href='{$homeurl}'>$company</a></h3>
		<h4 class = "company-page-subHead">About Us</h4>
		<p class = "company-page-text-block">$aboutContent</p>	
		<h4 class = "company-page-subHead">The People</h4>
EOHTML;
	
		foreach($peopleData as $row){
			$cpname = $row['fname'] . ' ' . $row['lname']; 
			$title = $row['title'];
			$bio = $row['bio'];
			$photo= $row['photo'];
			$email = 'Use company email';

			$post->post_content .= <<<EOHTML
			<div class="person-profile">
				<img src="$photo" alt="Photo of $cpname" title="$cpname"/>
				<div class="person-profile-content">
					<div class="person-profile-header">
						<span class="person-profile-name">$cpname</span>
						<span class="person-profile-year">'13</span>
					</div>
					<div class="person-profile-title">$title</div>
					<p class="person-profile-bio">$bio</p>
				</div>
			</div>
EOHTML;
		}
		
		return $post;
		
	}
	
	
	function upsmart_page_profiles_company_invest($post) {
		global $wpdb;
		upsmart_require_login();
		
		$result = false;
		
		if(!empty($_GET)) {
						
			$result = $wpdb->get_row($wpdb->prepare("SELECT name 
							FROM upsmart_business 
							WHERE wordpress_id = %d",
							array(
								$_GET['id']
							)
			), ARRAY_A);
			
			if($result === false) wp_die("An error has occurred.");	
		}
		
		$company = $result['name'];
		$post->post_content .= <<<EOHTML
			<h3 class = "company-page-title">Invest in $company</h3>
			<p class="company-page-text-block">
			While we have to wait until January to give ownership in our companies in exchange for investment, you can still support 
			them now through donation! In fact, give more than &#36;x&#46;xx to any one of our companies and you will earn &#36;yx&#46;yy in UpSmart Credit, 
			for you to invest in January. That&apos;s right! Help our entrepreneurs out and we will help you pay for your first ownership in a company.
			</p>
			
			<div id="money">
				<form action="home_url('profiles/13')" method="Post">
					<input name="companyName" type="hidden" value="$company"/>
					
					<label for=dollars>$</label>
					<input id="dollars" name="dollars"type="text" placeholder="Dollar Amount" required autofocus />
					
					<label for=cents>&#46;</label>
					<input id=cents name=cents type=text placeholder="Amount of Cents" required />
EOHTML;
					$post->post_content .= do_shortcode('[s2Member-Security-Badge v="1"]');
					$post->post_content .= <<<EOHTML
					<input id="upsmartCredit" name="upsmartCredit" value="agree" type="checkbox"> 
					<label for = "upsmartCredit">Yes, I would like to recieve UpSmart Credit for my contribution.</label>
					
					<input id="termsOfServerice" name="termsOfServerice" value="agree" type="checkbox"> 
					<label for = "termsOfServerice">Yes, I have read and agreed to the terms of service.</label>
				</form>
EOHTML;
				$post->post_content .= do_shortcode('[s2Member-PayPal-Button level="1" ccaps="" desc="Silver Member / description and pricing details here." 
				ps="paypal" lc="" cc="USD" dg="0" ns="1" custom="website.com" ta="100" tp="1" tt="M" ra="14.99" rp="1" rt="M" rr="1" rrt="" rra="1" 
				image="default" output="button" ]');
				$post->post_content .= <<<EOHTML
			</div>
EOHTML;

	return $post;
		
	}


	function upsmart_page_profiles_company_invest_checkout($post) {
		global $wpdb;
		
		upsmart_require_login();

		$amount = 0;
		$reward = 0;
		
		if( isset($_POST["dollars"]) ){
			$dollar = intval(esc_attr($_POST["dollars"]));
			$amount =  number_format(round($dollar, 2), 2);
		}
		
		$errorVar = '&invalid=';
		
		if(isset($_POST["investRewards"]) ){
		   
			$reward = $_POST["investRewards"];
			
			if($amount <= 1){ $errorVar .= "more than a $1.00"; header("Location: " . home_url('profiles/12') . $errorVar); }
			else
			if($reward == "T-Shirt") {
				$errorVar .= "$10.00";
				if($amount < 10){ header("Location: " . home_url('profiles/12') . $errorVar); }
			}
			else if($reward == "UpSmart Credits") {
				$errorVar .= "$25.00";
				if($amount < 25){ header("Location: " . home_url('profiles/12') . $errorVar); }
			}
		}
		else{
		  header("Location: " . home_url('profiles/12') . $errorVar);
		}
		
		$company = $_POST["companyName"];
		
		$post->post_content .= <<<EOHTML
		<h3 class = "company-page-title">Checkout for {$_POST["companyname"]}</h3>
		<section id="summary">
			<ul class="left"> 
				<li><b>Funding: </b> <span class="finalSelection">$company</span></li>
				<li>By: $companyContact</li>
				<li>
					<label for = "termsOfServerice">Yes, I have read and agreed to the <a href="/about-us/"> terms of service.</a></label>
					<input id="termsOfServerice" name="termsOfServerice" value="agree" type="checkbox" required>
				</li>
			</ul>
			<ul class="right"> 
				<li>Pledge: <span class="final-selection">$amount</span></li>
				<li>Selected Reward: <span class="final-selection">$reward</span></li>
				<li>
					<a id="change-investment" href="home_url('profiles/12')">Change Investment Amount!</a>
				</li>
			</ul>
		</section>	

		<section id="checkout">
			<h4> Please select one of our payment services to complete your contribution.</h4>
EOHTML;
			$post->post_content .= do_shortcode('[s2Member-PayPal-Button level="1" ccaps="" desc="Benefactor" ps="paypal" lc="" cc="USD" dg="0" ns="1" custom="website.com" ta="0" tp="1" tt="M" ra="$amount" rp="1" rt="L" rr="1" rrt="" rra="1" image="default" output="button" ]');
			$post->post_content .= <<<EOHTML
			<form action="https://checkout.google.com/api/checkout/v2/checkoutForm/Merchant/857665999167502" id="BB_BuyButtonForm" class="right" method="post" name="BB_BuyButtonForm" target="_top">
				<input name="item_name_1" type="hidden" value="$company Donation"/>
				<input name="item_description_1" type="hidden" value=""/>
				<input name="item_quantity_1" type="hidden" value="1"/>
				<input name="item_price_1" type="hidden" value="$amount"/>
				<input name="item_currency_1" type="hidden" value="USD"/>
				<input name="_charset_" type="hidden" value="utf-8"/>
				<input alt="" src="https://checkout.google.com/buttons/buy.gif?merchant_id=857665999167502&amp;w=121&amp;h=44&amp;style=trans&amp;variant=text&amp;loc=en_US" type="image" height="44px" width="121px"/>
			</form>	
		</section>
EOHTML;

	return $post;
		
	}
	
	
	function upsmart_page_profiles_company_labs($post) {
		global $wpdb;
		
		upsmart_require_login();
		
		//This page was never completed...
		
	}
