<?php
function show_error_message($message = "Произошла ошибка.")
{
	die (" <div class = 'error'>
		$message </div>
		</main> </body> </html> ");
}
?>