<div class='lect-subjects'>
	<?php if($notice) 
		echo("$notice <br>"); ?>
	<form method="POST" action="lect_subjects.php">
		<?php
		foreach ($subjects as $id => $name)
		{
			echo "<div class = 'subject-item'>";
			echo "<div class = 'subject-line'>";
			echo "<div class = 'subject-name'> $name </div>";
			echo "<div class = 'subject-button'> <button type='submit' class = 'del' name='del$id' value='Удалить'> Удалить </button></div>";
			
			echo "</div>";
			echo "</div>";
		
		}?>
		
		<div class = 'subject-item'>
			<div class = 'subject-line'>
			
			<div class = 'subject-name'>  <select name = 'subject' size = '1' > 
			<?php foreach ($all_subjects as $id => $name)
			{
				echo "<option value = " . $id . ">" . $name . " </option>";
			}?>
			</select>
			</div>
			<div class = 'subject-button'> <button type='submit' class = 'del' name='add' value='Добавить'> Добавить </button> </div>
			</div>
		</div>
	</form> 
</div> 
