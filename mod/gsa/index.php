<?php

require_once "../config.php";
require_once "parse.php";
require_once "sample.php";
require_once "../../vendor/PHPMailer/src/PHPMailer.php";
require_once "../../vendor/PHPMailer/src/Exception.php";

use \Tsugi\Util\LTI;
use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;
use \Tsugi\Grades\GradeUtil;
use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;
use \Tsugi\Core\User;


function percent($x) {
    return sprintf("%.1f%%", $x * 100);
}

$LAUNCH = LTIX::requireData();
$p = $CFG->dbprefix;
$fciType = LTIX::ltiCustomGet('fci_type');
$resultId = $_SESSION['lti']['result_id'];
$currentTerm = $_SESSION['lti_post']['custom_dxjcanvas_section'];
$fciState;


// $currentRole = $PDOX->rowDie("SELECT lms_rolename FROM {$CFG->dbprefix}lti_user WHERE user_id = :userId",
// array(':userId' => $USER->id));
//
// if (fnmatch('*TeacherEnrollment*', $currentRole['lms_rolename'])) {
//   $USER->setInstructor(1);
// }

// Determine whether or not the user is an instructor
// instructor = true
// not an instructor = false
$userRole = $USER->determineUserRole($USER->id);

// If this isn't an instructor, check if they're a repeat student and, if so, wipe previous attempt
if (!$userRole) {
    $wipeStatus = false;
    $wipeStatus = $RESULT->wipeRepeats($resultId, $currentTerm, $USER->id);
    
    if ($wipeStatus) {
         $RESULT->grade = 0;
    }
}


if ( SettingsForm::handleSettingsPost() ) {
    header( 'Location: '.addSession('index.php') ) ;
    return;
}


// Get the settings
$max_tries = Settings::linkGet('tries')+100;
if ( $max_tries < 1 ) $max_tries = 1;
$delay = Settings::linkGet('delay')+0;

// Get any due date information
$dueDate = SettingsForm::getDueDate();

// Load the quiz
$gift = $LINK->getJson();

// parse the quiz questions
$questions = false;
$errors = array("No questions found");
if ( strlen($gift) > 0 ) {
    $questions = array();
    $errors = array();
    parse_gift($gift, $questions, $errors);
}


// Load the previous attempt
$attempt = json_decode($RESULT->getJson());

$when = 0;
$tries = 0;
if ( $attempt && is_object($attempt) ) {
    if ( isset($attempt->when) ) $when = $attempt->when + 0;
    if ( isset($attempt->tries) ) $tries = $attempt->tries + 0;
}

// Decide if it is OK to submit this quiz
$ok = true;
$why = '';
if ( $tries >= $max_tries ) {
    $ok = false;
    $why = 'This quiz can only be attempted ('.$max_tries.') time(s).';
} else if ( $when > 0 && ($when + $delay) > time() ) {
    $ok = false;
    $why = 'You cannot retry this quiz for '.SettingsForm::getDueDateDelta(($when + $delay) - time());
}

if (isset($attempt->submit)){
    $oldsubmit = json_encode($attempt->submit);
} else {
    $oldsubmit = json_encode(array());
}
if (isset($RESULT->grade)){
    $oldgrade = $RESULT->grade;
} else {
    $oldgrade = array();
}
if (isset($attempt->grade->feedback)){
    $oldfeedback=$attempt->grade->feedback;
} else {
    $oldfeedback="";
}

//echo(json_encode('POST COUNT :'.count($_POST)));
if ( count($_POST) > 0 ) {
    if ( $questions == false ) {
        $_SESSION['error'] ='Internal error: No questions';
        header( 'Location: '.addSession('index.php') ) ;
        return;
    }

    if ( $userRole || $ok ) {
        // No problem
    } else {
        // No error message in session because status is always displayed
        header( 'Location: '.addSession('index.php') ) ;
        return;
    }

    $result = array("when" => time(), "tries" => $tries+1, "submit" => $_POST);
    $RESULT->setJson(json_encode($result));

    $_SESSION['gift_submit'] = $_POST;
    $quiz = make_quiz($_POST->submit, $questions, $errors);

    //Compare
    if(strlen($_POST[$questions[0]->code])>0){
        if($oldgrade !== 1){
            $gradetosend=.5;
            $scorestr = "Your Flex Check-In Assignment has been Submitted.";
        } else {
            $gradetosend=1;
        }
    } else {
        $gradetosend = 0;
    }

    // Use LTIX to send the grade back to the LMS.
    $debug_log = array();
    $resultRow=$PDOX->rowDie("SELECT * FROM {$CFG->dbprefix}lti_result WHERE user_id = :UID AND link_id = :LID",
        array( ':UID' => $USER->id, ':LID' => $LINK->id
        )
    );
    $retval = LTIX::gradeSend($gradetosend,$resultRow, $debug_log);
    $_SESSION['debug_log'] = $debug_log;

    if ( $retval === true ) {
        $_SESSION['success'] = $scorestr;
    } else if ( is_string($retval) ) {
        $_SESSION['error'] = "Feedback not sent: ".$retval."resultid : ".$resultRow['source_id'];
    } else {
        echo("<pre>\n");
        var_dump($retval);
        echo("</pre>\n");
        die();
    }

    header( 'Location: '.addSession('index.php') ) ;
    return;
}


//$currentMonth = date("m");
//$currentYear = date("Y");
//
//$dateSubmitted = $currentYear . "-" . $currentMonth . "-1";
//
//$sql = "SELECT USERNAME, ORG_DEFINED_ID, ROLE_NAME, ORG_UNIT_CODE, ORG_UNIT_ID, DATE_FORMAT(DATE_SUBMITTED, '%m/%d/%y %H:%i'), DATE_POSTED, TIME_STARTED, TIME_COMPLETED, INSTITUTION FROM financial_aid_report_vw WHERE DATE_SUBMITTED <= :dateSubmitted";
//$result = $PDOX->queryDie($sql, array(
//    "dateSubmitted"=>$dateSubmitted
//));
////print_r($result);
//
//$currentDate = date("m_d_Y");
//$reportName = 'report_' . $currentDate . '.csv';
//
//// Headers to download the file -- Probably won't need them once I add the code to email it
//header('Content-type: text/csv');
//header('Content-Disposition: attachment; filename="report' . $currentDate . '.csv"');
//header('Cache-Control: no-cache, no-store, must-revalidate');
//header('Pragma: no-cache');
//header('Expires: 0');
//
//$report = tempnam(sys_get_temp_dir(), 'report_') . '.csv';
//
//// I think fopen will change, too?
//$file = fopen($report, 'w');
//
//fputcsv($file, array('USERNAME', 'ORG_DEFINED_ID', 'ROLE_NAME', 'ORG_UNIT_CODE', 'ORG_UNIT_ID', 'DATE_SUBMITTED', 'DATE_POSTED', 'TIME_STARTED', 'TIME_COMPLETED', 'INSTITUTION'));
//$anotherArray = [];
//
//foreach ($result as $row)
//{
//
//    $revisedRow = array();
//
//    $i = 0;
//
//    foreach ($row as $item) {
//        // The rows are duplicated, so this fixes it
//        if ($i % 2 != 0) {
//            array_push($revisedRow, $item);
//        }
//
//        $i++;
//    }
//
//
//fputcsv($file, $revisedRow);
//array_push($anotherArray, $revisedRow);
//}
//
//
//$mail = new PHPMailer();
//$mail->setFrom('marcella.thompson@uwex.edu', 'Marcella Thompson');
//$mail->addAddress('marcella.thompson@uwex.edu', 'Marcella Thompson');
//$mail->Subject = "This is a test";
//$mail->Body = "Hi! This is a test email.";
//
//// replace file with the path to where the file temporarily lives
//$mail->addStringAttachment(file_get_contents($report), $reportName);
//$mail->send();
//
////unlink($report);
//
//// Don't forget about this
//fclose($file);
//exit();


// View
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();

// Settings button and dialog

// echo('<span style="position: fixed; right: 10px; top: 5px;">');
echo('<span style="float: right; margin-bottom: 10px;">');
if ( $userRole ) {
    echo('<a href="configure.php" class="btn btn-default">Flex Check-In Content</a> ');
}
if ( $userRole ) {
    echo('<a href="grades.php"><button class="btn btn-info">Submission Detail</button></a> '."\n");
}
$OUTPUT->exitButton();
//SettingsForm::button();
echo('</span>');

/*
SettingsForm::start();
SettingsForm::text('tries',__('The number of tries allowed for this quiz.  Leave blank or set to 1 for a single try.'));
SettingsForm::text('delay',__('The number of seconds between retries.  Leave blank or set to zero to allow immediate retries.'));
SettingsForm::dueDate();
SettingsForm::end();
*/

//Define default starting message, we hope to have the instructor edit this this
//$startMessage="
//<div class=\"page-container\" role=\"main\"> <!-- BEGIN PAGE CONTAINER -->
//  <header role=\"banner\"> <!-- REMEMBER TO CHANGE THE PAGE TITLE -->
//    <h1><span class=\"icon-assessment\"></span> Flex Check-In Assignment</h1>
//  </header>
//  <!-- END HEADER -->
//  <div class=\"callout danger vertical\">
//  </div>
//  <hr>
//  <h2>Assignment Overview</h2>
//  <p>Setting goals, having a plan, and writing both down is a proven way to help people reach goals they set for themselves. You have already completed one of the hardest parts of reaching any goal… getting starting. Congratulations!</p>
//  <p>We want you to succeed at reaching your educational goals,  which is why we encourage you to create a plan for how you will progress  through each competency set. </p>
//  <p>We have seen that the people who succeed in finishing competency sets in one subscription period do four things:</p>
//  <ol>
//    <li>They create a plan for how they will progress through the competency set.</li>
//    <li>They start early and set challenging yet realistic timelines.</li>
//    <li>They stick with the plan and keep moving forward.</li>
//    <li>They build calendar items and notifications to help them manage a plan.</li>
//  </ol>
//  <p>The purpose of the goal setting assignment is for you to put into writing what you want to accomplish and when you want to accomplish it.&nbsp;</p>
//  <h2>Feedback</h2>
//  <p>After you have completed the assignment and submitted it, a  faculty member will review your plan and provide you with some basic  feedback.&nbsp; This assignment is also a  great opportunity for you to reach out to your ASC and discuss your goals and  plan. </p>
//</div>
//<!-- END PAGE CONTAINER -->
//";

//$OUTPUT->welcomeUserCourse($startMessage);
$OUTPUT->flashMessages();


// Clean up the JSON for presentation
if ( $gift === false || strlen($gift) < 1 ) {
    //Add code to initiate quiz with Sample GIFT if this is the first time

    $defaultgift = displayProperGIFT();

    $LINK->setJson($defaultgift);
    header( 'Location: '.addSession('index.php') ) ;
    /*
    if(!$userRole){
        echo('<p class="alert alert-danger" style="clear:both;">You do not have access to this Flex Check-In Assignment yet. Please try again later.</p>'."\n");
        $OUTPUT->footer();
        return;
    } else {
        echo('<p class="alert alert-danger" style="clear:both;">This Flex Check-In Assignment has not yet been configured.</p>'."\n");
        $OUTPUT->footer();
        return;
    }*/
}

// if (!$USER->giveFeedback && !$USER->modifyQuestion && !$USER->readonlyView && !$USER->individualView) {
//     header( 'Location: '.addSession('no-access-message.php') ) ;
// }



$hideQuestion = false;

if (! $userRole) {

$sql = "SELECT fci_state FROM lti_result WHERE result_id = :resultId";
$result = $PDOX->queryDie($sql, array(
    ':resultId' => $resultId
));

foreach ($result as $fciLine) {
    $fciState = $fciLine['fci_state'];
}

// Only do this for students
$fciState = 0;
switch ($fciState) {
    case 0:
        // In this case, we do nothing
    break;

    case 1:
        // Access to M1 Only
        if ($fciType == 'M1') {
            // Do nothing since you have access to it then
        } else if ($fciType == 'M2' ) {

            $availablityDate;

            $sql = "SELECT sis_enrollment_id FROM lti_result WHERE result_id = :resultId";
            $result = $PDOX->queryDie($sql, array(
                ':resultId'=>$resultId
            ));

            foreach ($result as $enrollmentId) {
                if ($enrollmentId != null && $enrollmentId != '') {
                    $termId = substr($enrollmentId['sis_enrollment_id'], -4);
                }
            }

            $sql = "SELECT m.sp_m1_start_dt FROM fci_month AS m INNER JOIN fci_term AS t ON m.month_id=t.term_month_2 WHERE t.term_id = :termId";
            $result = $PDOX->queryDie($sql, array(
                ':termId'=>$termId
            ));

            foreach ($result as $startDate) {
                $availablityDate = $startDate['sp_m1_start_dt'];
            }

            $hideQuestion = true;

            $monthNum = substr($availablityDate, 5, 2);
            $dateObj = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('F');

             ?>
             <br /><br /><br /><br />
             <p class="alert alert-danger" style="clear:both;">
                 This FCI question will not be available until <?php echo $monthName; ?> 1.
             <br /><br />
                 For every competency set or project in which you are currently enrolled,
                 you must submit your response to FCI-Q2 within the first 10 calendar days  <u>of the second month</u> of your subscription period.

                 <br /><br />

                 Note: <i>If you add a competency set after the 10th day of your subscription period, or complete a competency
                 set early, the FCI policies apply differently.</i> FCI questions must be completed in sequential order. Visit the
                 <a href='https://flex.wisconsin.edu/current-students/flex-check-in/' target='_blank'>Flex Check-In</a> website and associated
                 <a href='https://flex.wisconsin.edu/faqs/#flex-check-in' target='_blank'>FAQ pages</a> for important information.
             </p>

             <?php

        } else if ($fciType == 'M3') {

            $availablityDate;

            $sql = "SELECT sis_enrollment_id FROM lti_result WHERE result_id = :resultId";
            $result = $PDOX->queryDie($sql, array(
                ':resultId'=>$resultId
            ));

            foreach ($result as $enrollmentId) {
                if ($enrollmentId != null && $enrollmentId != '') {
                    $termId = substr($enrollmentId['sis_enrollment_id'], -4);
                }
            }

            $sql = "SELECT m.sp_m1_start_dt FROM fci_month AS m INNER JOIN fci_term AS t ON m.month_id=t.term_month_3 WHERE t.term_id = :termId";

            $result = $PDOX->queryDie($sql, array(
                ':termId'=>$termId
            ));

            foreach ($result as $startDate) {
                $availablityDate = $startDate['sp_m1_start_dt'];
            }

            $hideQuestion = true;

            $monthNum = substr($availablityDate, 5, 2);
            $dateObj = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('F');

             ?>
             <br /><br /><br /><br />
             <p class="alert alert-danger" style="clear:both;">
                 This FCI question will not be available until <?php echo $monthName; ?> 1.
            <br /><br />
                 For every competency set or project in which you are currently enrolled,
                 you must submit your response to FCI-Q3 within the first 10 calendar days  <u>of the third month</u> of your subscription period.

                 <br /><br />

                 Note: <i>If you add a competency set after the 10th day of your subscription period, or complete a competency
                 set early, the FCI policies apply differently.</i> FCI questions must be completed in sequential order. Visit the
                 <a href='https://flex.wisconsin.edu/current-students/flex-check-in/' target='_blank'>Flex Check-In</a> website and associated
                 <a href='https://flex.wisconsin.edu/faqs/#flex-check-in' target='_blank'>FAQ pages</a> for important information.
             </p>

             <?php

        } else {
            // Do nothing since that is a glitch
        }

    break;

    case 2:
        // Access to M1 and M2
        if ($fciType == 'M1' || $fciType == 'M2') {
            // Do nothing since you have access to it then
        } else if ($fciType == 'M3') {

            $availablityDate;

            $sql = "SELECT sis_enrollment_id FROM lti_result WHERE result_id = :resultId";
            $result = $PDOX->queryDie($sql, array(
                ':resultId'=>$resultId
            ));

            foreach ($result as $enrollmentId) {
                if ($enrollmentId != null && $enrollmentId != '') {
                    $termId = substr($enrollmentId['sis_enrollment_id'], -4);
                }
            }

            $sql = "SELECT m.sp_m1_start_dt FROM fci_month AS m INNER JOIN fci_term AS t ON m.month_id=t.term_month_3 WHERE t.term_id = :termId";
            $result = $PDOX->queryDie($sql, array(
                ':termId'=>$termId
            ));

            foreach ($result as $startDate) {
                $availablityDate = $startDate['sp_m1_start_dt'];
            }

            $hideQuestion = true;

            $monthNum = substr($availablityDate, 5, 2);
            $dateObj = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('F');

             ?>
             <br /><br /><br /><br />
             <p class="alert alert-danger" style="clear:both;">
                 This FCI question will not be available until <?php echo $monthName; ?> 1.
            <br /><br />
                 For every competency set or project in which you are currently enrolled,
                 you must submit your response to FCI-Q3 within the first 10 calendar days  <u>of the third month</u> of your subscription period.

                 <br /><br />

                 Note: <i>If you add a competency set after the 10th day of your subscription period, or complete a competency
                 set early, the FCI policies apply differently.</i> FCI questions must be completed in sequential order. Visit the
                 <a href='https://flex.wisconsin.edu/current-students/flex-check-in/' target='_blank'>Flex Check-In</a> website and associated
                 <a href='https://flex.wisconsin.edu/faqs/#flex-check-in' target='_blank'>FAQ pages</a> for important information.
             </p>

             <?php

        } else {
            // Do nothing since that is a glitch
        }
    break;

    case 3:
        // In this case, we do nothing
    break;

    case 4:
        // Display error message and only error message
        ?>

        <p class="alert alert-danger" style="clear:both;">
            At this time, you do not have access to the Flex Check-In activity because your subscription period has not yet officially
            begun. For any projects and/or competency sets in which you are currently registered, the FCI-Question 1 must be completed
            <i>within</i> the first 10 days of your subscription period. Please make plans to return
            to your FCI once your subscription period is underway. Click <a href='https://flex.wisconsin.edu/current-students/flex-check-in/' target='_blank'>HERE</a> for further information.
        </p>

        <?php
    break;

    case 5:
        // Display message
        ?>
        <p class="alert alert-danger" style="clear:both;">
            This activity is no longer available.
        </p>
        <?php
    break;

    default:
        // In this case, we do nothing
    break;

}


if (! $userRole && $oldfeedback != null && $oldfeedback != '' && !$hideQuestion) {
    // Instructor Name display
    $sql = "SELECT u.displayname FROM {$CFG->dbprefix}lti_user AS u INNER JOIN fci_result_history AS f ON u.user_id=f.instructor_id WHERE f.link_id = :link_id && f.user_id = :student_id ORDER BY f.instructor_id DESC LIMIT 1";
    $stmt = $PDOX->queryDie($sql, array(
        ':link_id'=>$LINK->id,
        ':student_id'=>$USER->id
    ));
    $displayRow = $stmt->fetch(\PDO::FETCH_ASSOC);
//    echo "Instructor Name: " . $displayRow['displayname'] . "<br />";
    echo('<p class="alert alert-info" style="clear:both;font-weight: bold;"> Instructor Name: ' . $displayRow['displayname'] . ' </p>'."\n");

}

// Check database to see if previous response should be hidden or visible
$displayPrevious = 1;
$resetFlag = 0;


$sql = "SELECT reset_flag FROM fci_result_history WHERE result_id = :resultId ORDER BY result_id ASC";
$result = $PDOX->queryDie($sql, array(
    ':resultId' => $resultId
));


foreach ($result as $fciLine) {
    if (isset($fciLine['reset_flag'])) {
        $resetFlag = $fciLine['reset_flag'];
    } else {
        $resetFlag = 0;
    }
}

if ($resetFlag == 1) {
    $displayPrevious = 1;
} else {
    $displayPrevious = 0;
}

if ($displayPrevious == 1) {
echo('<p class="alert alert-info" style="clear:both;">Please note:<br /><br />If you are repeating this competency set for any reason (received an IP, PR, W, F grade) or if you completed the FCI previously but were subsequently dropped, you need to complete the FCI exercise again, starting with FCI Question 1. If you have questions please reach out to your ASC prior to the FCI deadline.<br /><br />If you have added this competency set after the subscription period has begun, please allow up to 24 hours for the Flex Check-In activity to become available. In the meantime, you may complete other activities within the competency set.</p>'."\n");
}


if ( $userRole) {
    echo('<p class="alert alert-success" style="clear:both;font-weight: bold;"> Instructor Preview : Below is what your students see when they log into this Flex Check-In Assignment </p>'."\n");
}


if(isset($RESULT->grade) && !$hideQuestion) {
    if($RESULT->grade ==1 || $RESULT->grade ==0){
        echo('<p class="alert alert-success" style="clear:both;">Your current submission on this assignment is: '.percent($RESULT->grade).'</p>'."\n");
    } else if (isset($attempt->submit) ) {

    // && count($attempt->submit)>0){
        echo('<p class="alert alert-info" style="clear:both;">Your Flex Check-In Assignment is Submitted but has not Received Feedback. </p>'."\n");
    } else {
        echo('<p class="alert alert-danger" style="clear:both;font-weight: bold;">You have yet to submit your Flex Check-In Assignment. Please do so below. </p>'."\n");
    }
}


if ( ! $ok ) {
    if ( $userRole ) {
        echo('<p class="alert alert-info" style="clear:both;">'.$why.' (except for the fact that you are the instructor)</p>'."\n");
    } else {
        echo('<p class="alert alert-danger" style="clear:both;">'.$why.'</p>'."\n");
    }
}




}



// parse the GIFT questions
$questions = array();
$errors = array();
parse_gift($gift, $questions, $errors);

if (!$hideQuestion) {
?>
    <form method="post">
        <div  style="text-align: center;" size="400" cols='80'>
            <div style ="display: inline-block; text-align: left;margin-top: 2%" cols="80">
                <ol id="quiz">
                </ol>

                    <?php
                    if (!$userRole) {
                    ?>
                <div style="text-align: center;"> <input type=submit name=doCancel  class="btn btn-info" onclick="location='<?php
                                echo(addSession('feedback-display.php'));
                        ?>'; return false;" value="View Feedback">

                    <?php
                    } else if ($ok || $userRole ) {
                        echo('<div style="text-align: center;">');
                    }

                if ( $ok || $userRole ) {
                    echo('<input type="submit" value="Submit" class="btn btn-info"> </div>');
                } else {
                    echo('</div>');
                }
                ?>
            </div>
        </div>
    </form>

<?php
}

/*
$qj = json_encode($questions);
echo("<pre>\n");
var_dump($attempt);
var_dump($errors);
echo(htmlent_utf8(LTI::jsonIndent($qj)));
echo("</pre>\n");
*/

$OUTPUT->footerStart();

if (!$hideQuestion) {
    require_once('templates.php');
}


?>
    <script>
        <?php
            if ($displayPrevious == 0) {
        ?>
            console.log("Student response is hidden");
            <?php
                $oldsubmit = json_encode(array());
            ?>
            var submitJSON = <?php echo $oldsubmit ?>;
        <?php
            } else {
        ?>
        var submitJSON = <?php echo $oldsubmit ?>;
        <?php
            }
        ?>
        var gradeJSON = <?php echo json_encode($oldgrade) ?>;
        var feedbackString = <?php echo json_encode($oldfeedback) ?>;
        TEMPLATES = [];
        $(document).ready(function(){
            $.getJSON('<?= addSession('quiz.php')?>', function(quiz) {
                window.console && console.log(quiz);
                for(var i=0; i<quiz.questions.length; i++) {
                    question = quiz.questions[i];
                    type = question.type;
                    if(submitJSON[question.code] !== null){
                        submit = submitJSON[question.code];
                    }
                    console.log(type);
                    if ( TEMPLATES[type] ) {
                        template = TEMPLATES[type];
                    } else {
                        source  = $('#'+type).html();
                        if ( source == undefined ) {
                            window.console && console.log("Did not find template for question type="+type);
                            continue;
                        }
                        template = Handlebars.compile(source);
                        TEMPLATES[type] = template;
                    }
                    $('#quiz').append(template(question));

                    if(submit !== undefined){

                        var windowsUA = window.navigator.userAgent;
                        var ie10 = windowsUA.indexOf('MSIE ');
                        var ie11 = windowsUA.indexOf('Trident/');
                        var edge = windowsUA.indexOf('Edge/');

                        if (ie10 > 0 || ie11 > 0 || edge > 0) {
                            // IE/Edge Modification

                            var submitText = document.createTextNode(submit);
                            document.getElementsByTagName('textarea')[i].appendChild(submitText);

                        } else {
                            document.getElementsByTagName('textarea')[i].append(submit);
                        }
                    }
                }
                if(gradeJSON == 'Completed'|| gradeJSON =="1") {
                    <?php
                        if ($displayPrevious == 0) {
                    ?>
                        console.log("Checkbox is unchecked");
                        document.getElementById("completed_chbx").checked = false;
                    <?php
                        } else {
                    ?>
                    console.log("Quiz was stored as completed");
                    document.getElementById("completed_chbx").checked = "checked";
                    <?php
                        }
                    ?>
                } else {
                    document.getElementById("completed_chbx").checked = false;
                }
                if(feedbackString !== undefined || feedbackString !== null){

                <?php
                    if ($displayPrevious == 0) {
                ?>
                    console.log("Feedback is hidden");
                    document.getElementById("feedback").value = "";
                <?php
                    } else {
                ?>
                    document.getElementById("feedback").value = feedbackString;
                    <?php
                        }
                    ?>
                }
            }).fail( function() { alert('Unable to load quiz data'); } );
        });
    </script>

<?php



//Debug Session
//        echo("<pre>\n");
//        var_dump($_SESSION);
//        echo("</pre>\n");
//

$OUTPUT->footerStart();
