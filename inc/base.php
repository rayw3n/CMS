<?php
$installation = false;
if (file_exists("../inc/config.php")) {
    include("../inc/config.php");
}

spl_autoload_register(null, false);
spl_autoload_extensions('.class.php');
spl_autoload_register('classLoader');

function classLoader($class)
{
    $classname = strtolower($class);
    $filename = $classname . '.class.php';
    $path['core'] = '../inc/core/';
    $path['lib'] = '../inc/lib/';
    $path['modules'] = '../inc/core/site_modules/';
    $path['servermanager'] = '../inc/core/servermanager/';
    foreach ($path as $dir) {
        if (is_readable($dir . $filename)) {
            include $dir . $filename;
            return true;
        } else if (is_readable($dir . $classname . '/' . $classname . '.php')) {

            include $dir . $classname . '/' . $classname . '.php';
            return true;
        }
    }
    return false;
}

Config::init();
Db::init(Config::$sql);
Config::loadSettings();
Config::loadLanguage();
Auth::checkStatus();

//Parameter auslesen für allegemeine Settings
if (isset($_GET['show'])) {
    $show = $_GET['show'];
} else {
    $show = "";
}
if (isset($_GET['do'])) {
    $do = $_GET['do'];
} else {
    $do = "";
}
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = "";
}

//Allegmeine Pfade setzten durch Resultat der Parameter
$disp = "";
$case = array();
$path['dir'] = $config['dir'];
$path['content'] = "../content/";
$path['include'] = "../inc/";
$path['upload'] = $path['content'] . "upload/";
$path['images'] = $path['content'] . "images/";
$path['plugins'] = $path['include'] . "plugins/";
$path['pages'] = "../pages/";
$path['panels'] = $path['include'] . "panels/";
$path['dyn_panels'] = $path['include'] . "panels_dyn/";
$file['functions'] = $path['include'] . "functions.php";
$path['lang'] = $path['content'] . "language/";

$path['style'] = "../templates/" . Config::$settings->style . "/";
$path['css'] = $path['style'] . "_css/";
$path['js'] = $path['style'] . "_js/";
$path['style_index'] = $path['style'] . "index.html";

require_once($file['functions']);

function dbConnect()
{
    global $config;
    if ($config['sql_host'] != '' && $config['sql_user'] != '' && $config['sql_pass'] != '' && $config['sql_db'] != '') {
        if (!$db_link = mysqli_connect($config['sql_host'], $config['sql_user'], $config['sql_pass'], $config['sql_db'])) {
            die("Fehler beim Zugriff auf die Datenbank!");
        } else {
            mysqli_query($db_link, "SET NAMES 'utf8'");
            return $db_link;
        }
    } else {
        die("Es wurden nicht alle Datenbank Daten zur Verbindung angegeben");
    }
}

function _assoc($fetch)
{
    if (array_key_exists('_stmt_rows_', $fetch)) {
        return $fetch[0];
    } else {
        return $fetch->fetch_assoc();
    }
}

function db($input = "", $mysqli_action = null)
{
    if (!$qry = mysqli_query(dbConnect(), $input)) {
        return false;
    }

    if ($mysqli_action != null) {
        switch ($mysqli_action) {
            case 'array':
                $qry = mysqli_fetch_array($qry);
                break;
            case 'rows':
                $qry = mysqli_num_rows($qry);
                break;
            case 'object':
                $qry = mysqli_fetch_object($qry);
                break;
        }
    }
    return ($qry);
}

function up($input = "")
{
    if (!mysqli_query(dbConnect(), $input)) {
        die($input);
    }
    return true;
}