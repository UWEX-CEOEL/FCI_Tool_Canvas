<?php
require_once "../config.php";
require_once "parse.php";
require_once "sample.php";

use \Tsugi\Util\LTI;
use \Tsugi\Core\Settings;
use \Tsugi\Core\LTIX;
use \Tsugi\UI\SettingsForm;
use \Tsugi\Grades\GradeUtil;


function percent($x) {
    return sprintf("%.1f%%", $x * 100);
}

$LAUNCH = LTIX::requireData();
$p = $CFG->dbprefix;
$userRole = $USER->determineUserRole($USER->id);

if ( SettingsForm::handleSettingsPost() ) {
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
        return;
    }

    if ( $userRole || $ok ) {
        // No problem
    } else {
        // No error message in session because status is always displayed
        return;
    }

    $result = array("when" => time(), "tries" => $tries+1, "submit" => $_POST);
    $RESULT->setJson(json_encode($result));

    $_SESSION['gift_submit'] = $_POST;

    print_r($_POST['submit']);

    $quiz = make_quiz($_POST->submit, $questions, $errors);
// HERE?
print_r($quiz);


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

    // This row should possibly be the student id not the user id??
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

    return;
}

// View
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();



// Settings button and dialog



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

if (!$userRole && $oldfeedback != null && $oldfeedback != '') {
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





// parse the GIFT questions
$questions = array();
$errors = array();
parse_gift($gift, $questions, $errors);

?>
    <form method="post">
        <div  style="text-align: center;" size="400" cols='80'>
            <div style ="display: inline-block; text-align: left;margin-top: 2%" cols="80">
                <ol id="quiz">
                </ol>

                    <?php
                    // $userRole = $USER->instructor;
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

/*
$qj = json_encode($questions);
echo("<pre>\n");
var_dump($attempt);
var_dump($errors);
echo(htmlent_utf8(LTI::jsonIndent($qj)));
echo("</pre>\n");
*/

$OUTPUT->footerStart();



?>

<script id="essay_question" type="text/x-handlebars-template">


	<p style="clear:both;font-weight: bold;">Instructor Feedback: </p>
	<textarea name="feedback" id="feedback" size="400" rows="5" cols="80"/>
    <p style="clear:both;font-weight: bold;">Completed : <input id="completed_chbx" type="checkbox" name="completed_chbx" size="20" rows="1" cols = "5" /></p>

</script>

<script id="flex_check_in" type="text/x-handlebars-template">
    <li>
        <p>Hello Flex Check-in Question</p>
    </li>
</script>
<script id="short_answer_question" type="text/x-handlebars-template">
  <li>
    <p>
    {{#if scored}}{{#if correct}}
        <i class="fa fa-check text-success"></i>
    {{else}}
        <i class="fa fa-times text-danger"></i>
    {{/if}} {{/if}}
    {{{question}}}</p>
    <p><input type="text" name="{{code}}" value="{{value}}" size="80"/></p>
  </li>
</script>
<script id="multiple_answers_question" type="text/x-handlebars-template">
  <li>
    <p>
    {{#if scored}}{{#if correct}}
        <i class="fa fa-check text-success"></i>
    {{else}}
        <i class="fa fa-times text-danger"></i>
    {{/if}} {{/if}}
    {{{question}}}</p>
    <div>
    {{#each answers}}
    <p><input type="checkbox" name="{{code}}" {{#if checked}}checked{{/if}} value="true"/> {{text}}</p>
    {{/each}}
    </div>
  </li>
</script>
<script id="true_false_question" type="text/x-handlebars-template">
  <li>
    <p>
    {{#if scored}}{{#if correct}}
        <i class="fa fa-check text-success"></i>
    {{else}}
        <i class="fa fa-times text-danger"></i>
    {{/if}} {{/if}}
    {{{question}}}</p>
    <p><input type="radio" name="{{code}}" {{#if value_true}}checked{{/if}} value="T"/> True
    <input type="radio" name="{{code}}" {{#if value_false}}checked{{/if}} value="F"/> False
    </p>
  </li>
</script>
<script id="multiple_choice_question" type="text/x-handlebars-template">
  <li>
    <p>
    {{#if scored}}{{#if correct}}
        <i class="fa fa-check text-success"></i>
    {{else}}
        <i class="fa fa-times text-danger"></i>
    {{/if}} {{/if}}
    {{{question}}}</p>
    <div>
    {{#each answers}}
    <p><input type="radio" name="{{../code}}" {{#if checked}}checked{{/if}} value="{{code}}"/> {{text}}</p>
    {{/each}}
    </div>
  </li>
</script>





    <script>
        var submitJSON = <?php echo $oldsubmit ?>;
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
                    console.log("Quiz was stored as completed");
                    document.getElementById("completed_chbx").checked = "checked";
                } else {
                    document.getElementById("completed_chbx").checked = false;
                }
                if(feedbackString !== undefined || feedbackString !== null){
                    document.getElementById("feedback").value = feedbackString;
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
