<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title> <?= $websiteName ?> </title>
	<link rel="stylesheet" href="style/style.css">
	<script src="js/jquery-3.4.1.min.js"></script>
	<script src="js/menu.js"></script>
	<script src="js/statistics_click.js"></script>
	<style>
		table.top-players {
			display: table;
			opacity: 1;
		}
		@media screen and (max-width: 600px) {
			table.top-players {
				display: none;
			}
		}
	</style>
</head>
<body>
	<?php include("static/header.php"); ?>

	<?php include("static/menu.php"); ?>

	<?php include("static/statistics.php"); ?>

	<main>
		<section class="victoryWrapper">
			<span>
				<span class="victory">Victory</span>
				<p class="victory-reason">
					<?php
						if(isset($_SESSION['victoryReason'])) {
							echo $_SESSION['victoryReason'];
						}
					?>
				</p><br>
			</span>

			<pre id="logs"><?php echo file_get_contents('logs/log_tour_history.txt'); ?></pre>
		</section>
	</main>

	<?php include("static/footer.php"); ?>

</body>
</html>