<?php
error_reporting(E_ALL);
require('../../config/config.php');
$link = mysql_connect(DB_HOST, DB_USER, DB_PASS) or die('Could not connect to database server ' . DB_HOST);
mysql_select_db(DB_NAME, $link) or die('Could not select database ' . DB_NAME);

$id = $_GET['id'];

$sql = file_get_contents("$id.sql");
$sql = str_replace('<?php echo $table_prefix ?>', DB_PREFIX, $sql);
executeMultipleQueries($sql, &$total_queries, &$executed_queries, $link);
echo 'Summary' . "<br>\n";
echo 'Total queries in SQL: ' . $total_queries . " <br>\n";
echo 'Total executed queries: ' . $executed_queries  . " <br>\n";

mysql_close($link);


    function executeMultipleQueries($sql, &$total_queries, &$executed_queries, $link) {
      if (!trim($sql)) {
        $total_queries = 0;
        $executed_queries = 0;
        return true;
      } // if
      
      // Make it work on PHP 5.0.4
      $sql = str_replace(array("\r\n", "\r"), array("\n", "\n"), $sql);
      
      $queries = explode(";\n", $sql);
      if (!is_array($queries) || !count($queries)) {
        $total_queries = 0;
        $executed_queries = 0;
        return true;
      } // if
      
      $total_queries = count($queries);
      foreach ($queries as $query) {
        if (trim($query)) {
          echo $query;
          if (mysql_query(trim($query), $link)) {
            $executed_queries++;
            echo " OK<br>\n";
          } else {
            echo " FAIL<br>\n";
            echo mysql_error(). "<br>\n";
            return false;
          } // if
        } // if
      } // if
      
      return true;
    } // executeMultipleQueries
?>