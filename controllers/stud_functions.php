<?php
	function get_student($connection, $user_id)
	{
		$result = query_mysql($connection, "SELECT * FROM student WHERE id = '$user_id'");
		if ($result->num_rows)
			return $result->fetch_assoc();
		else
			die ("Такого студента нет");
	}

	function get_user($connection, $email)
	{
		$result = query_mysql($connection, "SELECT * FROM user WHERE email = '$email'");
		if ($result->num_rows)
		{
			return $result->fetch_assoc();
		}
		else
			die ("Пользователь с таким e-mail не найден");
	}
?>