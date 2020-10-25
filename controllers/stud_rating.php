<?php
	$title = "Рейтинг";
	require_once 'header_stud.php';
	require_once '../service/login.php';
	require_once '../service/functions.php';
	require_once 'stud_functions.php';
	require_once '../service/check_access.php';

	$subject = $group = '';
    if (isset($user_email) and check_role($connection, $user_email) == 'student')
    {
        $user = get_user($connection, $user_email);
        $student = get_student($connection, $user['id']);
        $semester = $student['semester'];
    }
    else
    {
        header('Location: index.php');
    }

    function calculate_avg_mark($marks) {
        $sum = 0; $i = 0.0;
        do {
            $sum += $marks["$i"];
            $i += 1;
        } while (isset($marks["$i"]));
        if ($i)
            return $sum / $i;
        else return 0;
    }
?>

<body>
    <div class="settings">
        <form action="stud_rating.php" method="post">
            <div class="rating-settings">
                <select name="group" size="1">
                    <option selected>Группа..</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select>
            </div>
            <?php
                $subjects = query_mysql($connection, "SELECT * FROM subject 
                                                        JOIN subject_semester ON subject_semester.subject_id = subject.id 
                                                        WHERE subject_semester.semester = '$semester'");
            ?>
            <div class="rating-settings">
                <select name="subject" size="1">
                    <option selected>Предмет...</option>
                    <?php
                    if (isset($subjects)) {
                        foreach ($subjects as $subject)
                        {
                            echo "<option value='" . $subject['id'] . "'>" . $subject['name'] . "</option>";
                        }
                    }
                    else echo "<option>Предметы не найдены</option>";
                    ?>
                </select>
            </div>
            <div class="rating-settings">
                <button type="submit">Посмотреть результаты</button>
            </div>
        </form>
    </div>
    <div id="table-rating">
        <table class="ved">
            <?php
                if (isset($_POST['group']) && isset($_POST['subject']))
                {
                    $subject = $_POST['subject'];
                    $group = $_POST['group'];

                    $table = query_mysql($connection, "select distinct stud.stud_id, stud.student as 'student',
                                                        att1.mark as 'att1', att2.mark as 'att2', att3.mark as 'att3'                                                    
                                                        from
                                                        (select concat_ws(' ', u.surname, u.name, u.middle_name) as 'student',
                                                                u.id as 'stud_id'
                                                        from user u, student s
                                                        where s._group = '$group'
                                                        and s.semester = '$semester'
                                                        and u.id = s.id) as stud
                                                        left join
                                                        (select m1.mark as 'mark', s1.id as 'id'
                                                        from student s1, mark m1
                                                        where m1.student_id = s1.id
                                                        and m1.subject_id = '$subject'
                                                        and m1.attestation_number = '1') as att1 on att1.id = stud.stud_id
                                                        left outer join
                                                        (select m2.mark as 'mark', s2.id as 'id'
                                                        from student s2, mark m2
                                                        where m2.student_id = s2.id
                                                        and m2.subject_id = '$subject'
                                                        and m2.attestation_number = '2') as att2 on att2.id = stud.stud_id
                                                        left outer join
                                                        (select m3.mark as 'mark', s3.id as 'id'
                                                        from user u3, student s3, mark m3
                                                        where m3.student_id = s3.id
                                                        and m3.subject_id = '$subject'
                                                        and m3.attestation_number = '3') as att3 on att3.id = stud.stud_id
                                                        order by    (att1.mark + att2.mark + att3.mark) desc,
                                                                    (att1.mark + att2.mark) desc,
                                                                    (att1) desc
                                                        ");
                    $group_rating = query_mysql($connection, "select avg(m.mark) as 'gr_mark'
                                                        from mark m, student s
                                                        where s._group = '$group'
                                                        and s.semester = '$semester'
                                                        and m.student_id = s.id
                                                        and m.subject_id = '$subject'");
                    if ($table->num_rows) {
                        echo "<tr id=\"hat\"><th>Студент</th><th>1</th><th>2</th><th>3</th><th>Средний балл</th></tr>";
                        while ($row = $table->fetch_assoc()) {
                            echo "<tr>";
                                if (isset($row['student'])) {
                                    echo "<td>" . $row['student'] . "</td>";
                                }
                                else echo "<td></td>";
                                if (isset($row['att1'])) {
                                    echo "<td>" . $row['att1'] . "</td>";
                                }
                                else echo "<td></td>";
                                if (isset($row['att2'])) {
                                    echo "<td>" . $row['att2'] . "</td>";
                                }
                                else echo "<td></td>";
                                if (isset($row['att3'])) {
                                    echo "<td>" . $row['att3'] . "</td>";
                                }
                                else echo "<td></td>";
                                if ($avg_mark = calculate_avg_mark(array($row['att1'], $row['att2'], $row['att3']))) {
                                    echo "<td>" . $avg_mark . "</td>";
                                }
                                else echo "<td></td>";
                            echo "</tr>";
                        }

                    }
                    else echo "<td colspan='5'>Нет данных</td>";
                    if ($group_rating->num_rows) {
                        $row = $group_rating->fetch_assoc();
                        if ($row['gr_mark']) {
                            echo "<tr id='group-mark'>";
                            echo "<td colspan='4'>Средний балл группы: </td>";
                            echo "<td>" . $row['gr_mark'] . "</td>";
                            echo "</tr";
                        }
                    }
                }
            ?>
        </table>
    </div>
</body>
<script>
    function showRatingTable() {
    }
</script>


