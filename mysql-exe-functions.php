<?php 
// Query if a certain value exist in the DB
function queryExist($pdo,$mysql){  
  $return_data = array();
  $return_data['var']['mysql'] = $mysql;
  $return_data['var']['status'] = "success";

  try {
    $query = $pdo->query($mysql);
    if ($query){
      $result = $query->fetchAll(PDO::FETCH_ASSOC);

      if ($result !== FALSE){
        $return_data['var']['affected_rows'] = sizeof($result);
  
        if (sizeof($result) > 0) {
          $return_data['data']['exist'] = TRUE;
        } 
        else {
          $return_data['data']['exist'] = FALSE;
        }
      }
    }
    else{
      $return_data['var']['status'] = "sql_error";
      $return_data['var']['err_msg'] = $pdo->errorInfo();
    }

  } catch (PDOException $e) { // Failed to connect? Lets see the exception details..
    $return_data['var']['status'] = "exception";
    $return_data['var']['e_code'] = $e->getCode();
    $return_data['var']['e_msg'] = $e->getMessage();
  }
  check_return_data($return_data); // Log errors if there are any

  return $return_data; 
}

// Query the DB and return a 1 value
function query1($pdo,$mysql,$index){
  $return_data = array();
  $return_data['var']['mysql'] = $mysql;
  $return_data['var']['status'] = "success";

    try {
    $query = $pdo->query($mysql);
    if ($query){
      $result = $query->fetchAll(PDO::FETCH_ASSOC);

      if ($result !== FALSE){
        $return_data['var']['affected_rows'] = sizeof($result);
        $return_data['data'][$index] = $result[0][$index];
      }
    }
    else{
      $return_data['var']['status'] = "sql_error";
      $return_data['var']['err_msg'] = $pdo->errorInfo();
    }

  } catch (PDOException $e) { // Failed to connect? Lets see the exception details..
    $return_data['var']['status'] = "exception";
    $return_data['var']['e_code'] = $e->getCode();
    $return_data['var']['e_msg'] = $e->getMessage();
  }

  check_return_data($return_data); // Log errors if there are any

  return $return_data; 
}

// Query the DB and return a 1 dimensional array
function query1D($pdo,$mysql,$index_list){
  $tmp_array = array();
  $return_data = array();
  $return_data['var']['mysql'] = $mysql;
  $return_data['var']['status'] = "success";

  try {
  $query = $pdo->query($mysql);
  if ($query){
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if ($result !== FALSE){
      $return_data['var']['affected_rows'] = sizeof($result);
      $return_data['data'] = $result[0];
    }
  }
  else{
    $return_data['var']['status'] = "sql_error";
    $return_data['var']['err_msg'] = $pdo->errorInfo();
  }

  } catch (PDOException $e) { // Failed to connect? Lets see the exception details..
    $return_data['var']['status'] = "exception";
    $return_data['var']['e_code'] = $e->getCode();
    $return_data['var']['e_msg'] = $e->getMessage();
  }
  
  check_return_data($return_data); // Log errors if there are any

  return $return_data; 
}

// Query the DB and return a 2 dimensional array
function query2D($pdo,$mysql,$index_list){

  $tmp_array = array();
  $return_data = array();
  $return_data['var']['mysql'] = $mysql;
  $return_data['var']['status'] = "success";

    try {
    $query = $pdo->query($mysql);
    if ($query){
      $result = $query->fetchAll(PDO::FETCH_ASSOC);

      if ($result !== FALSE){
        $return_data['var']['affected_rows'] = sizeof($result);
        for ($i=0; $i < sizeof($result); $i++) { 
          for ($j=0; $j < sizeof($index_list); $j++) {
            $index = $index_list[$j];  
            $tmp_array[$i][$index] = $result[$i][$index];
          }
       } 
       $return_data['data'] = $tmp_array;      
      }
    }
    else{
      $return_data['var']['status'] = "sql_error";
      $return_data['var']['err_msg'] = $pdo->errorInfo();
    }

  } catch (PDOException $e) { // Failed to connect? Lets see the exception details..
    $return_data['var']['status'] = "exception";
    $return_data['var']['e_code'] = $e->getCode();
    $return_data['var']['e_msg'] = $e->getMessage();
  }  

  check_return_data($return_data); // Log errors if there are any

  return $return_data; 
}

function insert_row($pdo,$table,$mysql_insert){

  $return_data = array();
  $return_data['var']['status'] = "success";

  $cols = array_keys($mysql_insert);

  $insert_array = array();

  //Create String of columns
  $mysql_cols = "";
  $mysql_values = "";
  for ($j=0; $j < sizeof($cols); $j++) {
    $mysql_cols .= $cols[$j] . ","; 
    $mysql_values .= ":" . $cols[$j] . ",";    
    
    $index = ":" . $cols[$j];
    $val = $mysql_insert[$cols[$j]];

    $insert_array[$index] = $val;
  }
  $mysql_cols = substr($mysql_cols,0,-1); // Get rid of the last comma
  $mysql_values = substr($mysql_values,0,-1); // Get rid of the last comma
  
  $mysql = "INSERT INTO $table ($mysql_cols) VALUES ($mysql_values)";
  $return_data['var']['mysql'] = $mysql;  

  try {
    $query = $pdo->prepare($mysql);
    if ($query){
      $result = $query->execute($insert_array);

      if ($result !== FALSE){
        $return_data['var']['affected_rows'] = $query->rowCount();
        $return_data['var']['last_id'] = $pdo->lastInsertId();
      }
    }
    else{
      $return_data['var']['status'] = "sql_error";
      $return_data['var']['err_msg'] = $pdo->errorInfo();
    }

  } catch (PDOException $e) { // Failed to connect? Lets see the exception details..
    $return_data['var']['status'] = "exception";
    $return_data['var']['e_code'] = $e->getCode();
    $return_data['var']['e_msg'] = $e->getMessage();
  }

  check_return_data($return_data); // Log errors if there are any

  return $return_data;

}

function update_row($pdo,$table,$mysql_updates,$where_clause){
  $return_data = array();
  $return_data['var']['status'] = "success";

  $cols = array_keys($mysql_updates);
  $insert_array = array();

 // Create String of VALUES
  // 0 = Number; 1 = String; 2 = Timestamp
  $mysql_values = "";
  for ($j=0; $j < sizeof($cols); $j++) {
    $col_name = $cols[$j];
    $value = ":" . $cols[$j];    
    $mysql_values .= $col_name . "=" . $value . ","; 

    $index = ":" . $cols[$j];
    $val = $mysql_updates[$cols[$j]];

    $insert_array[$index] = $val;
  }
    
  
  $mysql_values = substr($mysql_values,0,-1); // Get rid of the last comma

  $mysql = "UPDATE $table SET $mysql_values WHERE $where_clause";
  $return_data['var']['mysql'] = $mysql;

  try {
    $query = $pdo->prepare($mysql);
    if ($query){
      $result = $query->execute($insert_array);

      if ($result !== FALSE){
        $return_data['var']['affected_rows'] = $query->rowCount();
        $return_data['var']['last_id'] = $pdo->lastInsertId();
      }
    }
    else{
      $return_data['var']['status'] = "sql_error";
      $return_data['var']['err_msg'] = $pdo->errorInfo();
    }

  } catch (PDOException $e) { // Failed to connect? Lets see the exception details..
    $return_data['var']['status'] = "exception";
    $return_data['var']['e_code'] = $e->getCode();
    $return_data['var']['e_msg'] = $e->getMessage();
  }

  check_return_data($return_data); // Log errors if there are any

  return $return_data;
}

// Query if a certain value exist in the DB
function delete_row($pdo,$mysql){  
  $return_data = array();
  $return_data['var']['mysql'] = $mysql;
  $return_data['var']['status'] = "success";

  try {
    $query = $pdo->prepare($mysql);
    if ($query){
      $result = $query->execute();

      if ($result !== FALSE){
        $return_data['var']['affected_rows'] = $query->rowCount();
      }
    }
    else{
      $return_data['var']['status'] = "sql_error";
      $return_data['var']['err_msg'] = $pdo->errorInfo();
    }

  } catch (PDOException $e) { // Failed to connect? Lets see the exception details..
    $return_data['var']['status'] = "exception";
    $return_data['var']['e_code'] = $e->getCode();
    $return_data['var']['e_msg'] = $e->getMessage();
  }
  
  check_return_data($return_data); // Log errors if there are any

  return $return_data; 
}

function lock_table($pdo,$table_name){
  $mysql = "Lock tables $table_name write";

  $return_data = array();
  $return_data['var']['mysql'] = $mysql;
  $return_data['var']['status'] = "success";


  try {
    $query = $pdo->prepare($mysql);
    if ($query){
      $result = $query->execute();
    }
    else{
      $return_data['var']['status'] = "sql_error";
      $return_data['var']['err_msg'] = $pdo->errorInfo();
    }

  } catch (PDOException $e) { // Failed to connect? Lets see the exception details..
    $return_data['var']['status'] = "exception";
    $return_data['var']['e_code'] = $e->getCode();
    $return_data['var']['e_msg'] = $e->getMessage();
  }

  check_return_data($return_data); // Log errors if there are any

  return $return_data;
}

function unlock_table($pdo){
  $mysql = "unlock tables";

  $return_data = array();
  $return_data['var']['mysql'] = $mysql;
  $return_data['var']['status'] = "success";


  try {
    $query = $pdo->prepare($mysql);
    if ($query){
      $result = $query->execute();
    }
    else{
      $return_data['var']['status'] = "sql_error";
      $return_data['var']['err_msg'] = $pdo->errorInfo();
    }

  } catch (PDOException $e) { // Failed to connect? Lets see the exception details..
    $return_data['var']['status'] = "exception";
    $return_data['var']['e_code'] = $e->getCode();
    $return_data['var']['e_msg'] = $e->getMessage();
  }

  check_return_data($return_data); // Log errors if there are any

  return $return_data;
}

function create_now_timestamp(){ 
  return date("Y-m-d H:i:s");
}

function check_return_data($return_data){
  if ($return_data['var']['status'] != "success") {
    $error_type = $return_data['var']['status'];
    $error_message = "";
    $mysql = $return_data['var']['mysql'];
    if ($error_type == "sql_error") {
      $error_message = print_r($return_data['var']['err_msg'],true);
    }
    if ($error_type == "exception") {
      $error_message = $return_data['var']['e_msg'];
    }
    log_error($error_type,$error_message,$mysql);  
  }
}

function log_error($error_type,$error_message,$mysql){
  $timestamp = create_now_timestamp();
  $log_message = "[$timestamp] $error_type\n  $error_message\n $mysql \n"; 
  $log_file = "./mysql-exe-errors.log"; 
  error_log($log_message, 3, $log_file); 
}

?>