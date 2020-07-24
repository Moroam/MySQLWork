<?php
/**
 * Class MySQLWork
 *
 * @version 1.0.1
 */
class MySQLWork
{
  protected $mysqli = null;

  public function __construct(string $host = '', string $user = '', string $password = '', string $database = '', string $charset = "utf8"){
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    if($host != '' && $user != '' && $password != '' && $database != ''){
      $this->conn($host, $user, $password, $database, $charset);
    }
  }

  public function __get(string $name){
    if($name === 'mysqli'){
      return $this->mysqli;
    }

    return false;
  }

  public function __set(string $name, mysqli $mysqli){
    if($name === 'mysqli'){
      $this->close();
      $this->mysqli = $mysqli;
    }

  }

  /**
   * create and set mysqli connection
   *
   * @param string $host Hostname or an IP address, like localhost or 127.0.0.1
   * @param string $user Database username
   * @param string $password Database password
   * @param string $database Database name
   * @param string $charset  connection charset
   * @return $mysqli
   * @throws mysqli_sql_exception If any mysqli function failed due to mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)
   */
  public function conn(string $host, string $user, string $password, string $database, string $charset = "utf8") : bool {
    $this->close();

    $mysqli = new mysqli($host, $user, $password, $database)
      or die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);

    if(!$mysqli){
      error_log("MySQLWork: Mysqli connection error");
      return false;
    }

    if (!$mysqli->set_charset($charset)) {
        error_log("MySQLWork: Error load charset utf8: " . $mysqli->error);
        $mysqli->close();
        return false;
    }

    $this->mysqli = $mysqli;
    return true;
  }

  /**
   * close mysqli connection
   *
   * @return bool
   */
  public function close() : bool {
    if( $this->mysqliTest() ){ // check mysqli
      return $this->mysqli->close();
    }

    return true;
  }

  /**
   * All queries go here. Stops the output of multiquery
   *
   * @param string $sql SQL query
   * @return mysqli_result $result
   * @throws mysqli_sql_exception If any mysqli function failed due to mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)
   */
  public function query(string $sql) {
    if(!$result = $this->mysqli->query($sql, MYSQLI_STORE_RESULT)){
      error_log('MySQLWork: Query Error (' . $this->mysqli->errno . ') ' . $this->mysqli->error);
      return false;
    }

    #clear multi-result
    while($this->mysqli->more_results() && $this->mysqli->next_result()){
      $this->mysqli->store_result();
    }

    return $result;
  }


  /**
   * Fetch one value from mysqli_result
   *
   * @param mysqli_result $query
   * @return string
   * @throws mysqli_sql_exception If any mysqli function failed due to mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)
   */
  public function oneValue(mysqli_result $query ) : string {
    $query->data_seek(0);
    $row = $query->fetch_row();
    $query->free();
    return $row[0];
  }

  /**
   * Fetch one value from sql query
   *
   * @param string $sql SQL query
   * @return result
   * @throws mysqli_sql_exception If any mysqli function failed due to mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)
   */
  public function oneValueSQL(string $sql) : string {
    return $this->oneValue($this->query($sql));
  }

  /**
   * Return fields array from mysqli_result
   *
   * @param mysqli_result $query
   * @return array
   * @throws mysqli_sql_exception If any mysqli function failed due to mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)
   */
  public function fieldsArray(mysqli_result $query ) : array {
    $finfo = $query->fetch_fields();
    foreach ($finfo as $val ) {
      $names[] = $val->name;
    }
    return $names;
  }

  /**
   * Return html table from mysqli_result
   *
   * @param mysqli_result $query
   * @return string
   * @throws mysqli_sql_exception If any mysqli function failed due to mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)
   */
  public function htmlTable(mysqli_result $query, string $caption = '', string $columns_width = '', string $width = '100%', bool $free = TRUE ) : string {
    $table = "<table width='$width' border='1' style='line-height:1.25rem;border-collapse:collapse;font-size:0.9rem;margin:0.1rem;font-style:serif;'>".PHP_EOL;

    if ($caption <> ''){
      $table .= "<caption style='font-size:1rem;font-weight:bold;text-align:center;'>$caption</caption>".PHP_EOL;
    }

    $table .= $columns_width;
    $flds = $this->fieldsArray( $query );
    $table .= '<tr>';
    foreach ($flds as $fld){
      $table .= "<th align='center'>$fld</th>";
    }
    $table .= '</tr>'.PHP_EOL;

    $query->data_seek(0);
    while ($row = $query->fetch_row()) {
      $table .= '<tr>' ;
      foreach ($row as $val){
        $table .= "<td>$val</td>";
      }
      $table .= '</tr>'.PHP_EOL;
    }
    $table.='</table>'.PHP_EOL;

    if ($free) {
      $query->free();
    }

    return $table;
  }


  /**
   * Return array from mysqli_result
   *
   * @param string $sql
   * @param bool $assoc return assoc array or simple array
   * @param bool $free free mysqli_result
   * @return array
   * @throws mysqli_sql_exception If any mysqli function failed due to mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)
   */
  function array(mysqli_result $query, bool $assoc = TRUE, bool $free = TRUE) : array {
    $arr = [];
    $query->data_seek(0);

    if($assoc){
      while ($row = $query->fetch_assoc()) $arr[] = $row;
    } else {
      while ($row = $query->fetch_row()) $arr[] = $row;
    }

    if($free){
      $query->free();
    }

    return $arr;
  }


  /**
   * Return array from sql query
   *
   * @param string $sql sql query
   * @param bool $assoc return assoc array or simple array
   * @param bool $free free mysqli_result
   * @return array
   * @throws mysqli_sql_exception If any mysqli function failed due to mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)
   */
  function arraySQL(string $sql, bool $assoc = TRUE, bool $free = TRUE) : array {
    return $this->array($this->query($sql), $assoc, $free);
  }


  /**
   * Return array from sql query
   *  Example: SELECT id, value FROM spr ORDER BY id; => array[id] = value
   *
   * @param string $sql
   * @return array
   * @throws mysqli_sql_exception If any mysqli function failed due to mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)
   */
  public function array2(string $sql) : array {
    $a = $this->arraySQL($sql, FALSE);

    return array_combine(array_column($a, 0), array_column($a, 1));
  }


  /**
   * Return array from sql multi query
   *
   * @param string $sql
   * @return array mysqli_result
   * @throws mysqli_sql_exception If any mysqli function failed due to mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT)
   */
  public function multiQuery(string $sql) : array {
    $arr = [];
    if ($this->mysqli->multi_query($sql)) {
      do {
        $arr[] = $this->mysqli->store_result();
      } while ($this->mysqli->more_results() && $this->mysqli->next_result());
    }

    return $arr;
  }

  /**
   * Format value for working with sql
   *
   * @param string $value
   * @return string
   */
  public function test(string $value) : string {
    $data = trim($value);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    if( $this->mysqliTest() ) {
      $data = $this->mysqli->real_escape_string($data);
    }

    return $data;
  }

  /**
   * Test and format post value for working with sql
   *
   * @param string $var_name
   * @param $def_value default value if post value not exists
   * @return string
   */
  public function TIP(string $var_name, $def_value = '') {
    $data = $_POST[$var_name] ?? $def_value;
    return $this->test($data);
  }

  public function mysqliTest() : bool {
    return $this->mysqli && get_class($this->mysqli) === 'mysqli';
  }
}
