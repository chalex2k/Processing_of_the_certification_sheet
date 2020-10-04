<script>
function getdetails(){
	let a1 = [];
	let a2 = [];
	let a3 = [];
	console.log(Number(document.getElementById('count_of_students').value));
	for(let i = 0; i < Number(document.getElementById('count_of_students').value); i++){
    //a1[i] = $('input[name="att1"]')[i].val();
	a1.push(document.getElementsByName('att1')[i].value);
	a2.push(document.getElementsByName('att2')[i].value);
	a3.push(document.getElementsByName('att3')[i].value);
	}
    $.ajax({
        type: "POST",
        url: "lect_ved.php",
        data: {save:true, att1:a1, att2:a2, att3:a3}
    }).done(function(result)
        {
            //$("#msg").html( " Request is " + result );
        });
}

function readCookie(name) {
	var name_cook = name+"=";
	var spl = document.cookie.split(";");
	
	for(var i=0; i<spl.length; i++) {
		var c = spl[i];
		while(c.charAt(0) == " ") {
			c = c.substring(1, c.length);
		}
		if(c.indexOf(name_cook) == 0) {	
			return c.substring(name_cook.length, c.length);
		}
	}
}

function getexcel(){
	let a1 = [];
	let a2 = [];
	let a3 = [];
	console.log(Number(document.getElementById('count_of_students').value));
	for(let i = 0; i < Number(document.getElementById('count_of_students').value); i++){
    //a1[i] = $('input[name="att1"]')[i].val();
	a1.push(document.getElementsByName('att1')[i].value);
	a2.push(document.getElementsByName('att2')[i].value);
	a3.push(document.getElementsByName('att3')[i].value);
	}
    $.ajax({
        type: "POST",
        url: "lect_ved.php",
        data: {load:true, att1:a1, att2:a2, att3:a3}
    }).done(function(result)
        {
            //$("#msg").html( " Request is " + result );
	    //console.log(readCookie("fn"));
	var pdflink = readCookie("fn");
	var link = document.createElement('a');
	//pdflink - путь к файлу		
	link.setAttribute('href', pdflink); 
	//pdfname - имя файла для загрузки (как он будет называться у посетителя)
	link.setAttribute('download', "ved.xls");
	link.setAttribute('target','_blank');
	link.style.display = 'none';
	document.body.appendChild(link); 
	link.click(); 
	document.body.removeChild(link);
        });
}
</script>

<div class = 'lect-ved'>
	<div class='lect-ved-item'>
		<div class='ved-form'>
			<form method="POST" action="lect_ved.php">
				<div class = 'ved-form-item'>
				Семестр:
				</div>
				<div class = 'ved-form-item'>
					<select name = 'semester' size = '1'>
						<?php
						for ($i = 1; $i <= 9; $i++)
							echo("<option " . ($i==$semester? " selected " : " ") . " value = '$i'> $i </option>");
						?>
					</select>
				</div>
				<div class = 'ved-form-item'>
				Группа:
				</div>
				<div class = 'ved-form-item'>
					<select name = 'group' size = '1' >
						<?php
						for ($i = 1; $i <= 9; $i++)
							echo("<option " . ($i==$group? " selected " : " ") . " value = '$i'> $i </option>");
						?>
					</select>
				</div>
				<div class = 'ved-form-item'>
				Предмет:
				</div>
				<div class = 'ved-form-item'>
					<select name = 'subject' size = '1' >
					<?php foreach ($subjects as $key => $value)
					{
						echo "<option " ;
						if($subject == $value)
							echo "selected ";
						echo "value = " . $key . ">" . "$value" . " </option>";
					} ?>
					</select>
				</div>
				<div class = "ved-form-item">
					<button type="submit" name="ok" value="Ok">Ok</button>
				</div>
			</form>
		</div>
	</div>

	
	<div class='lect-ved-item2'>
		<?php if($table)
	{
		$cnt = count($list);
	echo ("<form method='POST' action='lect_ved.php'>
	<input type='hidden' name='count_of_students' id='count_of_students' value='$cnt'>
		<table class='ved'>");
	echo '<tr id="hat"><th id="subject">Студент</th><th>1</th><th>2</th><th>3</th></tr>';
	foreach ($list as $row) // если не все оценки стоят?
	{
		echo '<tr> <td id="subject">' . $row[0] -> initials . '</td>
				   <td><input type="text" class="input-mark" name="att1" value="' . $row[1] -> value . '"></td>
				   <td><input type="text" class="input-mark" name="att2" value="' . $row[2] -> value . '"></td>
				   <td><input type="text" class="input-mark" name="att3" value="' . $row[3] -> value . '"></td>
			 </tr>';		}	
	echo '
	</table>
	
	 </form>
	<div class="input-form">

	<input type="button" class = "save" onClick = "getdetails()"  name="save" value="Сохранить ведомость "/>
	<input type="button" class = "lect-ved-buttons-item" onClick = "getexcel()" name="load" value="Экспорт в excel"></button>   </div>    
	</div>  
	   </div> </div>';

	echo '<div class= "input-form">';
	echo ('<!-- Тип кодирования данных, enctype, ДОЛЖЕН БЫТЬ указан ИМЕННО так -->
<form enctype="multipart/form-data" action="lect_ved.php" method="POST">
    <!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла -->
    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
    <!-- Название элемента input определяет имя в массиве $_FILES -->
    Загрузить из файла excel: <input name="user_file" type="file" />
    <input type="submit" name = "send" value="Отправить файл" />
</form>');
echo '<div id="msg"></div>';
	echo "</div>";
	echo "</div>";
	}
	else{echo "<table class='ved'><tr> <td colspan='4'>Заполните форму справа</td> </tr></table>";}
	if($send)
	{
		echo "Уведомление: оценки установлены";
	}
	
	if($save)
	{
		echo "Уведомление: Данные сохранены.";
	}
	
 ?>
 
 </div>