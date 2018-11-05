<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>AI Trainer 💪🏽</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="container">
		<br>
		<div class="row">
			<div class="col text-center">
				<h1>AI Trainer 💪🏽</h1>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col text-center">
				<code>Big shout out to Myrtis Painter on her 106th bday, but celebrating the milestone at #TacoBell confirms two things: You don't care about seeing 107 and there is always an element of luck and randomness to longevity. #NachosRule</code>
			</div>
		</div>
		<br>
		<div class="row text-center">
			<div class="col"><button type="button" class="btn btn-success w-100 positive" data-toggle="button" aria-pressed="false" autocomplete="off">Positive (p)</button></div>
			<div class="col"><button type="button" class="btn btn-primary w-100 neutral" data-toggle="button" aria-pressed="false" autocomplete="off">Neutral (o)</button></div>
			<div class="col"><button type="button" class="btn btn-danger w-100 negative" data-toggle="button" aria-pressed="false" autocomplete="off">Negative (i)</button></div>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function() {

			function positive() {
				console.log('positive');
			}

			function neutral() {
				console.log('neutral');
			}

			function negative() {
				console.log('negative');
			}

			// handle keypress 
			$(document).keypress(function(e) {
				var sentiment;
				if(e.which == 112) {
					positive();
				}
				if (e.which == 111) {
					neutral();
				}
				if (e.which == 105) {
					negative();
				}
			});

			// handle button clicks
			$('.positive').on('click', function() {
				positive();
			});

			$('.neutral').on('click', function() {
				neutral();
			});

			$('.negative').on('click', function() {
				negative();
			});
		});
	</script>
</body>
</html>