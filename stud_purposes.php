<?php
	$title = "Цели";
	require_once 'header_stud.php';
	require_once 'stud_functions.php';
	require_once 'check_access.php';

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

    $result = query_mysql($connection, "select subjects.subject as 'subject', exp_marks.exp_mark as 'exp_mark', real_marks.real_mark as 'mark', real_marks.student_id as 'student'
                                                from 
                                                (select s.name as 'subject', s.id as 'subject_id'
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
                                                and real_marks.student_id = exp_marks.student_id
                                              ");
?>

<body>
    <div class="table">
        <table class="ved">
            <tr id="hat"><th id="subject">Предмет</th><th>Цель</th><th>Реальный балл</th></tr>
            <?php
            $result->data_seek(0);
            while ($row = $result->fetch_assoc())
            {
                echo '<tr>';
                echo '<td id="subject">' . $row['subject'] . '</td>';
                if (isset($row['exp_mark'])) {
                    echo '<td>' . $row['exp_mark'] . '</td>';
                }
                else {
                    echo '<td></td>';
                }
                if (isset($row['mark'])) {
                    echo '<td>' . $row['mark'] . '</td>';
                }
                else {
                    echo '<td></td>';
                }
            }
            ?>
        </table>
    </div>
</body>
