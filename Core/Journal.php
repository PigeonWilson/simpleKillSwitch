<?php
if (!defined('PREVENT_DIRECT_FILE_ACCESS_CONST')) die();
/*
 * Logging capabilities
 * This is used directly at the $_REQUEST level, hence capturing
 * everything.
 *
 * It uses a different connection string because its purpose is to
 * send the information into a different database
 */
class Journal
{
    private T $_;

    function __construct(string $db_hostname, string $db_name, string $db_username, string $db_password)
    {
        $this->_ = new T($db_hostname, $db_name, $db_username, $db_password);
    }

    public function newEntry
    (
        string $method,
        array $request,
        array $request2,
        string $input,
        string $ip,
        string $userAgent,
        string $currentScript
    ) : void
    {
        $this->_->db->create('Entry',
            [
                'id' => NULL,
                'method' => $method,
                'request' => json_encode($request, JSON_PRETTY_PRINT),
                'request2' => json_encode($request2, JSON_PRETTY_PRINT),
                'input' => $input,
                'ip' => $ip,
                'userAgent' => $userAgent,
                'currentScript' => $currentScript
            ]);
    }

    // none of these reason will ever appear on the frontend. There is a reason why good program die silently*
    // except when SETUP_MODE is set to true in the loader.
    public const JOURNAL_SYSTEM_ENTRY_DIE_REASON_NOT_AUTHENTICATED = 'not authenticated. The program will now die. ';

    /*
     * this function is meant to be used whenever the program would die
     * Contrary to what the word means in our world, a program die
     * for a variety of reason.
     *
     * Example: Don't have the permission to run that? Bam, die.
     * */
    public function newSystemEntry(string $reason, array $data = null) : void
    {
        $this->_->db->create('SystemEntry',
            [
                'id' => NULL,
                'reason' => $reason,
                'data' => json_encode($data, JSON_PRETTY_PRINT)
            ]);
    }
}