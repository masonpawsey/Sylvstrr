<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>AI Trainer 💪🏽</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

	<!-- jQuery -->
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<main role="main" class="container">
		<br>
		<div class="row">
			<div class="col text-center">
				<h1>AI Trainer 💪🏽</h1>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col text-center">
				<code data-id=""></code>
			</div>
		</div>
		<br>
		<div class="row text-center">
			<div class="col-4"><button type="button" class="btn btn-success w-100 positive" data-toggle="button" aria-pressed="false" autocomplete="off">Positive (p)</button></div>
			<div class="col-4"><button type="button" class="btn btn-primary w-100 neutral" data-toggle="button" aria-pressed="false" autocomplete="off">Neutral (o)</button></div>
			<div class="col-4"><button type="button" class="btn btn-danger w-100 negative" data-toggle="button" aria-pressed="false" autocomplete="off">Negative (i)</button></div>
		</div>
	</main>

	<footer class="footer d-sm-none d-md-block">
		<div class="container">
			<span class="col positive-stat text-success">Positive: </span>
			<span class="col neutral-stat text-primary">Neutral: </span>
			<span class="col negative-stat text-danger">Negative: </span>
			<span class="float-right" style="padding-right: 10px;"><a href="../">Try it</a></span>
			<span class="float-right" style="padding-right: 10px;"><a href="../corpus.csv">Corpus file</a></span>
			<span class="float-right remaining-stat" style="padding-right: 10px;"></span>
			<span class="float-right total-stat" style="padding-right: 10px;"></span>
		</div>
	</footer>

	<script type="text/javascript">
		$(document).ready(function() {
			function getStats() {
				$.ajax({
					url: 'stats.php',
					type: 'POST',
					data: {},
					success: function (result) {
						// console.log($.parseJSON(result)['positive']);
						$('.positive-stat').text('Positive: ' + $.parseJSON(result)['positive']);
						$('.neutral-stat').text('Neutral: ' + $.parseJSON(result)['neutral']);
						$('.negative-stat').text('Negative: ' + $.parseJSON(result)['negative']);
						$('.total-stat').text('Done: ' + $.parseJSON(result)['done']);
						$('.remaining-stat').text('Remaining: ' + $.parseJSON(result)['unknown']);
					}
				});
			}

			// Going to have some issues with the tweet being "in use" but never actually reported on... like when a Tweet gets loaded and the page is refreshed
			function newTweet() {
				$.ajax({
					url: 'get_tweets_for_trainer.php',
					type: 'POST',
					data: {},
					success: function (result) {
						$('code').html($.parseJSON(result)['text']);
						$('code').attr('data-id',$.parseJSON(result)['id']);
						getStats();
					}
				});
			}

			function positive() {
				console.log('positive');
				$.ajax({
					url: 'update_sentiment.php',
					type: 'POST',
					data: {id:$('code').attr('data-id'), sentiment:'positive'},
					success: function (result) {
						console.log(result);
						newTweet();
					}
				});
			}

			function neutral() {
				console.log('neutral');
				$.ajax({
					url: 'update_sentiment.php',
					type: 'POST',
					data: {id:$('code').attr('data-id'), sentiment:'neutral'},
					success: function (result) {
						console.log(result);
						newTweet();
					}
				});
			}

			function negative() {
				console.log('negative');
				$.ajax({
					url: 'update_sentiment.php',
					type: 'POST',
					data: {id:$('code').attr('data-id'), sentiment:'negative'},
					success: function (result) {
						console.log(result);
						newTweet();
					}
				});
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

			newTweet();
			getStats();

		});
	</script>
</body>
</html>