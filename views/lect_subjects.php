<div class='lect-subjects'>
	<?php if($notice) 
		echo("$notice <br>"); ?>
	<form method="POST" action="lect_subjects.php">
		<?php
		foreach ($subjects as $id => $name)
		{
			echo "<div class = 'subject'> $name <input type='submit' name='del$id' value='Удалить'> </div>";
		}?>
		<div class = 'add-subject'>
		Добавить предмет: <br> 
			<select name = 'subject' size = '1' >
			<?php foreach ($all_subjects as $id => $name)
			{
				echo "<option value = " . $id . ">" . $name . " </option>";
			}?>
			</select>
			<input type='submit' name='add' value='Добавить'> 
		</div>";
	</form> 
</div> 
