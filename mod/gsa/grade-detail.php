<?php
require_once "../config.php";
require_once "parse.php";

use \Tsugi\Util\LTI;
use \Tsugi\Core\Settings;
use \Tsugi\Core\Result;
use \Tsugi\UI\SettingsForm;

use \Tsugi\Core\Cache;
use \Tsugi\Core\LTIX;
use \Tsugi\Grades\GradeUtil;

$LAUNCH = LTIX::requireData();
$userRole = $USER->determineUserRole($USER->id);

// Get the user's grade data also checks session
$row = GradeUtil::gradeLoad($_REQUEST['user_id']);
$gift = $LINK->getJson();

echo('<div style="text-align: center;">');
echo('<div style ="display: inline-block; text-align: left;margin-top: 2%">');
echo('<a href="grades.php" class="btn btn-info">Back to All Submissions</a>');
echo('<h3 style="background-color: lightgrey;border-width: thick; border-radius: 5px;padding: 15px;">Flex Check-In Assignment : (Instructor Feedback)</h3>');
// Show the basic info for this user
    GradeUtil::gradeShowInfo($row);
echo("</div></div>");

// Parse out answers
$json = json_decode($row['json']);

//Parse existing gift file for questions
{
    $questions = array();
    $errors = array();
    parse_gift($gift, $questions, $errors);
    $qmonth1="";

    if(isset($questions[0])){
        $qmonth1=$questions[0]->question;
        $qcode1=$questions[0]->code;
    }
}

if ( isset($_POST) ) {
    if(empty($json->grade)){
        $grade = array();
    } else {
        $grade = (array) $json->grade;
    }

    /*echo("<p> gift JSON :</p>\n");
    echo("<pre>\n");
    echo(htmlentities(json_encode($_POST, JSON_PRETTY_PRINT)));
    echo("\n</pre>\n");*/
    //If Completion and Feedback posted is unchanged then ignore

    if(isset($_POST['grade1']))
    {
        //echo("Posted Grade:".$_POST['grade1']);
        //if($_POST['grade1']==="on"||$_POST['grade1']==="M"||$_POST['grade1']==="MD"){
            $grade[$qcode1]=$_POST['grade1'];
            if(isset($_POST['feedback'])){
                $grade['feedback']=$_POST['feedback'];
            }
            //echo("Posted Grade:".$_POST['grade1']." Stored Grade:".$grade[$qcode1]);
        //}

        // grade toggle
        $json->grade =$grade;
        if(isset($grade[$qcode1])){
            if($grade[$qcode1]==="Completed"){
                $gradetosend=1.00;
            }else{
                $gradetosend=0;
            }
            $debug_log=array();
            //Fetch row of lti_result to send grade
            $resultRow=$PDOX->rowDie("SELECT * FROM {$CFG->dbprefix}lti_result WHERE result_id = :RID",
                array( ':RID' => $row['result_id']
                )
            );
            $retval = LTIX::gradeSend($gradetosend,$resultRow, $debug_log);
            $_SESSION['debug_log'] = $debug_log;
        }

        $stmt = $PDOX->queryDie(
        "UPDATE {$CFG->dbprefix}lti_result SET json = :json, instructor_id = :instructor_id, instructor_updated=NOW() WHERE result_id = :RID",
        array(
            ':json' => json_encode($json),
            ':RID' => $row['result_id'],
            ':instructor_id' => $USER->id)
        );
	    
// 	    $stmt = $PDOX->rowDie(
// 		        "UPDATE {$CFG->dbprefix}lti_result SET json = :json, instructor_id = :instructor_id, instructor_updated=NOW() WHERE result_id = :RID",
//         array(
//             ':json' => json_encode($json),
//             ':RID' => $row['result_id'],
//             ':instructor_id' => $USER->id)
// 		    );

        ?>

        <script type="text/javascript">
            location='<?php echo(addSession('grades.php'));?>';
        </script>

        <?php
    }
}

if ( is_object($json) ) {
    /*
    echo("<p> Stored JSON :</p>\n");
    echo("<pre>\n");
    echo(htmlentities(json_encode($row,*/
    $oldgrade1="";
    $oldfeedback="";
    if(isset($json->grade)){
        $oldgrades = (array) $json->grade;
        if (isset($oldgrades[$qcode1])){
            $oldgrade1=$oldgrades[$qcode1];
        }
        if (isset($oldgrades['feedback'])){
            $oldfeedback=$oldgrades['feedback'];
        }
    }

    $answer1="(No Response)";
    if(isset($json->submit)){
        $student_submit = (array) $json->submit;
        if (isset($student_submit[$qcode1])){
            $answer1=$student_submit[$qcode1];
        }
    }

}

require_once('templates.php');
// View
$OUTPUT->header();
?>

<div style="text-align: center;">
    <form method="post" style="display: inline-block; text-align: left;margin-top: 10px;">

    <h3>Flex Check-In Question </h3>

        <textarea readonly rows="3" cols="80"><?php
        echo(htmlent_utf8($qmonth1));
        ?></textarea>

    <h3>Student Response </h3>
        <p>
            <textarea readonly name="answer1" size="400" rows="10" cols="80" /><?php
			if(isset($answer1)){
            echo(htmlent_utf8($answer1));
			}else{
			 echo('(No Response)');
			}
            ?></textarea>
        </p>
    <h3>Instructor Feedback </h3>
    <?php
    if (!$userRole) {
    ?>
        <p>
            <textarea name ="feedback" size="100" rows="5" cols="80"><?php
				if(isset($oldfeedback)){
                echo(htmlent_utf8($oldfeedback));
				}
                ?></textarea>
            <br>
        </p>
        <p>Completed:
            <input type='hidden' value='Incomplete' name='grade1'>
            <input type='checkbox' name='grade1' value='Completed' <?php if($oldgrade1 === 'Completed' || $oldgrade1 == null){echo('checked');} ?>>
            <br><br>
        </p>
        <input type="submit" value="Submit" class="btn btn-info" >
    <?php
    } else {
    ?>
        <p>
            <textarea name ="feedback" size="100" rows="5" cols="80"><?php
				if(isset($oldfeedback)){
                echo(htmlent_utf8($oldfeedback));
				}
                ?></textarea>
            <br>
        </p>
        <p>Completed:
            <input type='hidden' value='Incomplete' name='grade1'>
            <input type='checkbox' name='grade1' value='Completed' <?php if($oldgrade1 === 'Completed' || $oldgrade1 == null){echo('checked');} ?>>
            <br><br>
        </p>
        <input type="submit" value="Submit" class="btn btn-info" >

    <?php
        }
    ?>

        <input type=submit name=doCancel  class="btn btn-info" onclick="location='<?php echo(addSession('grades.php'));?>'; return false;" value="Back">

        <input type=submit name=doCancel  class="btn btn-info" onclick="location='<?php
                $currentStudent = $row['user_id'];
                $currentResult = $row['result_id'];
                echo(addSession('feedback-display.php?student=' . $currentStudent . '&result=' . $currentResult));
        ?>'; return false;" value="View Feedback">
        </p>
    </form>
</div>


<?php
$OUTPUT->footer();
