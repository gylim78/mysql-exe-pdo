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
  $mySQL_data = query2D($pdo,$mysql,$index);

  $tables = $mySQL_data['data'];
  $tmp_array_index = "Tables_in_$db";

  
  


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Required meta tags -->
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html;">
      <title></title>

    <style>
      .db_table_function{

      }

      .hidden{
        display: none;
      }
    </style>
    <script src="jquery-3.3.1.min.js"></script>
    <script>
      $(document).ready(function(){
        $("#table_dropdown").change(function (e) { 
          e.preventDefault();
          selected_table = $(this).val();
          console.log(selected_table);

          if (selected_table == "show_all") {
            $(".db_table_function").removeClass("hidden");
          } else {            
            $(".db_table_function").addClass("hidden");
            $("#code_"+selected_table).removeClass("hidden");          
          }
        });

      });      
    </script>
</head>

<body>

  <select id="table_dropdown" class="form-control">
    <option value="show_all"> SHOW ALL </option>          
    <?php for ($i=0; $i < sizeof($tables); $i++) { ?>
        <option value="<?php echo $tables[$i][$tmp_array_index]; ?>"> <?php echo $tables[$i][$tmp_array_index]; ?> </option>          
    <?php } ?>
  </select>

<?php 
// Go through each table
  for ($i=0; $i < sizeof($tables); $i++) { 

    $myTable = $tables[$i][$tmp_array_index];
    $index = array("Field","Type");
    $mysql = "DESCRIBE " . $myTable;
    $mySQL_data = query2D($pdo,$mysql,$index);
    $table_struct = $mySQL_data['data'];

    if ($i % 2 == 0) {
      echo "<div id='code_$myTable' class='db_table_function' style='background-color: #FAD7A0;'>";
    } else {
      echo "<div id='code_$myTable' class='db_table_function' style='background-color: #F9E79F;'>";
    }

    // Create index list code
    // $index_list = array("network_print","network_files");
    $tmp = "";
    for ($j=0; $j < sizeof($table_struct); $j++) { 
      $tmp .= '"' . $table_struct[$j]['Field'] . '"';
      $tmp .= ",";
    }
    $tmp = substr($tmp,0,-1); // Get rid of the last comma
    $index_list_str = "\$index_list = array($tmp);";
    

    // Create 


    echo "<h1> TABLE : $myTable </h1>";

    // queryExist
    echo "<h2> queryExist ($myTable) </h2>";

    echo "<h3>Query if a certain value exist in the DB </br>";
    echo "\$return_data['data']['exist'] will either return true or false</h3>";

    $tmp = "SELECT [var1] FROM $myTable WHERE [var2]"; 
    echo "$tmp <br>";    
    $tmp = "\$mySQL_return = queryExist(\$pdo,\$mysql);"; 
    echo "$tmp <br>";
    $tmp = "return \$mySQL_return;"; 
    echo "$tmp <br>";

    // query1
    echo "<h2> query1 ($myTable) </h2>";

    echo "<h3>Query the DB and return a 1 value </br>";
    echo "This function is used mainly when you are looking for just 1 particular value</h3>";

    $tmp = "\$mysql = \"SELECT [var1] FROM $myTable WHERE [var2]\";"; 
    echo "$tmp <br>";    
    $tmp = "\$mySQL_return = query1(\$pdo,\$mysql,\"[var1]\");"; 
    echo "$tmp <br>";
    $tmp = "return \$mySQL_return;"; 
    echo "$tmp <br>";


    // query1D
    echo "<h2> query1D ($myTable) </h2>";

    echo "<h3>Query the DB and return a 1 dimensional array </br>";
    echo "This function is used usually when you have only 1 row to return from the database but with multiple columns<br>The function will only return the first row</h3>";

    echo $index_list_str;
    echo "<br>";
    $tmp = "\$mysql = \"SELECT * FROM $myTable WHERE [var1]\";"; 
    echo "$tmp <br>";    
    $tmp = "\$mySQL_return = query1D(\$pdo,\$mysql,\$index_list);"; 
    echo "$tmp <br>";
    $tmp = "return \$mySQL_return;"; 
    echo "$tmp <br>";


    // query2D
    echo "<h2> query2D ($myTable) </h2>";

    echo "<h3>Query the DB and return a 2 dimensional array </br>";
    echo "This function is used usually when you have more than 1 row to return from the database but with multiple columns</h3>";

    echo $index_list_str;
    echo "<br>";
    $tmp = "\$mysql = \"SELECT * FROM $myTable WHERE [var1]\";"; 
    echo "$tmp <br>";    
    $tmp = "\$mySQL_return = query2D(\$pdo,\$mysql,\$index_list);"; 
    echo "$tmp <br>";
    $tmp = "return \$mySQL_return;"; 
    echo "$tmp <br>";


    // insert_row
    echo "<h2> insert_row ($myTable) </h2>";

    echo "<h3>Insert values into the database </h3>";

    echo "\$dbTable = \"$myTable\";";
    echo "<br>";
    echo "\$mysql_insert = array();";
    echo "<br><br>";
    for ($j=0; $j < sizeof($table_struct); $j++) { 
      echo "\$mysql_insert['" . $table_struct[$j]['Field'] . "'] = $" . $table_struct[$j]['Field'] . ";";
      echo "<br>";
    }
    echo "<br>";
    $tmp = "\$mySQL_return = insert_row(\$pdo,\$dbTable,\$mysql_insert);"; 
    echo "$tmp <br>";
    $tmp = "return \$mySQL_return;"; 
    echo "$tmp <br>";


    // update_row
    echo "<h2> update_row ($myTable) </h2>";

    echo "<h3>Update values into the database </h3>";

    echo "\$dbTable = \"$myTable\";";
    echo "<br>";
    echo "\$where_clause = \"\";";
    echo "<br>";
    echo "\$mysql_vars = array();";
    echo "<br><br>";
    for ($j=0; $j < sizeof($table_struct); $j++) { 
      echo "\$mysql_vars['" . $table_struct[$j]['Field'] . "'] = ;";
      echo "<br>";
    }
    echo "<br>";
    $tmp = "\$mySQL_return = update_row(\$pdo,\$dbTable,\$mysql_vars,\$where_clause);"; 
    echo "$tmp <br>";
    $tmp = "return \$mySQL_return;"; 
    echo "$tmp <br>";

    
    // delete
    echo "<h2> delete_row ($myTable) </h2>";
    
    echo "<h3>Delete from the DB </h3>";
    
    $tmp = "\$mysql = \"DELETE FROM $myTable WHERE [var1]\";"; 
    echo "$tmp <br>";    
    $tmp = "\$mySQL_return = delete_row(\$pdo,\$mysql);"; 
    echo "$tmp <br>";
    $tmp = "return \$mySQL_return;"; 
    echo "$tmp <br>";
    
    
    echo "</div>";
  }

?>  

</body>
</html>