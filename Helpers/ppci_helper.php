<?php
use CodeIgniter\CodeIgniter;

/**
 * Affiche le nom et le contenu d'une variable
 * @param $tableau
 */
function printr($tableau, $mode_dump = 0, $force = false)
{
    global $APPLI_modeDeveloppement;
    if ($APPLI_modeDeveloppement || $force) {
        if ($mode_dump == 1) {
            var_dump($tableau);
        } else {
            if (is_array($tableau)) {
                print_r($tableau);
            } else {
                $content .= $tableau;
            }
        }
        $content .= "<br>";
    }
    return $content;
}

function test($content = "")
{
    global $testOccurrence;
    $nl = getLineFeed();
    if (!isset($testOccurrence)) {
        $testOccurrence == 1;
    }
    $retour = "test $testOccurrence : $content  <br>";
    $testOccurrence++;
    echo $retour . $nl;
}
/**
 * Display the content of a variable
 * with a structured format
 *
 * @param $arr
 * @param integer $level
 * @return string
 */
/**
 * Display the content of a variable
 * with a structured format
 *
 * @param $arr
 * @param integer $level
 * @return void
 */
function printA($arr, $level = 0, $exclude = array())
{
    $childLevel = $level + 1;
    $nl = getLineFeed();

    if (is_array($arr)) {
        foreach ($arr as $key => $var) {
            if (!in_array($key, $exclude)) {
                if (is_object($var)) {
                    $var = (array) $var;
                    $key .= " (object)";
                }
                for ($i = 0; $i < $level * 4; $i++) {
                    echo "&nbsp;";
                }
                echo $key . ": ";
                if (is_array($var)) {
                    echo $nl;
                    printA($var, $childLevel, $exclude);
                } else {
                    print_r($var);
                    echo $nl;
                }
            }
        }
    } else {
        echo "$arr" . $nl;
    }
}

function getLineFeed()
{
    if (is_cli()) {
        return PHP_EOL;
    } else {
        return "<br>";
    }
}

function setLogRequest($request, $comment = null)
{
    $log = service("Log");
    if (isset($_SESSION["login"])) {
        $login = $_SESSION["login"];
    } else if (isset($_REQUEST["login"])) {
        $login = $_REQUEST["login"];
    } else {
        $login = "Unknown";
    }
    $module = $request->getUri()->getRoutePath();
    $db = db_connect();
    $db->query("set search_path = " . $_ENV["database.default.searchpath"]);
    $log->setLog($login, $module, $comment);
}