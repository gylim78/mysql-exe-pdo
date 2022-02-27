<?php 
  include_once "db-connect.php";
  include_once "mysql-exe-functions.php";

  echo "// Generated : ";
  echo create_now_timestamp();
  echo "<br><br>";


  // Get a list of all the tables in the DB
  $mysql = "SHOW TABLES";
  $tmp = "Tables_in_" . $db;
  $index = array($tmp);
  $mySQL_data = query2D($conn,$mysql,$index);

  $tables = $mySQL_data['data'];
  $tmp_array_index = "Tables_in_$db";
  
  // Go through each table
  for ($i=0; $i < sizeof($tables); $i++) { 
    $myTable = $tables[$i][$tmp_array_index];
    $index = array("Field","Type");
    $mysql = "DESCRIBE " . $myTable;
    $mySQL_data = query2D($conn,$mysql,$index);
    $table_struct = $mySQL_data['data'];

    echo "// --- TABLE : $myTable <br>";

    $var_name = '$t_' . $myTable;

    for ($j=0; $j < sizeof($table_struct); $j++) { 
      $field = "['" . $table_struct[$j]['Field'] . "']";
      $field_type = check_field($table_struct[$j]['Type']);
      $php_code_line = $var_name . $field . " = " . $field_type . ";";
      echo $php_code_line;
      echo "<br>";
    }
    echo "<br>";

  }


function check_field($str){

  $numeric = array("int","float","tinyint","decimal");
  $string = array("varchar","text","tinytext","text","json","date");
  $timestamp = array("timestamp");

  for ($k=0; $k < sizeof($numeric); $k++) {     
    if (strpos($str, $numeric[$k]) !== false) {
      return 0;
    }
  }

  for ($k=0; $k < sizeof($string); $k++) {     
    if (strpos($str, $string[$k]) !== false) {
      return 1;
    }
  }

  for ($k=0; $k < sizeof($timestamp); $k++) {     
    if (strpos($str, $timestamp[$k]) !== false) {
      return 2;
    }
  }

  return 9;
}

?>