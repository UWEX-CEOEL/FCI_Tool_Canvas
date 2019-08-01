<?php
require_once "../config.php";
require_once "parse.php";
require_once "sample.php";

use \Tsugi\Core\Cache;
use \Tsugi\Core\LTIX;
use \Tsugi\Core\Link;

// Sanity checks
$LAUNCH = LTIX::requireData();
$userRole = $USER->determineUserRole($USER->id);

if ( ! $userRole ) die("Requires modifying role permission");

// Model
$p = $CFG->dbprefix;


//Note: Several pieces of code were commented out in response to stakeholders requesting changes in app behavior,
//instead of 3 questions in a single link we now have one link for each question.
//Keeping this code in in case there's a change of mind.

if ( isset($_POST['gift']) ) {
    if(isset($_POST['qmonth1']) /*|| ($_POST['qmonth2']) || ($_POST['qmonth3'])*/){
        $gift1= "::Q1::".$_POST['qmonth1']." {}";
        //$gift2= "::Q2::".$_POST['qmonth2']." {}";
        //$gift3= "::Q3::".$_POST['qmonth3']." {}";
        //$gift = htmlent_utf8($gift1."\n\n".$gift2."\n\n".$gift3);
		if($_POST['qmonth1'] !==""){
		$gift = $gift1;
		} else {
			$gift="";
		}
    }
    else{
        $gift = $_POST['gift'];
        $msg = "Errors in GIFT data: ".$gift;
        $_SESSION['error'] = $msg;
        return;
    }

    if ( get_magic_quotes_gpc() ) $gift = stripslashes($gift);
    $_SESSION['gift'] = $gift;

    // Some sanity checking...
    $questions = array();
    $errors = array();
    parse_gift($gift, $questions, $errors);

    if ( count($questions) < 1 ) {
        $_SESSION['error'] = "No valid questions found in input data";
        header( 'Location: '.addSession('configure.php') ) ;
        return;
    }

    if ( count($errors) > 0 ) {
        $msg = "Errors in GIFT data: ";
        $i = 1;
        foreach ( $errors as $error ) {
            $msg .= " ($i) ".$error;
        }
        $_SESSION['error'] = $msg;
        header( 'Location: '.addSession('configure.php') ) ;
        return;
    }

    if ($userRole) {
        // THEN update link table with the new json, the date updated, the instructor id, and the instructor name
        $sql = "UPDATE {$p}lti_link SET json = :json, updated_at = NOW(), instructor_id = :instructor_id WHERE link_id = :link_id";
        $PDOX->queryDie($sql, array(
            ':json'=>$gift,
            ':instructor_id'=>$USER->id,
            ':link_id'=>$_SESSION['lti']['link_id']));
        $actions[] = "=== Updated link=".$_SESSION['lti']['link_id'];
    }
	
	// Get link information to update link history table
	$sql = "SELECT link_sha256, link_key, created_at, updated_at FROM {$p}lti_link WHERE link_id = :link_id";
	$currentLink = $PDOX->rowDie($sql, array(
		':link_id'=>$_SESSION['lti']
	));


	// Update link history table
	$sql = "INSERT INTO {$p}fci_link_history (link_id, link_sha256, link_key, json, created_at, instructor_id, saved_timestamp) VALUES (:link_id, :link_sha256, :link_key, :json, :created_at, :instructor_id, :saved_timestamp)";
	$PDOX->queryDie($sql, array(
		':link_id'=>$_SESSION['lti']['link_id'],
		':link_sha256'=>$currentLink['link_sha256'],
		':link_key'=>$currentLink['link_key'],
		':json'=>$gift,
		':created_at'=>$currentLink['created_at'],
		':instructor_id'=>$USER->id,
		':saved_timestamp'=>$currentLink['updated_at']
	));

    $_SESSION['success'] = 'Flex Check-In updated';
    unset($_SESSION['gift']);
    header( 'Location: '.addSession('index.php') ) ;
    return;
}

// Load up the quiz
if ( isset($_SESSION['gift']) ) {
    $gift = $_SESSION['gift'];
    unset($_SESSION['gift']);
} else {
    $gift = $LINK->getJson();
}

// Clean up the JSON for presentation
if ( $gift === false || strlen($gift) < 1 ){
    $gift = displayProperGIFT();
//    if(LTIX::ltiCustomGet('fcitype') == 'M1'){
//        $gift="::Q1:: Review the project objectives and competency assessments.  As you compare these to your current knowledge and skills, which competency is most familiar to you and which competency will require the most learning and preparatory work on your part?  Please add some explanation for each. For which competencies do you need further guidance from faculty?{}
//
//        ";
//    } else if (LTIX::ltiCustomGet('fcitype') == 'M2'){
//        $gift="::Q1:: Consider your progress so far; what strategies have worked best for you?  Are there areas that remain a concern for you?  Why are they challenging? Beyond the learning resources and practice assessments provided to you, do you need additional guidance from faculty for the challenges you are facing?{}
//
//        ";
//    } else if (LTIX::ltiCustomGet('fcitype') == 'M3') {
//        $gift="::Q1:: Reflect on what you have learned in this project so far: How do the competencies mastered apply at your workplace or advance your career? If you are not able to progress well through the project, what could you have done differently to achieve better progress and what additional guidance do you need from faculty?{}
//
//        ";
//    } else {
//        $gift = getSampleGIFT();
//    }
}
//Parse existing gift file
{
    $questions = array();
    $errors = array();
    parse_gift($gift, $questions, $errors);
    $qmonth1="";
    //$qmonth2="";
    //$qmonth3="";
    if(isset($questions[0])){
        $qmonth1=$questions[0]->question;
    }
    /*
    if(isset($questions[1])){
        $qmonth2=$questions[1]->question;
    }
    if(isset($questions[2])){
        $qmonth3=$questions[2]->question;
    }*/
}

// View
$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->flashMessages();

?>
<p>Please take care in making changes to questions if this quiz has submissions.</p>
<p>
    This is the configuration page for the Flex Check-In activity. As instructor for this <span class="formattedInstructions">
    competency set or project</span>, you are required to provide three questions.  Students must respond to these questions
    within the first 10 days of their enrollment in the <span class="formattedInstructions">competency set or project</span>,
    and within the first 10 days of each month thereafter as long as the student remains enrolled.
</p>
<p>
    Please note that a student will always complete the Flex Check-In questions in sequential order. This means that a
    student may be submitting FCI-Question 1 in month 2 or month 3, depending on when a <span class="formattedInstructions">
    competency set/project</span> is added. Likewise, if a student completes a <span class="formattedInstructions">competency
    set or project</span> early, no further FCIs are required.
</p>
<p>You may expect to receive student FCI submissions according to the following schedule:</p>
<ul>
    <li>Month 1 – Response is due by end of day on 10th day of subscription period</li>
    <li>Month 2 – Response is due by EOD on 17th calendar day</li>
    <li>Month 3 – Response is due by EOD on 17th calendar day</li>
</ul>

<p>Your questions should reflect the following:</p>
<ul>
    <li>
        Question 1 – Intended to allow the student to evaluate and reflect. There is not one correct answer. Rather, the
        instructor is interested in a student’s thoughtful responses.
    </li>
    <li>
        Question 2 - Focuses on a student’s process for learning and making progress in the competency set (an opportunity
        to show strategies for completing the set).
    </li>
    <li>
        Question 3 - Focuses on a student’s reflection of the <span class="formattedInstructions">competency set or
        project</span> to this point (an opportunity to show deeper learning and to make connections to the workplace)
        and may invite advice for future students completing the <span class="formattedInstructions">project/set</span>.

    </li>
</ul>

    <hr>
    <div style ="text-align: center;">
        <?php
        if (date('d') >= 20 && date('d') <= 25) {
            ?>
            <form method="post" style="display: inline-block;margin-left:5%;text-align: left;">
                <p><b>Flex Check-In Question :</b></p>
                <p>
                    <textarea name="qmonth1" size="400" rows="10" cols="80" style="height:200px; width:400px;"/><?php if (strlen($qmonth1)>0){echo(htmlent_utf8($qmonth1));};?></textarea>
                </p>

                <!--Remove Code for 1 Questions per link modification.
                -->

                <p>
                    <textarea name="gift" size="400" rows="10" cols="80" style="display:none;"/><?php echo($gift);?></textarea>
                </p>

                <input type="submit" value="Save" class="btn btn-info">
                <input type=submit class="btn btn-info" name=doCancel onclick="location='<?php echo(addSession('index.php'));?>'; return false;" value="Cancel"></p>
            </form>
            <?php
        } else {
            ?>

            <form method="post" style="display: inline-block;margin-left:5%;text-align: left;">
                <p><b>Flex Check-In Question :</b></p>
                <p>
                    <textarea readonly name="qmonth1" size="400" rows="10" cols="80" style="height:200px; width:400px;"/><?php if (strlen($qmonth1)>0){echo(htmlent_utf8($qmonth1));};?></textarea>
                </p>

                <!--Remove Code for 1 Questions per link modification.
                -->

                <p>
                    <textarea name="gift" size="400" rows="10" cols="80" style="display:none;"/><?php echo($gift);?></textarea>
                </p>

                <p>You cannot update any questions until

                    <?php
                    if (date('d') < 20) {
                        echo date('M');
                    } else {
                        echo date('M', strtotime('+1 month'));
                    }

                    $formatStyle = new NumberFormatter('en_US', NumberFormatter::SPELLOUT);
                    $formatStyle->setTextAttribute(NumberFormatter::DEFAULT_RULESET, "%spellout-ordinal");

                    echo " 20th.";
                    ?>

                </p>

                <input type=submit class="btn btn-info" name=doCancel onclick="location='<?php echo(addSession('index.php'));?>'; return false;" value="Go Back"></p>

            </form>
            <?php
        }

        ?>
        <!-- IF not within dates, THEN gray out submit button and place text say you can't do this -->
        <!--        <input type="submit" value="Save" class="btn btn-info">-->
    </div>
<?php

$OUTPUT->footer();
