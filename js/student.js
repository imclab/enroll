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
function expand_description(clicked_id){
	$("#ellipses" + clicked_id).toggle();
	$("#description" + clicked_id).toggle();
}
/**
function enroll(clicked_id){
	$("#enrollbutton" + clicked_id).toggle();
	$("#status" + clicked_id).append("<em>Please wait...</em>");
	$.post(
		'enroll.php',
		$("#enroll" + clicked_id).serialize(),
		function(data) {
		}
	);
	setTimeout(function() {
		location.reload(true);
	}, 1500);
}**/
function ghost_user(username,role){
	$.post(
		'admin/ghostuser.php',
		{ ghostuser: username, ghostrole: role },
		function(data) {
			if(role=="admin"){
				// similar behavior as an HTTP redirect
				window.location.replace("admin/index.php");
			}
			else if(role=="teacher"){
				// similar behavior as an HTTP redirect
				window.location.replace("teacher/agenda.php");
			}
			else if(role=="student"){
				// similar behavior as an HTTP redirect
				window.location.replace("xy.php");
			}
		}
	);
}