<?php

define('HOSTNAME', 'http://localhost');

/*  URL rewriting rule changes the way server handles requests.
  Use the relative URL of the application folder as the base URL for templates and scripts
 */
define('HOME', HOSTNAME . '/api1.0');
/* * ******************************************************************************************* */

// Define database connection
require 'config.php';
require 'db.php';
$db = Db::getDb(); // establish database connection


/* * *************************************************************************************
  Request should be in the following form:
  http://HOME/DBtable/method/parameter

  The URL rewriting rule  converts the "DBtable/method/parameter" string into the following 'key=value' format:
  'action=DBtable/method/parameter'

 * **************************************************************************************** */
// get the HTTP method, path and body of the request
$requestType = $_SERVER['REQUEST_METHOD'];

/*   Define tables and available actions.
 *   List all the available functions and tables.
 */

$tables = array('course', 'subject');
$methods = array('description', 'subjectList', 'courseList');

// Define a variable to store request action
$action = '';
// Read request action
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
}

// Read the request data
$request_data = explode('/', $action);


// Define the database table
$table = (isset($request_data[0]) && (!empty($request_data[0])) && in_array($request_data[0], $tables)) ? $request_data[0] : false;
// Define action
$method = (isset($request_data[1]) && (!empty($request_data[1])) && in_array($request_data[1], $methods)) ? $request_data[1] : false;
// Define parameter
$parameter = (isset($request_data[2]) ) ? $request_data[2] : null;




// Include headers for cross-domain access control
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods", "GET,HEAD,OPTIONS,POST,PUT, DELETE");
header("Access-Control-Allow-Headers", "Content-Type");

/*  Validate URL
 *   The URL should be in the format apix.x/table/action/parameter
 *   Only the defined tables and actions are permitted.
 */
if (!($table && $method ) || ($requestType !== 'GET')) {
    echo "Invaid URI";
    exit();
}

echo json_encode($method($parameter));

/* * ****** Private functions ********************************** */

function description($index) {
    $sql = "SELECT c.id, c.subject, c.number,  c.title, c.maxcredits, c.description, c.prereq , c.rotation FROM  course as c WHERE c.id = :index";
    $data = array(':index' => $index);
    return getOne($sql, $data);
}

function subjectList() {
    $sql = "SELECT distinct  name, subject  FROM subject  order by name";
    return getAll($sql);
}

function courseList($subject) {

    $sql = "select  c.id, c.maxcredits,   c.subject, c.number, c.title, c.requirement, c.postfixPrereq, c.description, c.prereq, c.rotation,
            (SELECT count(course) FROM `course_schedule` WHERE `course` = id ) as sections
         from  course as c where c.subject=:subject and c.rotation >-1 order by c.subject,  c.number ";
    $data = array(':subject' => $subject);
    return getAll($sql, $data);
}



function getAll($sql, $data = NULL) {
    global $db;
    // prepare PDOStatement object
    $stm = $db->prepare($sql);

    //execute SQL statement
    $stm->execute($data);
    $results = $stm->fetchAll();
    return $results;
}

function getOne($sql, $data = NULL) {
    global $db;
    // prepare PDOStatement object
    $stm = $db->prepare($sql);

    //execute SQL statement
    $stm->execute($data);

    // fetch all the rows using fetchAll() method

    $results = $stm->fetch();
    return $results;
}

?>