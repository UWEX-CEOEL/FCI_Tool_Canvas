<?php

$readonly = false;
$displayPrevious = 1;
$resetFlag = 0;

if (! $userRole) {

$sql = "SELECT fci_state FROM lti_result WHERE result_id = :resultId";
$result = $PDOX->queryDie($sql, array(
    ':resultId' => $resultId
));

foreach ($result as $fciLine) {
    $fciState = $fciLine['fci_state'];
}

if ($fciState == 5) {
    $readonly = true;
}

}


?>

<script id="essay_question" type="text/x-handlebars-template">

	<p style="clear:both;font-weight: bold;"> Flex Check-In Question : </p>
    <p style="max-width: 600px;">
    {{#if scored}}
        <i class="fa fa-check text-success"></i>
    {{/if}}
	{{{question}}}
	</p>

	<p style="clear:both;font-weight: bold;">Please Insert or Update Your Answer Below :</p>
	<p style="clear:both;font-weight: bold;">You must click the submit button at bottom of page to save your work and complete the FCI requirement</p>


<?php
if (isset($readonly)) {
if ($readonly) {
?>

    <textarea id="studentResponse" readonly type="text" name="{{code}}" size="400" rows="10" cols="80"> <?php echo ""; ?> </textarea>
<?php
} else {
?>

    <textarea id="studentResponse" type="text" name="{{code}}" value="{{value}}" size="400" rows="10" cols="80"/>

<?php
}
}
?>

	<p style="clear:both;font-weight: bold;">Instructor Feedback: </p>
	<textarea readonly name="feedback" id="feedback" size="400" rows="5" cols="80"/>

    <p style="clear:both;font-weight: bold;">Completed : <input id="completed_chbx" type="checkbox" name="completed_chbx" size="20" rows="1" cols = "5" disabled readonly/></p>

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
