<?php
if (!defined('PREVENT_DIRECT_FILE_ACCESS_CONST')) die();
class T
{
    // math challenge
    private array $mathSymbols = ['+', '-', '*'];

    public Db $db;

    function __construct($dbHost, $dbName, $dbUsername, $dbPassword)
    {
        if (!isset($_SESSION)) die ('sessionStorage is missing');

        $this->db = new Db($dbHost, $dbName, $dbUsername, $dbPassword);
    }

    public function kickout(string $reason = '') : void
    {
        $this->flushSession();
        unset($_SESSION);
        if (!isset($_SESSION)) session_start();
        header('Location: ' . NEBULA_ERROR . "?reason=$reason");
        die();
    }

    public function location(string $url) : void
    {
        header('Location: ' . $url);
        die();
    }

    public function echo(string $content) : void
    {
        echo $this->clean($content);
    }

    public function clean(string $content) : string
    {
        $result = htmlspecialchars(strip_tags($content), ENT_QUOTES, 'UTF-8');
        $result = str_replace(['<', '>','(',')',';'], '', $result);
        return $result;
    }

    public function getScriptName() : string
    {
        $scriptName = $this->clean($_SERVER["SCRIPT_NAME"]);
        return substr($scriptName,strrpos($scriptName,"/")+1);
    }

    public function random_str
    (
        int $length = 64,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i)
        {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }

    public function random_str_chain(int $wordsCount = 6) : string
    {
        $result = 'S' . random_int(0, 100);
        for ($index = 0; $index < $wordsCount; $index++)
        {
            $result .= '-' . random_int(0, 100) . array_values(PUBLIC_COMMON_STRING)[random_int(0, count(PUBLIC_COMMON_STRING) - 1)];
        }

        return  $result;
    }

    // session related
    public function addToSession(string $key, $content) : bool
    {
        if (!isset($_SESSION)) return false;
        if (isset($_SESSION[strtolower($key)])) return false;
        if (!isset($_SESSION[strtolower($key)]))
        {
            $_SESSION[strtolower($key)] = serialize($content);

            if (isset($_SESSION[strtolower($key)])) return true;
            if (!isset($_SESSION[strtolower($key)])) return false;
        }

        return false;
    }

    public function removeFromSession(string $key) : bool
    {
        if (!isset($_SESSION)) return false;
        if (!isset($_SESSION[strtolower($key)])) return false;
        unset($_SESSION[strtolower($key)]);

        if (isset($_SESSION[strtolower($key)])) return false;
        if (!isset($_SESSION[strtolower($key)])) return true;

        return false;
    }

    public function updateToSession(string $key, $content) : bool
    {
        if (!isset($_SESSION)) return false;
        if (!isset($_SESSION[strtolower($key)])) return false;
        if (isset($_SESSION[strtolower($key)]))
        {
            $_SESSION[strtolower($key)] = serialize($content);
            return true;
        }

        return false;
    }

    public function getFromSession(string $key)
    {
        if (!isset($_SESSION)) return '';
        if (isset($_SESSION[strtolower($key)])) return unserialize($_SESSION[strtolower($key)]);
        return '';
    }

    public function sessionEntryExist(string $key) : bool
    {
        $flag = true;
        if (!isset($_SESSION[strtolower($key)])) $flag = false;
        if (isset($_SESSION[strtolower($key)]) && $_SESSION[strtolower($key)]  == '') $flag = false;
        return $flag;
    }

    public function flushSession()
    {
        session_destroy();
        unset($_SESSION);
        if (!isset($_SESSION)) session_start();
    }
    //
    //


    public function mathChallenge() : string
    {
        $value1 = random_int(1, 100);
        $value2 = random_int(1, 100);

        return $value1 . ' ' . $this->mathSymbols[random_int(0, count($this->mathSymbols) - 1)] . ' ' . $value2;
    }

    public function evaluate(string $expression, string $answer) : bool
    {
        $flag = false;
        $realAnswer = eval('return '.$expression.';');
        if ($this->clean($answer) == $realAnswer) $flag = true;

        return $flag;
    }

    public function compare($userSourced, $systemSourced) : bool
    {
        $flag = true;
        if (strlen($this->clean($userSourced)) == 0) $flag = false;
        if (strlen($systemSourced) == 0) $flag = false;
        if ($this->clean($userSourced) != $systemSourced) $flag = false;

        return $flag;
    }

    private array $hashOptions = [
        'cost' => 16, // max 31
    ];

    public function hashValue(string $string) : string
    {
        return password_hash($string, PASSWORD_BCRYPT, $this->hashOptions);
    }

    public function compareHash(string $original, string $thisNewThing) : bool
    {
        return password_verify($thisNewThing, $original);
    }

}
