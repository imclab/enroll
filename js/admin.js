$(document).ready(

	function() {
		var $window = $(window);
		// side bar
		setTimeout(function () {
			$('.bs-docs-sidenav').affix({
				offset: {
					top: function () {
						return $window.width() <= 980 ? 290 : 210;
					}, bottom: 270
				}
			});
		}, 100);
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
					if(data=="student"){
						// similar behavior as an HTTP redirect
						window.location.replace("../xy.php");
					}
					else if(data=="admin"){
						// similar behavior as an HTTP redirect
						window.location.replace("index.php");
					}
					else if(data=="teacher"){
						// similar behavior as an HTTP redirect
						window.location.replace("../teacher/agenda.php");
					}
					else{
						// similar behavior as an HTTP redirect
						window.location.replace("index.php");
					}
				}
			);
		});
		$("#syncButton").click(function(event){
			event.preventDefault();
			$("#syncButton").toggle();
			$("#please_wait").toggle();
			$.post(
				'sync_users.php',
				$("#sync_users_form").serialize(),
				function(data) {
					$("#please_wait").toggle();
					$("#status_text").text(data);
					$("#status").toggle();
				}
			);
		});
	}
);