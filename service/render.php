<?php
 
function render($template,$vars = array()){
 
    // передаем функции имя шаблона
    // и список переменных для формирования представления.
 
    extract($vars);
 
    // так же функции можно передать несколько шаблонов
    if(is_array($template)){
 
        foreach($template as $k){
 
            $cl = strtolower(get_class($k));
            $$cl = $k;
 
            include "views/_$cl.php";
        }
 
    }
    else {
        include "../views/$template.php";
    }
}
?>