<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mariadb';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'hurixlms';
$CFG->dbuser    = 'root';
$CFG->dbpass    = '';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_unicode_ci',
);

$CFG->wwwroot   = 'http://localhost/hurixlms';
$CFG->dataroot  = 'D:\\wamp64\\hurixlmsData';
$CFG->admin     = 'admin';


//@ini_set('display_errors', '1'); // NOT FOR PRODUCTION SERVERS!
//$CFG->debug = 32767;         // NOT FOR PRODUCTION     SERVERS! // for Moodle 2.0 - 2.2, use:  $CFG->debug = 38911;  
//$CFG->debugdisplay = true;   // NOT FOR PRODUCTION SERVERS!


$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
