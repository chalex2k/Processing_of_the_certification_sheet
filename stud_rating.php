<?php
	$title = "Рейтинг";
	require_once 'header_stud.php';
	require_once 'login.php';
	require_once 'functions.php';
	require_once 'stud_functions.php';
	require_once 'check_access.php';

	$subject = $group = '';
	$user = get_user($connection, $user_email);
    if ($who == 'student')
    {
        $student = get_student($connection, $user['id']);
        $semester = $student['semester'];
    }
    else
    {
        header('Location: index.php');
    }
?>
<script>
    function showRatingTable() {
        group = document.getElementById('gp');
        subject = document.getElementById('sb');
        if (group.options.selectedIndex > 0 && subject.options.selectedIndex > 0) {
            document.getElementById('table-rating').style.display = "block";
        }
        else {
            document.getElementById('table-rating').style.display = "none";
        }

    }
</script>
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
    <div class="table">
        <table class="ved">
            <tr id="hat"><th>Студент</th><th>1</th><th>2</th><th>3</th><th>Средний балл</th></tr>
            <?php
                if (isset($_POST['group']) && isset($_POST['subject']))
                {
                    $subject = $_POST['subject'];
                    $group = $_POST['group'];

                    $table = query_mysql($connection, "select att1.student as 'student',
                                                        att1.mark as 'att1', att2.mark as 'att2', att3.mark as 'att3',
                                                        (att1.mark + att2.mark + att3.mark)/3 as avg_mark                                                      
                                                        from
                                                        (select concat_ws(' ', u1.surname, u1.name, u1.middle_name) as 'student', 
                                                        ifnull(m1.mark, 0) as 'mark', u1.id as 'id'
                                                        from user u1, student s1, mark m1
                                                        where s1._group = '$group'
                                                        and u1.id = s1.id
                                                        and m1.student_id = s1.id
                                                        and m1.subject_id = '$subject'
                                                        and m1.attestation_number = '1') att1
                                                        left outer join
                                                        (select ifnull(m2.mark, 0) as 'mark', u2.id as 'id'
                                                        from user u2, student s2, mark m2
                                                        where s2._group = '$group'
                                                        and u2.id = s2.id
                                                        and m2.student_id = s2.id
                                                        and m2.subject_id = '$subject'
                                                        and m2.attestation_number = '2') att2 on att1.id = att2.id
                                                        left outer join
                                                        (select ifnull(m3.mark, 0) as 'mark', u3.id as 'id'
                                                        from user u3, student s3, mark m3
                                                        where s3._group = '$group'
                                                        and u3.id = s3.id
                                                        and m3.student_id = s3.id
                                                        and m3.subject_id = '$subject'
                                                        and m3.attestation_number = '3') att3 on att2.id = att3.id
                                                        order by avg_mark desc
                                                        ");
                    if ($table->num_rows) {
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
                                if (isset($row['avg_mark'])) {
                                    echo "<td>" . $row['avg_mark'] . "</td>";
                                }
                                else echo "<td></td>";
                            echo "</tr>";
                        }

                    }
                }
            ?>
        </table>
    </div>
</body>


