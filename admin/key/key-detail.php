<?php
// In the top frame, we use cookies for session.
if (!defined('COOKIE_SESSION')) define('COOKIE_SESSION', true);
require_once("../../config.php");
require_once("../../admin/admin_util.php");

use \Tsugi\Util\U;
use \Tsugi\UI\CrudForm;

\Tsugi\Core\LTIX::getConnection();

header('Content-Type: text/html; charset=utf-8');
session_start();
require_once("../gate.php");
if ( $REDIRECTED === true || ! isset($_SESSION["admin"]) ) return;

if ( ! isAdmin() ) {
    die('Must be admin');
}

$inedit = U::get($_REQUEST,'edit');

$tablename = "{$CFG->dbprefix}lti_key";
$current = $CFG->getCurrentFileUrl(__FILE__);
$from_location = "keys";
$allow_delete = true;
$allow_edit = true;
$where_clause = '';
$query_fields = array();
$fields = array('key_id', 'key_key', 'secret', 'deploy_key', 'issuer_id',
     'caliper_url', 'caliper_key', 'created_at', 'updated_at', 'user_id');
$realfields = array('key_id', 'key_key', 'key_sha256', 'secret', 'deploy_key', 'deploy_sha256', 'issuer_id',
     'caliper_url', 'caliper_key', 'created_at', 'updated_at', 'user_id');

$titles = array(
    'key_key' => 'LTI 1.1: OAuth Consumer Key',
    'secret' => 'LTI 1.1: OAuth Consumer Secret',
    'deploy_key' => 'LTI 1.3: Deployment ID (from the Platform)',
    'issuer_id' => 'LTI 1.3: Issuer',
);

// Handle the post data
$row =  CrudForm::handleUpdate($tablename, $realfields, $where_clause,
    $query_fields, $allow_edit, $allow_delete, $titles);

if ( $row === CrudForm::CRUD_FAIL || $row === CrudForm::CRUD_SUCCESS ) {
    header('Location: '.$from_location);
    return;
}

if ( ! $inedit && U::get($row, 'issuer_id') > 0 ) {
    $issuer_row = $PDOX->rowDie("SELECT issuer_key FROM {$CFG->dbprefix}lti_issuer WHERE issuer_id = :issuer_id",
        array(':issuer_id' => U::get($row, 'issuer_id'))
    );
    if ( $issuer_row ) {
        $row['issuer_id'] = $issuer_row['issuer_key'];
    }
}

$OUTPUT->header();
$OUTPUT->bodyStart();
$OUTPUT->topNav();
$OUTPUT->flashMessages();

$title = 'Tenant Entry';
echo("<h1>$title</h1>\n<p>\n");
$extra_buttons=false;
$row['lti13_tool_keyset_url'] = $CFG->wwwroot . '/lti/keyset?key_id=' . $row['key_id'];
$retval = CrudForm::updateForm($row, $fields, $current, $from_location, $allow_edit, $allow_delete,$extra_buttons,$titles);
if ( is_string($retval) ) die($retval);
echo("</p>\n");
?>
<p>
A single entry in this table defines a "distinct tenant" in Tsugi.
Data in Tsugi is isolated to a tenant.  You can route both
LTI 1.1 and LTI 1.3 launches to one tenant by setting fields on
this entry properly.  See below for details.
</p>
<p>
For LTI 1.1, set the <b>oauth_consumer_key</b> and <b>secret</b>.
For LTI 1.3, you first need to create or lookup an issuer/client_id and note its
integer primary key and enter it here (we will make a drop-down UI later).  You also need the
<b>deployment_id</b> for this integration from the LMS.
</p>
<p>
To receive both LTI 1.1 and LTI 1.3 launches to this "tenant", simply set all four fields.
</p>
<p>
If this is a pre-existing LTI 1.1 tenant, the LMS must have the <b>oauth_consumer_key</b>
and <b>secret</b> connected to its LTI 1.3 launches, and then Tsugi can link the accounts
and courses regardless of the type of launch.  For this to work, the LMS must support
LTI Advantage legacy LTI 1.1 support.
<p>
<?php
$OUTPUT->footerStart();

if ( $inedit ) {
    $sql = "SELECT issuer_id, issuer_key
        FROM {$CFG->dbprefix}lti_issuer";
    $issuer_rows = $PDOX->allRowsDie($sql);

    var_dump($row);
    $select_text = "<select id=\"issuer_id_select\"><option value=\"\">No Issuer Selected</option>";
    foreach($issuer_rows as $issuer_row) {
        $selected = $row['issuer_id'] == $issuer_row['issuer_id'] ? ' selected ' : '';
        $select_text .= '<option value="'.$issuer_row['issuer_id'].'"'.$selected.'>'.htmlentities($issuer_row['issuer_key'])."</option>";
    }
    $select_text .= "</select>";
    echo(htmlentities($select_text));

?>
<script>
    $('<?= $select_text ?>').insertBefore('#issuer_id');
    $('#issuer_id').hide();
    $('#issuer_id_select').on('change', function() {
    $('input[name="issuer_id"]').val(this.value);
    });
</script>
<?php
}

$OUTPUT->footerEnd();
