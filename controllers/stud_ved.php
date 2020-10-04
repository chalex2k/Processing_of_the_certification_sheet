<?php
    $title = "Мои баллы";
    require_once "header_stud.php";
    require_once "../service/functions.php";
    require_once "../service/check_access.php";

    if (isset($user_email) and check_role($connection, $user_email) == 'student') {
        $result = query_mysql($connection, "select att1.subject as 'subject', att1.mark as 'att1', att2.mark as 'att2', att3.mark as 'att3'
                                                from 
                                                (select s.name as 'subject', m.mark as 'mark'
                                                    from subject s, mark m, user u 
                                                    where u.email = '$user_email'
                                                    and u.id = m.student_id
                                                    and m.subject_id = s.id
                                                    and m.attestation_number = '1') att1
                                                left outer join
                                                (select s2.name as 'subject', m2.mark as 'mark'
                                                    from subject s2, mark m2, user u2
                                                    where u2.email = '$user_email'
                                                    and u2.id = m2.student_id
                                                    and m2.subject_id = s2.id
                                                    and m2.attestation_number = '2') att2 on att1.subject = att2.subject
                                                left outer join                                             
                                                (select s3.name as 'subject', m3.mark as 'mark'
                                                from subject s3, mark m3, user u3
                                                where u3.email = '$user_email'
                                                and u3.id = m3.student_id
                                                and m3.subject_id = s3.id
                                                and m3.attestation_number = '3') att3 on att2.subject = att3.subject");
    }
    else
    {
        header('Location: index.php');
    }


?>
<body>
    <div class="table">
        <table class="ved">
            <tr id="hat"><th id="subject">Предмет</th><th>1</th><th>2</th><th>3</th></tr>
            <?php
                $result->data_seek(0);
                while ($row = $result->fetch_assoc())
                {
                    echo '<tr>';
                    echo '<td id="subject">' . $row['subject'] . '</td>';
                    if (isset($row['att1'])) {
                        echo '<td>' . $row['att1'] . '</td>';
                    }
                    else {
                        echo '<td></td>';
                    }
                    if (isset($row['att2'])) {
                        echo '<td>' . $row['att2'] . '</td>';
                    }
                    else {
                        echo '<td></td>';
                    }
                    if (isset($row['att3'])) {
                        echo '<td>' . $row['att3'] . '</td>';
                    }
                    else {
                        echo '<td></td>';
                    }
                    echo '</tr>';
                }
            ?>
        </table>
    </div>
</body>
