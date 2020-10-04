<?php
	$title = "Цели";
	require_once 'header_stud.php';
	require_once 'stud_functions.php';
	require_once '../service/check_access.php';

    if (isset($user_email) and check_role($connection, $user_email) == 'student') {
        $user = get_user($connection, $user_email);
        $student = get_student($connection, $user['id']);
        $semester = $student['semester'];
        $stud_id = $student['id'];
    }
    else
    {
        header('Location: index.php');
    }

    $result = query_mysql($connection, "select subjects.subject_id as 'subj_id', subjects.subject as 'subject', subjects.is_exam as 'is_exam', exp_marks.exp_mark as 'exp_mark', real_marks.real_mark as 'mark'
                                                from 
                                                (select s.name as 'subject', s.id as 'subject_id', s.mark as 'is_exam'
                                                from subject s, subject_semester ss
                                                where s.id = ss.subject_id
                                                and ss.semester = '$semester') subjects
                                                left outer join
                                                (select avg(rm.mark) as 'real_mark', rm.subject_id as 'subject_id', rm.student_id as 'student_id'
                                                from mark rm 
                                                where rm.student_id = '$stud_id'
                                                group by rm.subject_id) real_marks on real_marks.subject_id = subjects.subject_id
                                                left outer join 
                                                (select em.mark as 'exp_mark', em.subject_id as 'subject_id', em.student_id as 'student_id'
                                                from expected_mark em
                                                where em.student_id = '$stud_id') exp_marks on exp_marks.subject_id = subjects.subject_id
                                                order by subject
                                              ");
?>

<body onload="setCellColor()">
    <div class="table">
        <form action="stud_purposes.php" method="post">
            <table class="ved">
                    <tr id="hat"><th id="subject">Предмет</th><th>Цель</th><th>Средний балл</th></tr>
                    <?php
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc())
                    {
                        echo '<tr>';
                        echo '<td id="subject">' . $row['subject'] . '</td>';
                        if (isset($row['exp_mark'])) {
                            echo '<td><input id="' . $row['subj_id'] . '"class="input-mark ' . $row['is_exam'] . '" type="text" name="' . $row['subj_id'] . ' " value="' . $row['exp_mark'] . '"></td>';
                        }
                        else {
                            echo '<td><input id="' . $row['subj_id'] . '" class="input-mark ' . $row['is_exam'] . '" type="text" name="' . $row['subj_id'] . '"></td>';
                        }
                        if (isset($row['mark'])) {
                            echo '<td class="marks" id="' . $row['subj_id'] . '">' . $row['mark'] . '</td>';
                        }
                        else {
                            echo '<td class="marks" id="' . $row['subj_id'] . '"></td>';
                        }
                    }
                    if (isset($_POST['save-btn'])) {
                        print_r($_POST);
                        foreach ($_POST as $subj_id => $mark) {
                            if (!empty($mark) && $mark <= 50 && $mark > 0) {
                                $is_mark = query_mysql($connection, "select em.mark  as 'mark'
                                                                        from expected_mark em 
                                                                        where em.student_id = '$stud_id'
                                                                        and em.subject_id = '$subj_id'");
                                if ($is_mark->num_rows) {
                                    print_r($is_mark->num_rows);
                                    $row = $is_mark->fetch_assoc();
                                    print_r($row);
                                    if ($row['mark'] != $mark) {
                                        $update_mark = query_mysql($connection, "update expected_mark set mark = '$mark' 
                                                where student_id = '$stud_id'
                                                and subject_id = '$subj_id'");
                                    }
                                }
                                else {
                                    print_r($mark);
                                    $insert_mark = query_mysql($connection, "insert into expected_mark values(null, '$stud_id', '$subj_id', '$mark')");
                                }
                            }
                        }
                        header( "Location: stud_purposes.php");
                    }
                    ?>
            </table>
            <button class="save" type="submit" name="save-btn">Сохранить</button>
            <button class="save" type="button" onclick="setMinMarks()">Хочу стипендию</button>
        </form>
    </div>
</body>
<script>
    function setMinMarks() {
        let inputs = document.getElementsByTagName('input');
        for (let input of inputs) {
            if (input.value == "") {
                if (input.classList.contains('1')) {
                    input.value = "35";
                }
                else {
                    input.value = "25";
                }
            }
        }
    }
    function setCellColor() {
        let exp_marks = document.getElementsByClassName('input-mark');
        let marks = document.getElementsByClassName('marks');
        for (let i = 0; i < marks.length; i++) {
            if (marks[i].innerText != "") {
                if (marks[i].innerText <= exp_marks[i].value) {
                    marks[i].style.color = 'red';
                } else {
                    marks[i].style.color = 'green';
                }
            }
        }
    }
</script>
