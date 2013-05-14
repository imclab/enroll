$(document).ready(
	function() {
		$(".remove_assignment").click(
			function(event) {
				$("#tile" + event.target.id).toggle();
				$.post(
					'remove.php',
					$("#remove" + event.target.id).serialize(),
					function(data) {
						//$("#status" + event.target.id).append(data);
					}
				);
				setTimeout(function() {
					location.reload(true);
				});
			}
		);
	}
);

function enroll(clicked_id){
	$("#enrollbutton" + clicked_id).toggle();
	$.post(
		'enroll.php',
		$("#enroll" + clicked_id).serialize(),
		function(data) {
			$("#status" + clicked_id).append(data);
		}
	);
	setTimeout(function() {
		location.reload(true);
	}, 1500);
}