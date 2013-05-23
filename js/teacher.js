$(document).ready(

	function() {

		months = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
		currentMonth = new Date().getMonth() + 1;

		setXYPicker();

		$("#previous").click(
			function(event) {
				$(".assigndetails").hide();
				$(".selectedMonth").hide();
				currentMonth--;
				if (currentMonth < 1) {currentMonth=12;}
				setXYPicker();
			}
		);

		$("#next").click(
			function(event) {
				$(".assigndetails").hide();
				$(".selectedMonth").hide();
				currentMonth++;
				if (currentMonth == 13) {currentMonth=1;}
				setXYPicker();
			}
		);

		$(".selectedXYDate").change(
			function(event) {
				$(".assigndetails").hide();
				$("#column" + event.target.id).show();
				$("#xyid" + event.target.id).attr('value', $(this).val());
			}
		);

		//Hides assign module when user taps label
		$("#assignLabel").click(
			function(event) {
				$('#assign').toggle();
			}
		);
	}
);

//XY FUNCTIONS
//Scrolls through XY dates
function setXYPicker(){
	$("#month").text(months[currentMonth-1]);
	if (currentMonth == 1) { $("#January").show(); }
	if (currentMonth == 2) { $("#February").show(); }
	if (currentMonth == 3) { $("#March").show(); }
	if (currentMonth == 4) { $("#April").show(); }
	if (currentMonth == 5) { $("#May").show(); }
	if (currentMonth == 6) { $("#June").show(); }
	if (currentMonth == 7) { $("#July").show(); }
	if (currentMonth == 8) { $("#August").show(); }
	if (currentMonth == 9) { $("#September").show(); }
	if (currentMonth == 10) { $("#October").show(); }
	if (currentMonth == 11) { $("#November").show(); }
	if (currentMonth == 12) { $("#December").show(); }
}

//Assign XY details to specific date
function assign_xy(clicked_id){
	$("#selection" + clicked_id).validate({
		rules: {
			xy_id: {
				required: true,
				minlength: 1
			},
			blockpreference: {
				required: true,
				minlength: 1
			}
		},
		highlight: function(element) {
			$(element).closest('.control-group').removeClass('success').addClass('error');
		},
		success: function(element) {
			$(element).text('OK!').addClass('valid').closest('.control-group').removeClass('error').addClass('success');
		},
		submitHandler: function(form){
			$.post(
				'assign_xy.php',
				$("#selection" + clicked_id).serialize(),
				function(data) {
					$("#status" + clicked_id).append(data);
					$("#status" + clicked_id).fadeOut(3000, function(){
						$("#status" + clicked_id).empty().fadeIn();
					});
				}
			);
		}
	});
}

function edit_XY(clicked_id){
	$editXYButton = "#editXYButton" + clicked_id;
	$name = "#name" + clicked_id;
	$description = "#description" + clicked_id;
	$changeButton = "#changeButton" + clicked_id;
	$preferred_room = "#preferred_room" + clicked_id;
	$preferred_class_size = "#preferred_class_size" + clicked_id;
	$category = "#category" + clicked_id;
	$freshmen = "#freshmen" + clicked_id;
	$sophomores = "#sophomores" + clicked_id;
	$juniors = "#juniors" + clicked_id;
	$seniors = "#seniors" + clicked_id;
	$updateXYButton = "#updateXYButton" + clicked_id;
	$delete = "#delete" + clicked_id;
	//Hide the Edit Button
	$($editXYButton).toggle();
	//Make fields editable
	$($changeButton).toggle();
	$($name).removeAttr("disabled");
	$($description).removeAttr("disabled");
	$($preferred_room).removeAttr("disabled");
	$($preferred_class_size).removeAttr("disabled");
	$($category).removeAttr("disabled");
	$($freshmen).removeAttr("disabled");
	$($sophomores).removeAttr("disabled");
	$($juniors).removeAttr("disabled");
	$($seniors).removeAttr("disabled");
	$($delete).removeAttr("disabled");
	//Show the update button
	$($updateXYButton).toggle();
}

//COLLOQUIUM FUNCTIONS

//Enable options to edit existing colloquium
function edit_colloquium(clicked_id){
	$editColloquiumButton = "#editColloquiumButton" + clicked_id;
	$name = "#name" + clicked_id;
	$description = "#description" + clicked_id;
	$changeButton = "#changeButton" + clicked_id;
	$duration = "#duration" + clicked_id;
	$preferred_room = "#preferred_room" + clicked_id;
	$preferred_class_size = "#preferred_class_size" + clicked_id;
	$preferred_lunch_block = "#preferred_lunch_block" + clicked_id;
	$freshmen = "#freshmen" + clicked_id;
	$sophomores = "#sophomores" + clicked_id;
	$juniors = "#juniors" + clicked_id;
	$seniors = "#seniors" + clicked_id;
	$updateColloquiumButton = "#updateColloquiumButton" + clicked_id;
	$delete = "#delete" + clicked_id;
	//Hide the Edit Button
	$($editColloquiumButton).toggle();
	//Make fields editable
	$($changeButton).toggle();
	$($name).removeAttr("disabled");
	$($description).removeAttr("disabled");
	$($duration).removeAttr("disabled");
	$($preferred_room).removeAttr("disabled");
	$($preferred_class_size).removeAttr("disabled");
	$($preferred_lunch_block).removeAttr("disabled");
	$($freshmen).removeAttr("disabled");
	$($sophomores).removeAttr("disabled");
	$($juniors).removeAttr("disabled");
	$($seniors).removeAttr("disabled");
	$($delete).removeAttr("disabled");
	//Show the update button
	$($updateColloquiumButton).toggle();
}

//Assign colloquium details to specific semester
function assign_colloquium(clicked_id){
	$("#sem" + clicked_id + "Selection").validate({
		rules: {
			c_id: {
				required: true,
				minlength: 1
			},
			duration: {
				required: true,
				minlength: 1
			}
		},
		highlight: function(element) {
			$(element).closest('.control-group').removeClass('success').addClass('error');
		},
		success: function(element) {
			$(element).text('OK!').addClass('valid').closest('.control-group').removeClass('error').addClass('success');
		},
		submitHandler: function(form){
			$.post(
				'assign_colloquium.php',
				$("#sem" + clicked_id + "Selection").serialize(),
				function(data) {
					$("#sem" + clicked_id + "Status").append(data);
					setTimeout(function() {
							location.reload(true);
						}, 1000);
				}
			);
		}
	});
}
function ghost_user(username,role){
	$.post(
		'../admin/ghostuser.php',
		{ ghostuser: username, ghostrole: role },
		function(data) {
			if(role=="admin"){
				// similar behavior as an HTTP redirect
				window.location.replace("../admin/index.php");
			}
			else if(role=="teacher"){
				// similar behavior as an HTTP redirect
				window.location.replace("agenda.php");
			}
			else if(role=="student"){
				// similar behavior as an HTTP redirect
				window.location.replace("../xy.php");
			}
		}
	);
}