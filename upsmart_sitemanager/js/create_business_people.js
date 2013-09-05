/**
  * @package UpSmart_SiteManager
  */

(function($) {
	if(typeof upsmart === "undefined") { upsmart = {} }
	upsmart.people = {
		people: [],
		pcounter: 0,
		acceptFileTypes: ["image/png","image/jpeg","image/jpg","image/gif"],
 
		init: function() {
			$("#new").click(upsmart.people.showAddDialog);
			$("#team .person").live("click",upsmart.people.showEditDialog);
			$("#form").submit(upsmart.people.prepareSubmit);
		},
 
		prepareSubmit: function() {
			$("#form input[name=json]").attr("value",JSON.stringify(upsmart.people.people));
		},
 
		createForm: function(n) {
			item = $("<table> \
				<tr><th>Name</th><td class='twoinput'><input name='pfname' placeholder='Jane'/><input name='plname' placeholder='Smith'/></tr> \
				<tr><th>Title</th><td><input name='ptitle' placeholder='Chief Executive Officer'/></tr> \
				<tr><th>Short Bio</th><td><textarea name='pbio'/></textarea></td></tr> \
				<tr><th>Photo</th><td><input id='photo_upload' name='photo'/> <input type='button' id='photo_button' value='Open Media Library'/></tr> \
				<tr><td colspan='2'>(Optional) Upload a photo of <acronym title='Replace this with their first name?'>this person</acronym>. The bigger the better&mdash;don't worry, we'll scale this down for you.</td></tr> \
				</table>\
				<br/>\
				<table>\
				<tr><th>Education</th><td><textarea name='pedu'/></textarea></td></tr> \
				<tr><th>Relevant Skills</th><td><textarea name='pskills'/></textarea></td></tr> \
				<tr><th>Professional Experience</th><td><textarea name='pprof'/></textarea></td></tr> \
				<tr><th>Awards & Recognition</th><td><textarea name='pawards'/></textarea></td></tr> \
				<tr><th>Community Involvement</th><td><textarea name='pcommunity'/></textarea></td></tr> \
				<tr><th>Years with the Company</th><td><input size='2' maxlength='2' name='pyears'/>years</td></tr>\
				<tr><th>Compensation Details</th><td><textarea name='pcompensation'/></textarea></td></tr> \
				</table>\
				<br/>\
				<table>\
				<tr><td id='ownershipquestion' colspan='2'>Does this person have an ownership stake?</td><td id='ownershipbox'><input type='checkbox' id='part_owner' name='owner' value='1'/>Yes</td></tr>\
				<tr><td id='ownershipperquestion' colspan='2'>What percentage does this person hold?</td><td id='ownershipperanswer'><input type='text' size='3' maxlength='3' id='ownership_percentage' name='ppercent'/>%</td></tr>\
			</table>");
			if(n < upsmart.people.people.length) {
				p = upsmart.people.people[n];
				item.find("input[name=pfname]").attr("value",p.fname);
				item.find("input[name=plname]").attr("value",p.lname);
				item.find("input[name=ptitle]").attr("value",p.title);
				item.find("textarea[name=pbio]").attr("value",p.bio);
				item.find("input[name=photo]").attr("value",p.photo);
				if(p.owner == 1) {item.find("input[name=owner]").attr("checked",true);} else {item.find("input[name=owner]").attr("checked",false);}
				item.find("input[name=ppercent]").attr("value",p.percent);
				item.find("textarea[name=pedu]").attr("value",p.edu);
				item.find("textarea[name=pskills]").attr("value",p.skills);
				item.find("textarea[name=pprof]").attr("value",p.prof);
				item.find("textarea[name=pawards]").attr("value",p.awards);
				item.find("textarea[name=pcommunity]").attr("value",p.community);
				item.find("input[name=pyears]").attr("value",p.years);
				item.find("textarea[name=pcompensation]").attr("value",p.compensation);
			}
			return item;
		},
		
		showAddDialog: function() {
			$("#dialog").data("person",upsmart.people.pcounter);
			$("#dialog").html(upsmart.people.createForm(upsmart.people.pcounter));
			$("#photo_button").click(open_media_library);
			upsmart.people.pcounter++;
			$("#dialog").dialog({
				width: 600,
				modal: true,
				buttons: {
					"Add": upsmart.people.addPerson,
					"Cancel": function() {
						$(this).dialog("close");
					}
				}
			});
		},
		showEditDialog: function() {
			pid = $(this).data("person");
			$("#dialog").data("person",pid);
			$("#dialog").html(upsmart.people.createForm(pid));
			$("#photo_button").click(open_media_library);
			$("#dialog").dialog({
				width: 600,
				modal: true,
				buttons: {
					"Save": upsmart.people.addPerson,
					"Cancel": function() {
						$(this).dialog("close");
					}
				}
			});
		},
		
		addPerson: function() {
			//Create a json object for this person.
			
			
			var person = {
				id: $("#dialog").data("person"),
				fname: $("#dialog input[name=pfname]").attr("value"),
				lname: $("#dialog input[name=plname]").attr("value"),
				title: $("#dialog input[name=ptitle]").attr("value"),
				bio:   $("#dialog textarea[name=pbio]").attr("value"),
				photo: $("#dialog input[name=photo]").attr("value"),
				percent: $("#dialog input[name=ppercent]").attr("value"),
				edu: $("#dialog textarea[name=pedu]").attr("value"),
				skills: $("#dialog textarea[name=pskills]").attr("value"),
				prof: $("#dialog textarea[name=pprof]").attr("value"),
 				awards: $("#dialog textarea[name=pawards]").attr("value"),
 				community: $("#dialog textarea[name=pcommunity]").attr("value"),
 				years: $("#dialog input[name=pyears]").attr("value"),
 				compensation: $("#dialog textarea[name=pcompensation]").attr("value"),
			}
			
			$(this).dialog("close");
			
			upsmart.people.finishAddPerson(person);
		},
		finishAddPerson: function(person) {
			upsmart.people.people[person.id] = person;
			if($("#person"+person.id).length == 0) {
				box = $("<div class='person'></div>").attr("id","person"+person.id);
				box.data("person",person.id);
				box.insertBefore($("#new"));
			} else {
				box = $("#person"+person.id);
				box.html("");
			}
			
			box.append($("<img/>").attr("src",person.photo));
			box.append($("<button/>").attr("class","remove-user-btn"));
			box.append($("<div/>").attr("class","label").html(person.fname+" "+person.lname));
			$('.remove-user-btn').bind('click', function(e) {
			    removePerson($(this).attr(person.id));

			    //Remove the containing div here as well, something like
			    $(this).parent().remove()
			});
		}
	}
	
	$(document).ready(upsmart.people.init);
}(jQuery));