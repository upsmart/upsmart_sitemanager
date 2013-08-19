<?php
	class default_people extends upsmart_template {
		static function getName() {return "people";}
		static function getTitle() {return "People";}
		static function getUsedData() {return array('people');}
		//The __toString function is where it gets applied.
		function __toString() {
			$out = '<div id="people">';
			
			foreach($this->data['people'] as $row) {
			$cpname = $row['fname'] . ' ' . $row['lname']; 
			$title = $row['title'];
			$bio = $row['bio'];
			$photo= $row['photo'];
			$email = 'Use company email';

			$out .= <<<EOHTML
			<div class="person-profile">
				<img src="$photo" alt="Photo of $cpname" title="$cpname"/>
				<div class="person-profile-content">
					<div class="person-profile-header">
						<span class="person-profile-name">$cpname</span>
					</div>
					<div class="person-profile-title">$title</div>
					<p class="person-profile-bio">$bio</p>
				</div>
			</div>
EOHTML;
		}
			$out .= "</div>";
			return $out;
		}
	}
