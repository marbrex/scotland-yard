<?php include('controllers/stat.php'); ?>
<table class="top-players">
	<thead>
		<tr>
			<th>Score</th>
			<th>Nickname</th>
		</tr>
	</thead>
	
	<tbody>
		<?php
			$nb = 0;
			while($row = mysqli_fetch_array($res)) {
				if ($nb < 12) {
					echo '<tr>';

					echo '<td>'.$row['scoreJ'].'</td>';
					echo '<td>'.$row['nicknameJ'].'</td>';

					echo '</tr>';

					++$nb;
				}
			}
		?>
	</tbody>
</table>