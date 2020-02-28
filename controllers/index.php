<?php
require_once "render.php";
require_once "start.php";
try {
    switch ($who)
    {
        case 'guest': $redirect_url = ''; break;
        case 'student': $redirect_url = "stud_ved.php"; break;
        case 'lecturer': $redirect_url = "lect_ved.php"; break;
        default: $redirect_url = "index.php";
    }
    header('HTTP/1.1 200 OK');
    if ($redirect_url)
        header("Location: $redirect_url");

    return render('index',
        [   'title' => 'Главная страница',
            'userstr' => $userstr ]
    );
}
catch (Exception $exc) {
    return render('error', []);
}







?>