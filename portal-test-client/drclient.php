<?php

require 'vendor/autoload.php';

//
// client env, will be loaded from configuration
//

define('IS_APIC_TEST', False);

define('APIC_DYNA_CLIENT_HOST', '');
define('APIC_DYNA_CLIENT_PATH', '');

define('APIC_BEARER_TOKEN_PATH','');
define('APIC_BEARER_TOKEN_CLIENT_ID', '');
define('APIC_BEARER_TOKEN_CLIENT_SECRET', '');
define('APIC_BEARER_TOKEN_USER_NAME', '');
define('APIC_BEARER_TOKEN_PASSWORD', '');
define('APIC_BEARER_TOKEN_SCOPE', '');

define('APIC_TEST_X_IBM_CLIENT_ID', '');

define('APIC_TEST_ANALYTICS_URL', '');

//
// end-of client-env
//

function appcreds_sync_log_error($message, $context = array()) {
//    \Drupal::logger(APIC_APPCREDS_SYNC_LOG_CHANNEL)->error($message, $context);
    print_r(sprintf("%s\n", $message));
}

function build_dyna_client_url() {
    $dyna_client_host = APIC_DYNA_CLIENT_HOST;
    $dyna_client_path = APIC_DYNA_CLIENT_PATH;

    return $dyna_client_host . $dyna_client_path;
}

function build_bearer_token_url() {
    $token_host = APIC_DYNA_CLIENT_HOST;
    $token_path = APIC_BEARER_TOKEN_PATH;

    return $token_host . $token_path;
}

/**
 * @param $app
 * @param $path
 */
function appcreds_post_analytics($app, $path) {

    $client = new GuzzleHttp\Client();

    $url = APIC_TEST_ANALYTICS_URL.$path;

    $x_ibm_client_id = APIC_TEST_X_IBM_CLIENT_ID;

    try {
        $resp = $client->post($url, [
            'json' => $app,
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-IBM-Client-Id' => $x_ibm_client_id,
            ]
        ]);

        $b = $resp->getStatusCode();
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        appcreds_sync_log_error($e->getMessage());
    }
}

/**
 * @return array
 */
function access_token() {
    //
    // could be preconfigured initial access token
    // or bearer access token
    //

    // return initial_access_token();
    return bearer_access_token();
}

function is_status_code_ok($status_code) {
    return $status_code >= 200 && $status_code < 300;
}

/**
 * @return array
 */
function bearer_access_token() {

    $client_id = APIC_BEARER_TOKEN_CLIENT_ID;
    $client_secret = APIC_BEARER_TOKEN_CLIENT_SECRET;

    $username = APIC_BEARER_TOKEN_USER_NAME;
    $password = APIC_BEARER_TOKEN_PASSWORD;

//  $scope = APIC_BEARER_TOKEN_SCOPE;

    // post
    $form_params = [
        'grant_type' => 'password',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'username' => $username,
        'password' => $password
    ];

    if (IS_APIC_TEST) {
        $form_params['scope'] = APIC_BEARER_TOKEN_SCOPE;
    }

    try {
        $client = new \GuzzleHttp\Client();

        $resp = $client->post(build_bearer_token_url(), [
            'form_params' => $form_params,
            'verify' => false,
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);

        $statusCode = $resp->getStatusCode();
        if (!is_status_code_ok($statusCode)) {
            return [];
        }

        $b = $resp->getBody()->getContents();

        // $b is json encoded string
        $json = json_decode($b);

        $token_type = $json->{'token_type'};
        $access_token = $json->{'access_token'};

        // return token array
        $tokarr = [];
        if (isset($token_type)) {
            $tokarr['token_type'] = $token_type;
        }

        if (isset($access_token)) {
            $tokarr['access_token'] = $access_token;
        }

        return $tokarr;

    } catch (\GuzzleHttp\Exception\RequestException $e) {
        appcreds_sync_log_error($e->getMessage());
    }

    return [];
}

function encode_bearer_token_header($token) {
    return sprintf("Bearer %s", $token);
}

/**
 * @param array $rh
 * @param string $token
 * @return array
 */
function appcreds_dyna_client_create($rh, $token) {

    try {
        $client = new GuzzleHttp\Client();

        $hdrs = [
            'Authorization' => encode_bearer_token_header($token),
            'Accept' => 'application/json'
        ];

        if (IS_APIC_TEST) {
            $hdrs['X-IBM-Client-Id'] = APIC_TEST_X_IBM_CLIENT_ID;
        }

        $resp = $client->post(build_dyna_client_url(), [
            'json' => $rh,
            'verify' => false,
            'headers' => $hdrs
        ]);

        $statusCode = $resp->getStatusCode();
        if (!is_status_code_ok($statusCode)) {
            return [];
        }

        $b = $resp->getBody()->getContents();
        $json = json_decode($b);

        $resp_client_id = $json->{'clientId'};
        $resp_secret = $json->{'secret'};
        $resp_token = $json->{'registrationAccessToken'};

        $resp = []; // response array

        if (isset($resp_client_id)) {
            $resp['clientId'] = $resp_client_id;
        }

        if (isset($resp_secret)) {
            $resp['secret'] = $resp_secret;
        }

        if (isset($resp_token)) {
            $resp['registrationAccessToken'] = $resp_token;
        }

        return $resp;

    } catch (\GuzzleHttp\Exception\RequestException $e) {
        appcreds_sync_log_error($e->getMessage());
    }

    return [];
}

/**
 * @param string $clientid
 * @param string $token
 * @return bool
 */
function appcreds_dyna_client_delete($clientid, $token) {

    $url = build_dyna_client_url();

    if ($url[strlen($url)-1] == '/') {
        $url = $url . $clientid;
    } else {
        $url = $url . '/' . $clientid;
    }

    try {
        $client = new GuzzleHttp\Client();

        $hdrs = [
            'Authorization' => encode_bearer_token_header($token)
        ];

        if (IS_APIC_TEST) {
            $hdrs['X-IBM-Client-Id'] = APIC_TEST_X_IBM_CLIENT_ID;
        }

        $resp = $client->delete($url, [
            'verify' => false,
            'headers' => $hdrs
        ]);

        $statusCode = $resp->getStatusCode();
        if (!is_status_code_ok($statusCode)) {
            return false;
        }

        return true;

    } catch (\GuzzleHttp\Exception\RequestException $e) {
        appcreds_sync_log_error($e->getMessage());
    }

    return false;
}

//
// test...
//

//
// get access token
//
$tokarr = access_token();
print_r($tokarr);

$access_token = $tokarr['access_token'];

//
// create client
//
$client_reg = [];
$client_reg['name'] = 'app1-clientid100';
$client_reg['clientId'] = 'client100';
$client_reg['secret'] = 'app1-clientid100-secret';
$client_reg['redirectUris'] = ['https://app1.bla.com', 'https://app1.foo.com'];

$resp = appcreds_dyna_client_create($client_reg, $access_token);
if (empty($resp)) {
    return;
}

print_r($resp);

//
// delete client
//
$client_id = $client_reg['clientId'];
$reg_token = $resp['registrationAccessToken'];

$ok = appcreds_dyna_client_delete($client_id, $reg_token) ? "true": "false";
print_r(sprintf("dyna-client-delete-rc: %s\n", $ok));
