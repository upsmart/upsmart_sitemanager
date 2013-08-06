<?php
	//TODO: Instead of get_current_user_id(), pass in an ID, so other users can be edited.
	function upsmart_create_contact_form() {
		global $wpdb;
		
		$data = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_contact WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		$data['phone'] = explode('-',$data['phone']);
		
		return <<<EOHTML
			<form method='post'>
			<table class= 'infoentry contact'>
				<tr><th colspan='2' id= 'contact_heading'><h3>Contact</h3></th></tr>
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
			</table>
			<input type='submit' value='Save'/>
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
			<table class='infoentry'>
				<tr><th>Company Name</th><td class= 'textinput'><input name='project_name' value='{$data['name']}' placeholder='Company Name'/></td></tr>
				<tr><th>Company Slogan</th><td class= 'textinput'><input name='project_slogan' value='{$data['slogan']}' placeholder='We do awesome stuff.'/></td></tr>
				<tr><th>Has your company been incorporated?</th>
					<td class= 'input'>
						<input type='radio' name='project_incorporated' {$inc['yes']} value='yes'/> Yes
						&nbsp;&nbsp;&nbsp;&nbsp;
						<input type='radio' name='project_incorporated' {$inc['no']} value='no'/> No
					</td>
				</tr>
				<tr><th>Web Site URL</th><td class= 'textinput'><input name='project_site' value='{$data['url']}' placeholder='http://www.example.com/'/></td></tr>
			</table>
			<input type='submit' value='Save'/>
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
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src='https://dl.dropboxusercontent.com/u/11993667/jQuery-Custom-File-Input/js/jQuery.fileinput.js'></script>

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
	<script type="text/javascript">
		$(function(){
			$('#file').customFileInput();
		});
		
	</script>
	<table class='media_infoentry'>
		<tr><td><h3>Branding</h3></td></tr>
		<tr><th><h4>Logo</h4></th><td><input type='text' id="logo_field" value="{$data['logo']}" name='logo'/>
		<input type='button' id='logo_upload' value='Upload/Open Library'/></tr>
		<tr><td colspan='2' class= 'subtext'>The bigger the better&mdash;don't worry, we'll scale this down for you. This image should have a transparent background and be suitable for display on a colored background.</td></tr>
		<tr><th><h4>Media</h4></th><td colspan='2'><input id="file" type='file' name='file'/>
		<input type='button' id='media1_upload' value='Upload/Open Library'/></td></tr>
		<tr><td colspan='2' class= 'subtext'>Now upload the "coolest" piece of media you have. This will be used on the top fold of your generated site to draw users in.</td></tr>
		<tr><td colspan='2' class= 'inputindent'>
		<input type='text' id="media2_field" value="{$data['media2']}" name='media2'/>
		<input type='button' id='media2_upload' value='Upload/Open Library'/>
		</td></tr>
		<tr><td colspan='2' class= 'subtext'>While you're at it, give us the second coolest piece of media you have as well. Who knows when you might need it?</td></tr>
		<tr>
		<th><h4>Note on images:</h4></th>
		<td colspan='2'>
		<li>The bigger the better&mdash;don't worry, we'll scale these down for you.</li><li>The uploaded files should have solid backgrounds.</li></td>
		</tr>
	</table>
	<table class= 'wp-editor'>

		<tr><th><strong>How many years has the company been in business?</strong></th><td><input type='text' size='2' maxlength='2' name='yearsactive' value='{$data['yearsactive']}'/>years</td></tr>
		<tr><th><h3>About</h3></th></tr>
		<tr><th colspan='2'><h4>Mission Statement</h4></th></tr>
		<tr><td colspan='2'>Your mission statement should include ...</td></tr>
		<tr><td colspan='2'>
EOHTML;
	$out .= upsmart_get_editor($data['mission'],'mission');
	$out .= <<<EOHTML
		</td></tr>
		
		<tr><th colspan='2'><h4>"About Us"</h4></th></tr>
		<tr><td colspan='2'>A good "About Us" writeup should answer the following questions: <li>Who are you? (Think back to the people biographies you just submitted)</li><li>What are you doing?</li><li>Why is what you're doing awesome?</li></td></tr>
		<tr><td colspan='2'>
EOHTML;
	$out .= upsmart_get_editor($data['about'],'about');
	$out .= <<<EOHTML
		</td></tr>
		
		<tr><th colspan='2'><h4>Company History</h4></th></tr>
		<tr><td colspan='2'>Let us know your background. Some things to think about: ...</td></tr>
		<tr><td colspan='2'>
EOHTML;
	$out .= upsmart_get_editor($data['history'],'history');
	$out .= <<<EOHTML
		</td></tr>
	</table>
			
		</table>
		<input type='submit' value='Save'/>
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

		$chkbox = $_POST['chk'];
		$mimonth = $_POST['month'];
		$miday = $_POST['day'];
		$miyear = $_POST['year'];
		$txtbox = $_POST['txt'];
 
		foreach($txtbox as $a => $b) {
			if($b == null) continue;
			$result = $wpdb -> query ($wpdb->prepare("INSERT INTO upsmart_milestones
									(wordpress_id, month, day, year, description)
									VALUES(%d,%d,%d,%d,%s)",
									array(
										get_current_user_id(),
										$b->month,
										$b->day,
										$b->year,
										$b->description
									)
			));

			if($result === false) return false;
			}
			return true;
		$value = $_POST['input1'];

		$sql = "INSERT INTO upsmart_test (Input) VALUES ('$value')";


	}

	function upsmart_create_milestones_form() {
		global $wpdb;
		$data = $wpdb->get_results($wpdb->prepare("SELECT * FROM upsmart_milestones WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		$months = array('January','February','March','April','May','June','July ','August','September','October','November','December');
		$out = <<<EOHTML
		<script src="js/modernizr-1.7.min.js"></script>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
		<script type="text/javascript" src="https://dl.dropboxusercontent.com/u/11993667/jquery.customSelect-master/jquery.customSelect.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
			    $('.styled').customSelect();
			    /* -OR- set a custom class name for the stylable element */
			    $('.mySelectBoxClass').customSelect({customClass:'mySelectBoxClass'});
			});
		</script>
		<form method='post'>
			<div class="subheading">
			<p>Below, share any milestone achievements of your company.  These could be first-hires, funding that you've received, revenue achievements, or anything else.</p>
			</div>
			<script type="text/javascript" src="http://upsmart.com/wp-content/plugins/upsmart_sitemanager/js/create_business_milestone.js"></script>
			<div id='milestones'>
			<div id='addRow'><input type="button" value="Add Row" onclick="addRow('dataTable')" /></div>
			<table id="dataTable" class= 'milestones' width="350px" border="1">
EOHTML;
		$count = 0;
		foreach($data as $milestone){
		$description_text = $milestone['description'];
		$out .= <<<EOHTML
			<tr>
				<td class= 'deleteRow'><input type="button" name="Delete" onClick="newDelete(this);"/> </td>
				<td class= 'milestone'>
					<div id='month' class= 'styled-select'>
					<select name="month[]">
EOHTML;
				foreach ($months as $month) {
					if($month == $milestone['month']){
						$out .= <<<EOHTML
						<option value="{$month}" selected>$month</option>
EOHTML;
					continue;
					}
					$out .= <<<EOHTML
					<option value="{$month}">$month</option>
EOHTML;
				}
				$out .= <<<EOHTML
				</select>
				<div></div>
				</div>
				<div id='day' class='styled-select'>
				<select name="day[]">
EOHTML;
				foreach (range(1, 31) as $day) {
					if($day == $milestone['day']){
						$out .= <<<EOHTML
						<option value="{$day}" selected>$day</option>
EOHTML;
					}
					$out .= <<<EOHTML
					<option value="{$day}">{$day}</option>
EOHTML;
				}
				$out .= <<<EOHTML
				</select>
				</div>
				<div id='year' class='styled-select'>
				<select name="year[]">
EOHTML;
				foreach (range(2013, 1990, -1) as $year) {
					if($year == $milestone['year']){
						$out .= <<<EOHTML
						<option value="{$year}" selected>$year</option>
EOHTML;
					}
					$out .= <<<EOHTML
					<option value="{$year}">{$year}</option>
EOHTML;
				}
				$out .= <<<EOHTML
					</select>
					</div>
				</td>
				<td><input type="text" name="txt[]" value='{$description_text}'/></td>
			</tr>
EOHTML;
		} /* END FOR EACH LOOP ON MILESTONES */
		
	$out .= <<<EOHTML
		</table>
		</div>
		<input type='submit' value='Save'/>
		</form>
EOHTML;
	return $out;
	}/* END FUNCTION */


	function upsmart_create_milestones_save() {
		global $wpdb;
		$i = 0;
		$wpid = get_current_user_id();
		mysql_query("DELETE FROM upsmart_milestones WHERE wordpress_id=".$wpid."") or die(mysql_error());
		foreach ($_POST["txt"] as $txt_value){

			$day = $_POST["day"][$i];
			$month = $_POST["month"][$i];
			$year = $_POST["year"][$i];
			$txt = $_POST["txt"][$i];
			
			$result = $wpdb->query($wpdb->prepare("REPLACE INTO upsmart_milestones
		                                      (wordpress_id,month,day,year,description)
		                                      VALUES(%d,%s,%d,%d,%s)",
		                                      array(
		                                          get_current_user_id(),
		                                          $month,
		                                          $day,
		                                          $year,
		                                          $txt,
		                                      )
			));
			echo "<br/>";
			echo "The variable I equals".$i;
			echo mysql_error();
			echo $month;
			$i++;

		}
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
	function upsmart_create_people_test(){
		global $wpdb;
		$data = json_decode(stripslashes($_POST['json']));
		echo $_POST['json']."<br/><br/><br/><br/>";
		echo "hello world! <br />";
		foreach($data as $p) {
			echo $p->fname."<br>/>";
			echo $p->lname."<br>/>";
			echo $p->title."<br>/>";
			echo $p->bio."<br>/>";
			echo $p->photo."<br>/>";
			echo $p->owner."<br>/>";
			echo $p->ownership_percentage."<br>/>";

		}
		return true;
	}

	function upsmart_create_people_save() {
		global $wpdb;
		
		$data = json_decode(stripslashes($_POST['json']));
		
		$wpdb->query($wpdb->prepare("DELETE FROM upsmart_people WHERE wordpress_id = %d",array(get_current_user_id())));
		
		foreach($data as $p) {
			if($p == null) continue;
			$result = $wpdb->query($wpdb->prepare("INSERT INTO upsmart_people
							(wordpress_id,fname,lname,title,bio,photo,owner,percent_owner,edu,skills,prof,awards,community,years,compensation)
							VALUES(%d,%s,%s,%s,%s,%s,%d,%d,%s,%s,%s,%s,%s,%d,%s)",
							array(
								get_current_user_id(),
								$p->fname,
								$p->lname,
								$p->title,
								$p->bio,
								$p->photo,
								$p->owner,
								$p->ownership_percentage,
								$p->edu,
								$p->skills,
								$p->prof,
								$p->awards,
								$p->community,
								$p->years,
								$p->compensation,
							)
			));
			echo mysql_error();

			if($result === false) return false;
		}
		echo $_POST['jason'];
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
	
	function upsmart_create_companydescription_form(){
		global $wpdb;
		$data = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_description WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		
		$media_url = admin_url("media-upload.php?type=image&amp;TB_iframe=true");
		
		$out = <<<EOHTML
		<form method='post' enctype='multipart/form-data'>
	<table class= 'wp-editor'>
		<tr><th colspan='2'><h3>Market Summary</h3></th></tr>
		<tr><td colspan='2'>Describe the nature of your business and list the marketplace needs that you are trying to satisfy.</td>
		<tr><td colspan='2'>
EOHTML;
	$settings = array(
	'textarea_rows' => 12,
	);
$out .= upsmart_get_editor($data['market_overview'],'marketoverview', $settings);
	$out .= <<<EOHTML
		</td></tr>
		
		<tr><th colspan='2'><h3>Value Proposition</h3></th></tr>
		<tr><td colspan='2'>Explain how your products and services meet these needs.</td></tr>
		<tr><td colspan='2'>
EOHTML;
	$settings = array(
	'textarea_rows' => 12,
	);
	$out .= upsmart_get_editor($data['value_prop'],'valueprop', $settings);
	$out .= <<<EOHTML
		</td></tr>
		
		<tr><th colspan='2'><h3>Target Market</h3></th></tr>
		<tr><td colspan='2'>List the specific consumers, organizations or businesses that your company serves or will serve.</td></tr>
		<tr><td colspan='2'>
EOHTML;
	$settings = array(
	'textarea_rows' => 12,
	);
	$out .= upsmart_get_editor($data['target_market'],'targetmarket', $settings);
	$out .= <<<EOHTML
		</td></tr>

		<tr><th colspan='2'><h3>Competitive Advantage</h3></th></tr>
		<tr><td colspan='2'>Explain the competitive advantages that you believe will make your business a success such as your location, expert team, opperational efficiancy, or ability to your customers.</td></tr>
		<tr><td colspan='2'>
EOHTML;
	$settings = array(
	'textarea_rows' => 12,
	);
	$out .= upsmart_get_editor($data['competitive_advantage'],'advantage', $settings);
	$out .= <<<EOHTML
		</td></tr>
	</table>
			
		</table>
	<br/>
	<input type='submit' value='Save'/>
	</form>
EOHTML;
		return $out;
	}

	function upsmart_create_companydescription_save() {
		global $wpdb;
		$result = $wpdb->query($wpdb->prepare("REPLACE INTO upsmart_description
		                                      (wordpress_id,yearsactive,market_overview,value_prop,target_market,competitive_advantage)
		                                      VALUES(%d,%d,%s,%s,%s,%s)",
		                                      array(
		                                          get_current_user_id(),
		                                          $_POST['yearsactive'],
		                                          $_POST['marketoverview'],
		                                          $_POST['valueprop'],
		                                          $_POST['targetmarket'],
		                                          $_POST['advantage'],
		                                      )
		));
		
		if($result === false) return false;
		return true;
	}

	function upsmart_create_financial_form() {
		global $wpdb;
		$data = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_disclaimer WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		
			if($data['financial'] != 1){
			return <<<EOHTML
			<form method='post'>
			<table>
				<tr><th><h4>By proceeding, you declare all information that you provide to be true, to the best of your knowledge, under the penalty of purjury.</h4></th></tr><tr><td><input type=checkbox name='financial_disclaimer' value='1'/>Yes, I understand.</td></tr>
			</table>
			<input type='submit' value='Save'/>
			</form>
EOHTML;
			}else{
			$redirect=home_url('create/10');
			if (isset($_POST['button'])){
			wp_redirect(home_url('create/10'));
			}
			return <<<EOHTML
			<form method='post'>
			<div style="display:none"><input type=checkbox name='financial_disclaimer' value='1' checked="checked"/></div>
			<table>
				<tr><th><h4>By proceeding, you declare all information that you provide to be true, to the best of your knowledge, under the penalty of purjury.</h4></th></tr><tr><td><input type=checkbox name='fin_disclaimer' value='1' checked="checked" disabled/>Yes, I understand.</td></tr>
			</table>
			<button name="button">Next</button>
			</form>
EOHTML;
			}
	}

	function upsmart_create_financial_save() {
		global $wpdb;
		$result = $wpdb->query($wpdb->prepare("REPLACE INTO upsmart_disclaimer
		                                      (wordpress_id,financial)
		                                      VALUES(%d,%d)",
		                                      array(
		                                          get_current_user_id(),
		                                          $_POST['financial_disclaimer'],
		                                      )
		));
		
		if($result === false) return false;
		return true;
	}

	function upsmart_create_financial_applicant_form(){
		global $wpdb;
		$assets = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_personal_financial_assets WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		$liabilities = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_personal_financial_liabilities WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);

			return <<<EOHTML
		<script type="text/javascript" src="http://upsmart.com/wp-content/plugins/upsmart_sitemanager/js/create_finance.js"></script>
			<body onload="findTotalAssets();findTotalLiabilities();findGrandTotal();">
			<form name='assetsandliabilitiesform' method='post'>
			
			<table class='assetsandliabilities'>
				<tr><th class='tableheadder'><h3>Assets</h3></th>
				<tr><th colspan= '1' class='lineitem'>Cash - Total all checking accounts</th><td colspan= '1' class='numbers'>$<input type=textbox id='checking' name='checking' class='assets' value='{$assets['checking']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Cash - Total all savings accounts</th><td colspan= '1' class='numbers'>$<input type=textbox id='savings' name='savings' class='assets' value='{$assets['savings']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Certificates of deposit (total)</th><td colspan= '1' class='numbers'>$<input 1242type=textbox name='deposit_certificates' class='assets' value='{$assets['deposit_certificates']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Securities - stocks/bonds/mutual funds (total)</th><td colspan= '1' class='numbers'>$<input type=textbox name='securities' class='assets' value='{$assets['securities']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Notes & contracts receivable (total)</th><td colspan= '1' class='numbers'>$<input type=textbox name='notes_contracts' class='assets' value='{$assets['notes_contracts']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Life insurance (cash surrender value)</th><td colspan= '1' class='numbers'>$<input type=textbox name='life_insurance' class='assets' value='{$assets['life_insurance']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Personal property total value <br/><span class='smaller'>(eg. autos, jewelry, etc.)</span></th><td colspan= '1' class='numbers'>$<input type=textbox name='personal_property' class='assets' value='{$assets['personal_property']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Retirement Funds <br/><span class='smaller'>(eg. IRAs, 401K)</span></th><td colspan= '1' class='numbers'>$<input type=textbox name='retirement' class='assets' value='{$assets['retirement']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Real estate total (market value)</th><td colspan= '1' class='numbers'>$<input type=textbox name='real_estate' class='assets' value='{$assets['real_estate']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th class='othervalue lineitem'>Other Assets (total)</th><td class='othervalue numbers'>$<input type=textbox class='assets' name='other_value'value='{$assets['other_value']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th class='listother'>*Please list assets totaled</th><td class='listother numbers'><input type=textbox name='other_specify'class='listother' value='{$assets['other_specify']}'/></td></tr>
				<tr><th colspan= '1' class='total'><h4>Assets Subtotal:</h4></th><td colspan= '1' class='numbers total'>$<input type="text" name="total_assets" value='{$assets['total_assets']}' id="total_assets" class:'subtotal' disabled onChange="findGrandTotal()"/></td></tr>
			
				<tr><th class='tableheadder liabilitiesheadder'><h3>Liabilities</h3></th></tr>
			
				<tr><th colspan= '1' class='lineitem'>Accounts Payable (total)</th><td class= 'numbers'>$<input type=textbox name='accounts_payable' class='liabilities' value='{$liabilities['accounts_payable']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Short-term loans (total)</th><td class= 'numbers'>$<input type=textbox name='short_term_loans' class='liabilities' value='{$liabilities['short_term_loans']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Income taxes payable (total)</th><td class= 'numbers'>$<input type=textbox name='income_taxes' class='liabilities' value='{$liabilities['income_taxes']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Long-Term debt total <br/><span class='smaller'>(eg. student loans)</span></th><td class= 'numbers'>$<input type=textbox name='long_term_debt' class='liabilities' value='{$liabilities['long_term_debt']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Deferred income tax (total)</th><td class= 'numbers'>$<input type=textbox name='deferred_income_tax' class='liabilities' value='{$liabilities['deferred_income_tax']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th class='othervalue lineitem'>Other Liabilities (total)</th><td class='othervalue numbers'>$<input type=textbox name='other_liabilities_value' class='liabilities' value='{$liabilities['other_liabilities_value']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th class='listother'>*Please list liabilities totaled</th><td class='listother numbers' ><input type=textbox name='other_liabilities_specify' class='listother' value='{$liabilities['other_liabilities_specify']}'/></td></tr>
				<tr><th colspan= '1' class='total'><h4>Liabilities Subtotal:</h4></th><td class='total numbers'>$<input type="text" name="total_liabilities" id="total_liabilities" class:'subtotal' disabled onChange="findGrandTotal()"/></td></tr>
				
				<tr><th class='total'><h4>Net:</h4></th><td class='total numbers'>$<input type="text" name="grand_total" id='grand_total' disabled/></td></tr>
				
				<tr class='submit'><td colspan='2'><input type='submit' value='Save'/></td></tr>

			</table>
			</form>
			</body>
EOHTML;
	}

	function upsmart_create_financial_applicant_save() {
		global $wpdb;
		$result_assets = $wpdb->query($wpdb->prepare("REPLACE INTO upsmart_personal_financial_assets
		                                      (wordpress_id,checking,savings,deposit_certificates,securities,notes_contracts,life_insurance,personal_property,retirement,real_estate,other_specify,other_value,total_assets)
		                                      VALUES(%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%s,%d,%d)",
		                                      array(
		                                          get_current_user_id(),
		                                          $_POST['checking'],
		                                          $_POST['savings'],
		                                          $_POST['deposit_certificates'],
		                                          $_POST['securities'],
		                                          $_POST['notes_contracts'],
		                                          $_POST['life_insurance'],
		                                          $_POST['personal_property'],
		                                          $_POST['retirement'],
		                                          $_POST['real_estate'],
		                                          $_POST['other_specify'],
		                                          $_POST['other_value'],
		                                          $_POST['total_assets'],
		                                      )
		));
		$result_liabilities = $wpdb->query($wpdb->prepare("REPLACE INTO upsmart_personal_financial_liabilities
		                                      (wordpress_id,accounts_payable,short_term_loans,income_taxes,long_term_debt,deferred_income_tax,other_liabilities_specify,other_liabilities_value,total_liabilities)
		                                      VALUES(%d,%d,%d,%d,%d,%d,%s,%d,%d)",
		                                      array(
		                                          get_current_user_id(),
		                                          $_POST['accounts_payable'],
		                                          $_POST['short_term_loans'],
		                                          $_POST['income_taxes'],
		                                          $_POST['long_term_debt'],
		                                          $_POST['deferred_income_tax'],
		                                          $_POST['other_liabilities_specify'],
		                                          $_POST['other_liabilities_value'],
		                                          $_POST['total_liabilities'],
		                                      )
		));
		
		if($result === false) return false;
		return true;
	}
	
	function upsmart_create_financial_business_form(){
		global $wpdb;
		$assets = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_business_financial_assets WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);
		$liabilities = $wpdb->get_row($wpdb->prepare("SELECT * FROM upsmart_business_financial_liabilities WHERE wordpress_id=%d",get_current_user_id()),ARRAY_A);

			return <<<EOHTML
		<script type="text/javascript" src="http://upsmart.com/wp-content/plugins/upsmart_sitemanager/js/create_finance.js"></script>
			<body onload="findTotalAssets();findTotalLiabilities();findGrandTotal();">
			<form name= 'assetsandliabilitiesform' method='post'>
			<table class='assetsandliabilities'>
				<tr><th class='tableheadder'><h3>Assets</h3></th>
				<tr><th colspan= '1' class='lineitem'>Cash - Total all checking accounts</th><td colspan= '1' class='numbers'>$<input type=textbox id='checking" name='checking' class='assets' value='{$assets['checking']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Cash - Total all savings accounts</th><td colspan= '1' class='numbers'>$<input type=textbox id='savings' name='savings' class='assets' value='{$assets['savings']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Inventory</th><td colspan= '1' class='numbers'>$<input type=textbox name='inventory' class='assets' value='{$assets['inventory']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Certificates of deposit (total)</th><td colspan= '1' class='numbers'>$<input type=textbox name='deposit_certificates' class='assets' value='{$assets['deposit_certificates']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Securities (total)</th><td colspan= '1' class='numbers'>$<input type=textbox name='securities' class='assets' value='{$assets['securities']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Notes & contracts receivable (total)</th><td colspan= '1' class='numbers'>$<input type=textbox name='notes_contracts' class='assets' value='{$assets['notes_contracts']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Property total value <br/><span class='smaller'>(eg. autos, plant equipment etc.)</span></th><td colspan= '1' class='numbers'>$<input type=textbox name='personal_property' class='assets' value='{$assets['personal_property']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Real estate total (market value)</th><td colspan= '1' class='numbers'>$<input type=textbox name='real_estate' class='assets' value='{$assets['real_estate']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th class='lineitem'>Other Assets (total)</th><td colspan= '1' class='numbers'>$<input type=textbox class='assets' name='other_value'value='{$assets['other_value']}' onChange="findTotalAssets()"/></td></tr>
				<tr><th class ='listother'>*Please list assets totaled</th><td class='numbers'><input type=textbox name='other_specify' value='{$assets['other_specify']}'/></td></tr>
				<tr'><th colspan= '1' class='total'><h4>Assets Subtotal:</h4></th><td colspan= '1' class='numbers total'>$<input type="text" name="total_assets" value='{$assets['total_assets']}' id="total_assets" disabled onChange="findGrandTotal"/></td></tr>

				<tr><th class= 'tableheadder liabilitiesheadder'><h3>Liabilities</h3></th></tr>

				<tr><th colspan= '1' class='lineitem'>Accounts Payable (total)</th><td colspan= '1' class='numbers'>$<input type=textbox name='accounts_payable' class='liabilities' value='{$liabilities['accounts_payable']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Short-term loans (total)</th><td colspan= '1' class='numbers'>$<input type=textbox name='short_term_loans' class='liabilities' value='{$liabilities['short_term_loans']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Income taxes payable (total)</th><td colspan= '1' class='numbers'>$<input type=textbox name='income_taxes' class='liabilities' value='{$liabilities['income_taxes']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Accrued salaries and wages <br/><span class='smaller'>(ie. pay earned by employees but not yet received)</span></th><td colspan= '1' class='numbers'>$<input type=textbox name='accrued_salaries' class='liabilities' value='{$liabilities['accrued_salaries']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Unearned revenue <br/><span class= 'smaller'>(ie. total of all payments made in advance for work that has not yet been delivered)</span></th><td colspan= '1' class='numbers'>$<input type=textbox name='unearned_revenue' class='liabilities' value='{$liabilities['unearned_revenue']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Long-Term debt total</th><td colspan= '1' class='numbers'>$<input type=textbox name='long_term_debt' class='liabilities' value='{$liabilities['long_term_debt']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Deferred income tax (total)</th><td colspan= '1' class='numbers'>$<input type=textbox name='deferred_income_tax' class='liabilities' value='{$liabilities['deferred_income_tax']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Owner's Investment (total)</th><td colspan= '1' class='numbers'>$<input type=textbox name='owners_investment' class='liabilities' value='{$liabilities['owners_investment']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th colspan= '1' class='lineitem'>Retained Earnings (total)</th><td colspan= '1' class='numbers'>$<input type=textbox name='retained_earnings' class='retained_earnings' value='{$liabilities['retained_earnings']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th class='lineitem'>Other Liabilities (total)</th><td colspan= '1' class='numbers'>$<input type=textbox name='other_liabilities_value' class='liabilities' value='{$liabilities['other_liabilities_value']}' onChange="findTotalLiabilities()"/></td></tr>
				<tr><th class='listother'>*Please list liabilities totaled</th><td class= 'numbers listother'><input type=textbox name='other_liabilities_specify' value='{$liabilities['other_liabilities_specify']}'/></td></tr>
				<tr><th colspan= '1' class= 'total'><h4>Liabilities Subtotal:</h4></th><td colspan= '1' class='numbers total'>$<input type="text" name="total_liabilities" id="total_liabilities" disabled/ onChange="findGrandTotal()"></td></tr>

				<tr><th class='total'><h4>Net:</h4></th><td class='total numbers'>$<input type="text" name="grand_total" id='grand_total' disabled/></td></tr>
				
				<tr class='submit'><td colspan='1'><input type='submit' value='Save'/></td></tr>
			</table>
			</form>
			</body>
EOHTML;
	}

	function upsmart_create_financial_business_save() {
		global $wpdb;
		$result_assets = $wpdb->query($wpdb->prepare("REPLACE INTO upsmart_business_financial_assets
		                                      (wordpress_id,checking,savings,inventory,deposit_certificates,securities,notes_contracts,personal_property,real_estate,other_specify,other_value,total_assets)
		                                      VALUES(%d,%d,%d,%d,%d,%d,%d,%d,%d,%s,%d,%d)",
		                                      array(
		                                          get_current_user_id(),
		                                          $_POST['checking'],
		                                          $_POST['savings'],
		                                          $_POST['inventory'],
		                                          $_POST['deposit_certificates'],
		                                          $_POST['securities'],
		                                          $_POST['notes_contracts'],
		                                          $_POST['personal_property'],
		                                          $_POST['real_estate'],
		                                          $_POST['other_specify'],
		                                          $_POST['other_value'],
		                                          $_POST['total_assets'],
		                                      )
		));
		$result_liabilities = $wpdb->query($wpdb->prepare("REPLACE INTO upsmart_business_financial_liabilities
		                                      (wordpress_id,accounts_payable,short_term_loans,income_taxes,accrued_salaries,unearned_revenue,long_term_debt,deferred_income_tax,owners_investment,retained_earnings,other_liabilities_specify,other_liabilities_value,total_liabilities)
		                                      VALUES(%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%s,%d,%d)",
		                                      array(
		                                          get_current_user_id(),
		                                          $_POST['accounts_payable'],
		                                          $_POST['short_term_loans'],
		                                          $_POST['income_taxes'],
		                                          $_POST['accrued_salaries'],
		                                          $_POST['unearned_revenue'],
		                                          $_POST['long_term_debt'],
		                                          $_POST['deferred_income_tax'],
		                                          $_POST['owners_investment'],
		                                          $_POST['retained_earnings'],
		                                          $_POST['other_liabilities_specify'],
		                                          $_POST['other_liabilities_value'],
		                                          $_POST['total_liabilities'],
		                                      )
		));
		
		if($result === false) return false;
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
