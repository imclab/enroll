$(document).ready(

	function() {
		//Hides menu and shows dashboard module
		$(".dashboard").click(
			function(event) {
				$('#menu').toggle();
				$('#dashboard').toggle();
			}
		);
		//Hides menu and shows verify module
		$(".verify").click(
			function(event) {
				$('#menu').toggle();
				$('#verify').toggle();
			}
		);
		//Hides menu and shows rosters module
		$(".rosters").click(
			function(event) {
				$('#menu').toggle();
				$('#rosters').toggle();
			}
		);
		$("#ghostuserform").submit(function(event){
			event.preventDefault();
			$.post(
				'ghostuser.php',
				$("#ghostuserform").serialize(),
				function(data) {
					if(data=="admin"){
						// similar behavior as an HTTP redirect
						window.location.replace("index.php");
					}
					else if(data=="teacher"){
						// similar behavior as an HTTP redirect
						window.location.replace("../teacher/agenda.php");
					}
					else if(data=="student"){
						// similar behavior as an HTTP redirect
						window.location.replace("../xy.php");
					}
					else{
						// similar behavior as an HTTP redirect
						window.location.replace("index.php");
					}
				}
			);
		});
	}
);