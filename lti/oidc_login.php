<?php

// https://openid.net/specs/openid-connect-core-1_0.html#AuthRequest
//
use \Tsugi\Util\U;
use \Tsugi\Util\LTI13;
use \Firebase\JWT\JWT;
use \Tsugi\Core\LTIX;

require_once "../config.php";

// target_link_uri and lti_message_hint are not required by Tsugi
$login_hint = U::get($_REQUEST, 'login_hint');
$iss = U::get($_REQUEST, 'iss');

// echo("<pre>\n");var_dump($_REQUEST); LTIX::abort_with_error_log();

if ( ! $login_hint ) {
    LTIX::abort_with_error_log('Missing login_hint');
}

if ( ! $iss ) {
    LTIX::abort_with_error_log('Missing iss');
}

$PDOX = \Tsugi\Core\LTIX::getConnection();

$key_sha256 = LTI13::extract_issuer_key_string($iss);

error_log("iss=".$iss." sha256=".$key_sha256);

$row = $PDOX->rowDie(
    "SELECT issuer_client, lti13_oidc_auth
    FROM {$CFG->dbprefix}lti_issuer
    WHERE issuer_sha256 = :SHA AND issuer_client IS NOT NULL AND lti13_oidc_auth IS NOT NULL",
    array(":SHA" => $key_sha256)
);

if ( ! is_array($row) || count($row) < 1 ) {
    LTIX::abort_with_error_log('Unknown or improper iss');
}
$client_id = trim($row['issuer_client']);
$redirect = trim($row['lti13_oidc_auth']);

$signature = \Tsugi\Core\LTIX::getBrowserSignature();

$payload = array();
$payload['signature'] = $signature;
$payload['time'] = time();
if ( U::get($_REQUEST,'target_link_uri') ) {
    $payload['target_link_uri'] = $_REQUEST['target_link_uri'];
}

$state = JWT::encode($payload, $CFG->cookiesecret, 'HS256');

$redirect = U::add_url_parm($redirect, "scope", "openid");
$redirect = U::add_url_parm($redirect, "response_type", "id_token");
$redirect = U::add_url_parm($redirect, "response_mode", "form_post");
$redirect = U::add_url_parm($redirect, "prompt", "none");
$redirect = U::add_url_parm($redirect, "nonce", uniqid());

// client_id - Required, per OIDC spec, the tool’s client id for this issuer.
$redirect = U::add_url_parm($redirect, "client_id", $client_id);
$redirect = U::add_url_parm($redirect, "login_hint", $login_hint);
if ( U::get($_REQUEST,'lti_message_hint') ) {
    $redirect = U::add_url_parm($redirect, "lti_message_hint", $_REQUEST['lti_message_hint']);
}
$redirect = U::add_url_parm($redirect, "redirect_uri", $CFG->wwwroot . '/lti/oidc_launch');
$redirect = U::add_url_parm($redirect, "state", $state);

error_log("oidc_login redirect: ".$redirect);
header("Location: ".$redirect);

