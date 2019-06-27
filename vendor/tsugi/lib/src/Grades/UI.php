<?php

namespace Tsugi\Grades;


use \Tsugi\UI\Table;
use \Tsugi\Core\LTIX;

class UI {
//TODO : Modify grade-details table to show uninitiated students in a link
//TODO : Fetch uninitiated students from student enrl table
//TODO : Need to make tables scrollable so that faculty can conveniently get to any assignment.

    public static function gradeTable($GRADE_DETAIL_CLASS) {
        global $CFG, $OUTPUT, $USER, $LINK;
        // Require CONTEXT, USER, and LINK
        $LAUNCH = LTIX::requireData();
        if ( ! $USER->instructor && ! $USER->ASC ) die("Requires instructor or ASC role");
        $p = $CFG->dbprefix;

        // Get basic grade data
        $query_parms = array(":LID" => $LINK->id);
        $orderfields =  array("R.updated_at", "displayname", "email");
        $searchfields = array();
        /*
        $sql =
            "SELECT R.user_id AS user_id, displayname, email,
                grade, note, R.updated_at AS last_updated
            FROM {$p}lti_result AS R
            JOIN {$p}lti_user AS U ON R.user_id = U.user_id
            WHERE R.link_id = :LID";*/

        // View
        $OUTPUT->header();
        $OUTPUT->bodyStart();
        $OUTPUT->flashMessages();
        //$OUTPUT->welcomeUserCourse();

        if ( isset($GRADE_DETAIL_CLASS) && is_object($GRADE_DETAIL_CLASS) ) {
            $detail = $GRADE_DETAIL_CLASS;
        } else {
            $detail = false;
        }

        // Create sql statements for each sub-category of grades.
        $sqlCompleted = "SELECT R.user_id AS user_id, displayname, email,
                grade, note, R.updated_at AS last_updated
            FROM {$p}lti_result AS R
            JOIN {$p}lti_user AS U ON R.user_id = U.user_id
            WHERE R.link_id = :LID AND grade =.5";

 $sqlIncomplete = "SELECT
                              U.displayname AS displayname,
                              U.email AS email,
                              R.grade AS grade,
                              (
                                    SELECT term_name 
                                    FROM fci_term t
                                    WHERE t.term_id=substring(R.sis_enrollment_id, -4, 4)
                                ) AS note,
                              R.updated_at AS last_updated
                                        FROM {$p}lti_result AS R
                                        JOIN {$p}lti_user AS U ON R.user_id = U.user_id
                                        WHERE R.link_id = :LID AND
                                        (grade IN ('',0) OR grade IS NULL) AND
                                        substring(R.sis_enrollment_id, -4, 4) IN (SELECT term_id
                                                                  FROM {$p}fci_term
                                                                  WHERE sysdate() BETWEEN term_start_dt AND term_end_dt)
                          UNION
                          SELECT
                                CONCAT(b.FIRST_NAME,' ',b.LAST_NAME) AS displayname,
                                lcase(b.EMAIL_ADDR) AS email,
                                NULL AS grade,
                                (
                                    SELECT term_name 
                                    FROM fci_term t
                                    WHERE t.term_id=substring(e.EXT_COURSE_ID, -4, 4)
                                ) AS note,
                                null AS last_updated
                                FROM {$p}lti_link l
                                join {$p}lti_context c on l.context_id = c.context_id
                                left join {$p}fci_sis_enrollments e on c.sis_course_code = e.COURSE_ID
                                join {$p}fci_sis_bio_demo b on (e.EXT_STUDENT_ID3=b.EXT_STUDENT_ID3 or e.EXT_STUDENT_ID1 = b.EXT_STUDENT_ID1)
                                left join {$p}lti_result r on e.EXT_COURSE_ID = r.sis_enrollment_id and l.link_id=r.link_id
                                WHERE l.link_id = :LID AND

                                      substring(e.EXT_COURSE_ID, -4, 4) IN (SELECT CASE WHEN(e.EXT_SITE_ID != 'MIL') THEN t.term_id WHEN (e.EXT_SITE_ID = 'MIL' AND substring(c.lms_course_code,1,4) = t.term_id ) THEN t.term_id ELSE NULL END
                                                                            FROM {$p}fci_term t
                                                                            WHERE sysdate() BETWEEN t.term_start_dt AND t.term_end_dt) AND
                                      e.WITHDRAW_DT IS NULL AND
                                      r.result_id IS NULL;";
        
        $sqlGraded= "SELECT R.user_id AS user_id, displayname, email,
                grade, note, R.updated_at AS last_updated
            FROM {$p}lti_result AS R
            JOIN {$p}lti_user AS U ON R.user_id = U.user_id
            WHERE R.link_id = :LID AND grade = 1";
        $params['desc'] = 1;
        // Create sub-category tables excluding the use of any search fields until further feedback
        echo('<p class="alert alert-info"> Below are the Submissions for this Flex Check-In Assignment:</p>');
        echo('<hr><h4> Awaiting Faculty Response:</h4>');
        Table::pagedAuto($sqlCompleted,$query_parms, false, $orderfields, "grade-detail.php", $params);
        echo('<hr><h4> Incomplete Submissions:</h4>');
        Table::pagedAuto($sqlIncomplete,$query_parms, $searchfields, $orderfields, "grade-detail.php", $params);
        echo('<hr><h4> Completed Submissions:</h4>');
        Table::pagedAuto($sqlGraded,$query_parms, $searchfields, $orderfields, "grade-detail.php", $params);



        // Since this is in a popup, put out a done button
        //$OUTPUT->closeButton();
        echo('<a href="index.php"><button class="btn btn-info">Back</button></a> ');

        $OUTPUT->footer();
    }
}
