$(document).ready(

	function() {
		//IMAGE UPLOAD
		var preview = $('#preview'); //id of the preview image
		new AjaxUpload('imageUpload', {
			action: 'upload_image.php', //the php script that receives and saves the image
			name: 'image', //upload_image.php will find the image info in the variable $_FILES['image']
			onSubmit: function(file, extension) {
				preview.attr('src', '../img/loading.gif'); //replace the image SRC with an animated GIF with a 'loading...' message 
			},
			onComplete: function(file, response) {
				preview.load(function(){
					preview.unbind();
				});
				preview.attr('src', '../img/courses/' + response); //make the preview image display the uploaded file
				$('#uploadedImg').val(response); //drop the path to the file into the hidden field
			}
		});

		//XY FUNCTIONS

		$("#addXYForm").submit(
			function(event) {
				/* stop form from submitting normally */
				event.preventDefault();
				$.post(
					'insert_xy.php',
					$("#addXYForm").serialize(),
					function(data) {
						$("#status").append(data);
					});
					setTimeout(function() {
						location.reload(true);
					}, 1500);
				}
		);

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

		$("#assignLabel").click(
			function(event) {
				$('#assign').toggle();
			}
		);

		//COLLOQUIUM FUNCTIONS

		//Assigning colloquium to Semester 1
		$("#assignSem1ColButton").click(
			function(event) {
				$.post('assign_colloquium.php',
					$("#sem1Selection").serialize(),
					function(data) {
						$("#sem1Status").append(data).fadeOut(1500);
					}
				);
				setTimeout(function() {
					location.reload(true);
				}, 3000);
		});

		//Assigning colloquium to Semester 2
		$("#assignSem2ColButton").click(
			function(event) {
				$.post('assign_colloquium.php',
					$("#sem2Selection").serialize(),
					function(data) {
						$("#sem2Status").append(data).fadeOut(1500);
					}
				);
				setTimeout(function() {
					location.reload(true);
				}, 3000);
		});

		$("#editSem1ColAssnButton").click(
			function(event) {
				$('#editSem1ColAssnButton').hide();
				$('#sem1Colloquium').removeAttr("disabled");
				$('#sem1Duration').removeAttr("disabled");
				$('#sem1Notes').removeAttr("disabled");
				$('#updateSem1ColAssnButton').show();
			}
		);

		$("#editSem2ColAssnButton").click(
			function(event) {
				$('#editSem2ColAssnButton').hide();
				$('#sem2Colloquium').removeAttr("disabled");
				$('#sem2Notes').removeAttr("disabled");
				$('#updateSem2ColAssnButton').show();
			}
		);

		//Updating colloquium for Semester 1
		$("#updateSem1ColAssnButton").click(function(event) {
			/* stop form from submitting normally */
			$.post(
				'assign_colloquium.php',
				$("#sem1ExistingSelection").serialize(),
				function(data) {
					$("#sem1Status").append(data).fadeOut(1500);
				}
			);
			setTimeout(function() {
				location.reload(true);
			}, 3000);
		});

		//Updating colloquium for Semester 2
		$("#updateSem2ColAssnButton").click(
			function(event) {
				/* stop form from submitting normally */
				$.post(
					'assign_colloquium.php',
					$("#sem2ExistingSelection").serialize(),
					function(data) {
						$("#sem2Status").append(data).fadeOut(1500);
					}
				);
				setTimeout(function() {
					location.reload(true);
				}, 3000);
			}
		);



		//When user clicks submit button to add coloquium
		$("#addColloquium").submit(
			function(event) {
				/* stop form from submitting normally */
				event.preventDefault();
				$.post(
					'insert_colloquium.php',
					$("#addColloquiumForm").serialize(),
					function(data) {
						$("#status").append(data);
					}
				);
				setTimeout(function() {
					location.reload(true);
				}, 3000);
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

function update_XY(clicked_id){
	$.post('update_xy.php', $("#updateXY" + clicked_id).serialize(), function(data) {
		$("#status" + clicked_id).append(data);
	});
	setTimeout(function() {
		location.reload(true);
	}, 1500);
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

//When user clicks submit button to update colloquium
function update_colloquium(clicked_id){
	/* stop form from submitting normally */
	$statusID = "#status" + clicked_id;
	$.post(
		'update_colloquium.php',
		$("#updateColloquium" + clicked_id).serialize(),
		function(data) {
			$($statusID).append(data);
		}
	);
	setTimeout(function() {
		location.reload(true);
	}, 3000);
}