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
	}
);