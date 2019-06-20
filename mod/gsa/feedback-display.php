<?php
require_once "../config.php";
require_once "parse.php";
require_once "sample.php";

use \Tsugi\Util\LTI;
use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;
use \Tsugi\Grades\GradeUtil;



$LAUNCH = LTIX::requireData();
$userRole = $USER->determineUserRole($USER->id);


// Get all of the feedback connected to that question and person
// Loop through, displaying the feedback in result id order (highest to lowest to show newer stuff) within an HTML template

$userId = $USER->id;
$currentUser = $USER->displayname;

    if ($userRole) {
        // In an instructor

        // The question should go here??


        $studentId = $_GET['student'];
        $resultId = $_GET['result'];
        $fciType = LTIX::ltiCustomGet('fcitype');
        $displayNoResult = true;

        if ($userRole) {

        $sql = "SELECT f.json, f.user_updated, f.instructor_updated, u.displayname FROM lti_result as r RIGHT JOIN fci_result_history AS f ON r.result_id = f.result_id INNER JOIN lti_user AS u ON f.user_id = u.user_id WHERE f.result_id = :resultId AND f.instructor_id = :instructorId AND f.user_id = :studentId AND r.fci_type = :fciType ORDER BY saved_timestamp DESC";

        $result = $PDOX->queryDie($sql, array(
            ':resultId'=>$resultId,
            ':instructorId'=>$userId,
            ':studentId'=>$studentId,
            ':fciType'=>$fciType
        ));
        } else {

        $sql = "SELECT f.json, f.user_updated, f.instructor_updated, u.displayname, f.instructor_id FROM lti_result as r RIGHT JOIN fci_result_history AS f ON r.result_id = f.result_id INNER JOIN lti_user AS u ON f.user_id = u.user_id WHERE f.result_id = :resultId AND f.user_id = :studentId AND r.fci_type = :fciType ORDER BY saved_timestamp DESC";

        $result = $PDOX->queryDie($sql, array(
            ':resultId'=>$resultId,
            ':studentId'=>$studentId,
            ':fciType'=>$fciType
        ));
        }

        $oldStudentResponse = '';
        $oldInstructorFeedback = '';
        $firstTimeFlag = true;

        $studentResponseList = [];
        $instructorFeedbackList = [];

        foreach ($result as $item) {

            $instructorName;
            $instructorId = $item['instructor_id'];
            $studentSubmittedDate = $item['user_updated'];
            $instructorSubmittedDate = $item['instructor_updated'];

            $sql= "SELECT displayname FROM lti_user WHERE user_id = :instructorId";
            $instructorResult = $PDOX->queryDie($sql, array(
                    ":instructorId"=>$instructorId
            ));

            foreach ($instructorResult as $instructor) {
                $instructorName = $instructor['displayname'];
            }

            $studentName = $item['displayname'];
            $studentSubmittedDate = $item['user_updated'];
            $instructorSubmittedDate = $item['instructor_updated'];

            if (isset($item['json']) && $item['json'] != null && $item['json'] != '') {
                $jsonLine = (array)json_decode($item['json']);

                if (isset($jsonLine['submit']) && $jsonLine['submit'] != null && $jsonLine['submit'] != '') {
                    $submitLine = (array)$jsonLine['submit'];

                    $i = 0;
                    foreach ($submitLine as $key => $value) {
                        if ($i == 1) {
                            $studentResponse = $value;
                        }
                        $i++;
                    }

                }

                if (isset($jsonLine['grade']) && $jsonLine['grade'] != null && $jsonLine['grade'] != '') {
                    $gradeLine = (array)$jsonLine['grade'];

                    if (isset($gradeLine['feedback']) && $gradeLine['feedback'] != null && $gradeLine['feedback'] != '') {
                        $instructorFeedback = $gradeLine['feedback'];
                    }
                }

            }

            if ($firstTimeFlag && !isset($instructorFeedback)) {

            $currentValues = array (
                "name" => $studentName,
                "date" => $studentSubmittedDate,
                "response" => $studentResponse
            );

            array_push($studentResponseList, $currentValues);

                $firstTimeFlag = false;
                $displayNoResult = false;
            }

            if (isset($studentResponse) && isset($instructorFeedback)) {
                if ($studentResponse != null && $studentResponse != '' && $instructorFeedback != null && $instructorFeedback != '' && $studentResponse != $oldStudentResponse && $instructorFeedback != $oldInstructorFeedback) {
                    $displayNoResult = false;
            ?>

            <?php
                if ($studentResponse != null && $studentResponse != '' && $studentResponse != $oldStudentResponse) {
                    $currentValues = array (
                        "name" => $studentName,
                        "date" => $studentSubmittedDate,
                        "response" => $studentResponse
                    );

                    array_push($studentResponseList, $currentValues);
             }

             if ($instructorFeedback != null && $instructorFeedback != '' && $instructorFeedback != $oldInstructorFeedback) {

                    $currentValues = array (
                        "name" => $instructorName,
                        "date" => $instructorSubmittedDate,
                        "feedback" => $instructorFeedback
                    );

                    array_push($instructorFeedbackList, $currentValues);

                }
                ?>
            <?php

            }

            }

                if (isset($studentResponse)) {
                    $oldStudentResponse = $studentResponse;
                }

                if (isset($instructorFeedback)) {
                    $oldInstructorFeedback = $instructorFeedback;
                }

                $jsonLine = '';
                $submitLine = '';
                $studentResponse = '';
                $gradeLine = '';
                $instructorFeedback = '';
                $firstTimeFlag = false;

        }

        if ($displayNoResult) {
        ?>
            <br /><br /><br /><br />
            <p class='alert alert-danger' style='text-align: center; border-radius: 4px; border: 1px solid #ebccd1; margin-bottom: 20px; padding: 15px; color: #a94442; background-color: #f2dede;'>No instructor feedback to display</p>
            <br /><br />
        <?php
        } else {
            echo "<div>";
            echo('<button onclick="window.history.back();" class="btn btn-info" style="color: white; background-color: #5bc0de; border: 1px solid #46b8da; margin-bottom: 0; font-weight: 400; text-align: center; vertical-align: middle; white-space: nowrap; padding: 6px 12px; font-size: 14px; line-height: 1.42857143; border-radius: 4px; user-select: none; cursor: pointer; clear: both;">Back to Student Submission</button> '."\n");
            echo "</div>";
            echo "<br />";
        }

        $studentResponseList = array_reverse($studentResponseList);
        $instructorFeedbackList = array_reverse($instructorFeedbackList);


        for ($i=0; $i < count($studentResponseList); $i++) {
            $studentArray = (array) $studentResponseList[$i];

             $studentName = $studentArray['name'];
             $studentDate = $studentArray['date'];
             $studentDisplay = $studentArray['response'];

             if (!isset($instructorFeedbackList[$i])) {
                 echo('<br /><br />');
             }

         ?>

            <div style="border-radius: 15px; border: solid 1px #31708f; width: 65%; float: left; padding-top: 0em; padding-bottom: 0em; padding-left: 2em; padding-right: 2em; margin-bottom: 1em; background-color: #d9edf7;">
                <h1 style='color: #31708f; font-family: serif; font-weight: 900;'><?php echo $studentName; ?></h1>
                <p style="font-size: 70%; color: #31708f; font-family: serif;"><?php echo $studentDate; ?></p>
                <hr style="display: block; height: 1px; border: 0; border-top: 1px solid #808080; margin: 1em 0; padding: 0; font-family: serif;" />
                <p style="text-align: center; color: #31708f; font-family: serif;"><?php echo $studentDisplay; ?></p>
            </div>

         <?php

             if (isset($instructorFeedbackList[$i])) {
                $instructorArray = (array) $instructorFeedbackList[$i];

                $instructorName = $instructorArray['name'];
                $instructorDate = $instructorArray['date'];
                $instructorDisplay = $instructorArray['feedback'];


            ?>

            <div style="border-radius: 15px; border: solid 1px #3c763d; width: 65%; float: right; margin-bottom: 1em; padding-top: 0em; padding-bottom: 0em; padding-right: 2em; padding-left: 2em; background-color: #dff0d8;">
                <h1 style="text-align: right; color: #3c763d; font-family: serif; font-weight: 900;"><?php echo $instructorName; ?></h1>
                <p style="font-size: 70%; text-align: right; color: #3c763d; font-family: serif;"><?php echo $instructorDate; ?></p>
                <hr style="display: block; height: 1px; border: 0; border-top: 1px solid #808080; margin: 1em 0; padding: 0; font-family: serif;" />
                <p style="text-align: center; color: #3c763d; font-family: serif;"><?php echo $instructorDisplay; ?></p>
            </div>

            <?php
            }

        }

        // INSTRUCTOR FEEDBACK BUTTON
        echo "<br /><br />";
        echo "<div style='clear: both;'>";
        echo('<span style="margin-right: 1em;"><button onclick="window.history.back();" class="btn btn-info" style="color: white; background-color: #5bc0de; border: 1px solid #46b8da; margin-bottom: 0; font-weight: 400; text-align: center; vertical-align: middle; white-space: nowrap; padding: 6px 12px; font-size: 14px; line-height: 1.42857143; border-radius: 4px; user-select: none; cursor: pointer;">Add Feedback</button></span>');
        echo('<span><button onclick="window.history.back();" class="btn btn-info" style="color: white; background-color: #5bc0de; border: 1px solid #46b8da; margin-bottom: 0; font-weight: 400; text-align: center; vertical-align: middle; white-space: nowrap; padding: 6px 12px; font-size: 14px; line-height: 1.42857143; border-radius: 4px; user-select: none; cursor: pointer;">Back to Student Submission</button></span>');
        echo "</div>";

    } else {
        // Is a student
        $displayNoResult = true;
        $resultId = $_SESSION['lti']['result_id'];
        $fciType = LTIX::ltiCustomGet('fcitype');

        $sql = "SELECT f.json, f.user_updated, f.instructor_updated, f.instructor_id FROM lti_result as r RIGHT JOIN fci_result_history AS f ON r.result_id = f.result_id WHERE f.user_id = :userId AND f.result_id = :resultId AND r.fci_type = :fciType ORDER BY saved_timestamp DESC";
        $result = $PDOX->queryDie($sql, array(
           ':userId'=>$userId,
            ':resultId'=>$resultId,
            ':fciType'=>$fciType
        ));

        $oldStudentResponse = '';
        $oldInstructorFeedback = '';
        $firstTimeFlag = true;
        $studentResponseList = [];
        $instructorFeedbackList = [];

        foreach ($result as $item) {

            $instructorName;
            $instructorId = $item['instructor_id'];
            $studentSubmittedDate = $item['user_updated'];
            $instructorSubmittedDate = $item['instructor_updated'];

            $sql= "SELECT displayname FROM lti_user WHERE user_id = :instructorId";
            $instructorResult = $PDOX->queryDie($sql, array(
                    ":instructorId"=>$instructorId
            ));

            foreach ($instructorResult as $instructor) {
                $instructorName = $instructor['displayname'];
            }

           if (isset($item['json']) && $item['json'] != null && $item['json'] != '') {
                           $jsonLine = (array)json_decode($item['json']);

                           if (isset($jsonLine['submit']) && $jsonLine['submit'] != null && $jsonLine['submit'] != '') {
                               $submitLine = (array)$jsonLine['submit'];

                               $i = 0;
                               foreach ($submitLine as $key => $value) {
                                   if ($i == 1) {
                                       $studentResponse = $value;
                                   }
                                   $i++;
                               }
                           }

                           if (isset($jsonLine['grade']) && $jsonLine['grade'] != null && $jsonLine['grade'] != '') {
                               $gradeLine = (array)$jsonLine['grade'];

                               if (isset($gradeLine['feedback']) && $gradeLine['feedback'] != null && $gradeLine['feedback'] != '') {
                                   $instructorFeedback = $gradeLine['feedback'];
                               }
                           }
                       }

                if ($firstTimeFlag && !isset($instructorFeedback)) {
                $displayNoResult = false;

                $currentValues = array (
                    "name" => $currentUser,
                    "date" => $studentSubmittedDate,
                    "response" => $studentResponse
                );

                array_push($studentResponseList, $currentValues);

                    $firstTimeFlag = false;
                }

               if (isset($studentResponse) && isset($instructorFeedback)) {
            if ($studentResponse != null && $studentResponse != '' && $instructorFeedback != null && $instructorFeedback != '' && $studentResponse != $oldStudentResponse && $instructorFeedback != $oldInstructorFeedback) {
                        $displayNoResult = false;

                    if ($studentResponse != null && $studentResponse != '' && $studentResponse != $oldStudentResponse) {

                        $currentValues = array (
                            "name" => $currentUser,
                            "date" => $studentSubmittedDate,
                            "response" => $studentResponse
                        );

                        array_push($studentResponseList, $currentValues);
             }

         if ($instructorFeedback != null && $instructorFeedback != '' && $instructorFeedback != $oldInstructorFeedback) {

                        $currentValues = array (
                            "name" => $instructorName,
                            "date" => $instructorSubmittedDate,
                            "feedback" => $instructorFeedback
                        );

                        array_push($instructorFeedbackList, $currentValues);

                }

            }

        }
                    if (isset($studentResponse)) {
                        $oldStudentResponse = $studentResponse;
                    }

                    if (isset($instructorFeedback)) {
                        $oldInstructorFeedback = $instructorFeedback;
                    }

                    $jsonLine = '';
                    $submitLine = '';
                    $studentResponse = '';
                    $gradeLine = '';
                    $instructorFeedback = '';
                    $firstTimeFlag = false;

            }


            if ($displayNoResult) {
                echo "<br /><br /><br /><br />";
                echo "<p class='alert alert-danger' style='text-align: center;'>No instructor feedback to display</p>";
                echo "<br /><br />";
            } else {
              ?>
                <input type=submit name=doCancel  class="btn btn-info" onclick="location='<?php echo(addSession('index.php'));?>'; return false;" style="color: white; background-color: #5bc0de; border: 1px solid #46b8da; margin-bottom: 0; font-weight: 400; text-align: center; vertical-align: middle; white-space: nowrap; padding: 6px 12px; font-size: 14px; line-height: 1.42857143; border-radius: 4px; user-select: none; cursor: pointer;" value="Back">
               <br /><br />
                <?php
            }

            $studentResponseList = array_reverse($studentResponseList);
            $instructorFeedbackList = array_reverse($instructorFeedbackList);


            for ($i=0; $i < count($studentResponseList); $i++) {
                $studentArray = (array) $studentResponseList[$i];

                $studentName = $studentArray['name'];
                $studentDate = $studentArray['date'];
                $studentDisplay = $studentArray['response'];

                ?>
                <div style="width: 100%;">
                <div style="clear: both; border-radius: 15px; border: solid 1px #31708f; width: 65%; float: left; padding-top: 0em; padding-bottom: 0em; padding-left: 2em; padding-right: 2em; margin-bottom: 1em; background-color: #d9edf7;">
                    <h1 style='color: #31708f; font-family: serif; font-weight: 900;'><?php echo $studentName; ?></h1>
                    <p style="font-size: 70%; color: #31708f; font-family: serif;"><?php echo $studentDate; ?></p>
                <hr style="display: block; height: 1px; border: 0; border-top: 1px solid #808080; margin: 1em 0; padding: 0; font-family: serif;" />
                    <p style="text-align: center; color: #31708f; font-family: serif;"><?php echo $studentDisplay; ?></p>
                </div>
                </div>

                <?php

                if (isset($instructorFeedbackList[$i])) {

                $instructorArray = (array) $instructorFeedbackList[$i];

                $instructorName = $instructorArray['name'];
                $instructorDate = $instructorArray['date'];
                $instructorDisplay = $instructorArray['feedback'];

                ?>


            <div style="border-radius: 15px; border: solid 1px #3c763d; width: 65%; float: right; margin-bottom: 1em; padding-top: 0em; padding-bottom: 0em; padding-right: 2em; padding-left: 2em; background-color: #dff0d8;">
                <h1 style="text-align: right; color: #3c763d; font-family: serif; font-weight: 900;"><?php echo $instructorName; ?></h1>
                <p style="font-size: 70%; text-align: right; color: #3c763d; font-family: serif;"><?php echo $instructorDate; ?></p>
                <hr style="display: block; height: 1px; border: 0; border-top: 1px solid #808080; margin: 1em 0; padding: 0; font-family: serif;" />
                <p style="text-align: center; color: #3c763d; font-family: serif;"><?php echo $instructorDisplay; ?></p>
            </div>
            <?php

                }

            }

            echo "<br /><br />";

            // STUDENT BOX
            require_once('student-display-box.php');


            ?>


            <br /><br />
            <input type=submit name=doCancel  class="btn btn-info" onclick="location='<?php echo(addSession('index.php'));?>'; return false;" value="Back">
            <?php

}





$OUTPUT->footerStart();

?>
