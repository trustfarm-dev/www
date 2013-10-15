<?php

/*!
 * This file is part of {@link https://github.com/MovLib MovLib}.
 *
 * Copyright © 2013-present {@link http://movlib.org/ MovLib}.
 *
 * MovLib is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * MovLib is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with MovLib.
 * If not, see {@link http://www.gnu.org/licenses/ gnu.org/licenses}.
 */
namespace MovLib\Data;

use \MovLib\Exception\ErrorException;
use \MovLib\Exception\DatabaseException;
use \mysqli;
use \ReflectionFunction;

/**
 * Base class for all database related classes.
 *
 * Implements the most basic methods to query the database and handles connecting and disconnecting globally for all
 * models. We ensure that we only have a single database connection per request in this class.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class Database extends \MovLib\Data\AbstractBase {


  // ------------------------------------------------------------------------------------------------------------------- Constants


  /**
   * TTL value for records in the temporary table that are deleted on a daily basis.
   *
   * @var int
   */
  const TMP_TTL_DAILY = "@daily";


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The number of affected rows from previous query.
   *
   * <b>NOTE</b>
   * The number indicates the affected rows of the last executed <code>DELETE</code>, <code>INSERT</code>,
   * <code>UPDATE</code> or <code>REPLACE</code> query. If the last query was a <code>SELECT</code> statement the number
   * indicates the total count of returned results (same as <code>count($this->select("QUERY"));</code>). If the number
   * is zero no records have been deleted, inserted, updated, replaced or nothing matched the where clause of the last
   * select. A negative number indicates that the previous query resulted in an error.
   *
   * @return int|string
   */
  protected $affectedRows = -1;

  /**
   * Name of the database to which this instance is connected.
   *
   * @var string
   */
  protected $database;

  /**
   * The <code>AUTO_INCREMENT</code> ID from previous query.
   *
   * <b>NOTE</b>
   * If the last executed query wasn't an <code>INSERT</code> or <code>UPDATE</code> statement or if the modified table
   * doesn't have a column with the <code>AUTO_INCREMENT</code> attribute, this function will return zero. The ID will
   * be returned as string if the value is greater than PHP's maximum integer value.
   *
   * @return int|string
   */
  protected $insertId = 0;

  /**
   * Associative array containing a single MySQLi instance for each database.
   *
   * @var \mysqli
   */
  protected static $mysqli = [];

  /**
   * The MySQLi statement object for all queries.
   *
   * @var \mysqli_stmt
   */
  protected $stmt;

  /**
   * Used to cache the reference to <code>stmt_bind_param()</code> function, which allow us to invoke the function with
   * an array of parameters. Using a shared reflection function among all instances of this function is faster than
   * calling <code>call_user_func_array()</code>.
   *
   * @var \ReflectionFunction
   */
  private static $stmtBindParam;

  /**
   * Flag indicating if a transaction is active.
   *
   * @var boolean
   */
  protected $transactionActive = false;


  // ------------------------------------------------------------------------------------------------------------------- Protected Final Methods


  /**
   * Generic delete, insert, or update query method.
   *
   * @param string $query
   *   The query to be executed.
   * @param string $types [optional]
   *   The type string in <code>\mysqli_stmt::bind_param()</code> syntax.
   * @param array $params [optional]
   *   The parameters to bind.
   * @return this
   * @throws \MovLib\Exception\DatabaseException
   */
  protected function query($query, $types = null, array $params = null) {
    $this->prepareAndExecute($query, $types, $params);
    $this->affectedRows = $this->stmt->affected_rows;
    $this->insertId     = $this->stmt->insert_id;
    return $this;
  }

  /**
   * Generic select query method.
   *
   * @param string $query
   *   The query to be executed.
   * @param string $types [optional]
   *   The type string in <code>\mysqli_stmt::bind_param</code> syntax.
   * @param array $params [optional]
   *   The parameters to bind.
   * @return array
   *   The query results as numeric array with each result as associative array.
   * @throws \MovLib\Exception\DatabaseException
   */
  protected function select($query, $types = null, array $params = null) {
    return $this->getResult($query, $types, $params)->fetch_all(MYSQLI_ASSOC);
  }

  /**
   * Generic select query method for a single row.
   *
   * <b>NOTE</b>
   * <code>LIMIT 1</code> is automatically appended to the query!
   *
   * @param string $query
   *   The query to be executed.
   * @param string $types [optional]
   *   The type string in <code>\mysqli_stmt::bind_param</code> syntax.
   * @param array $params [optional]
   *   The parameters to bind.
   * @return array
   *   The query result as associative array.
   * @throws \MovLib\Exception\DatabaseException
   */
  protected function selectAssoc($query, $types = null, $params = null) {
    return $this->getResult($query, $types, $params)->fetch_assoc();
  }

  protected function selectObj($query, $types = null, $params = null, $orderBy = null, $className = null, array $constructorParams = null) {
    $mysqliResult = $this->getResult($query, $types, $params);
    $objects = [];
    while ($object = $mysqliResult->fetch_object($className, $constructorParams)) {
      if ($orderBy) {
        $objects[$object->{$orderby}] = $object;
      }
      else {
        $objects[] = $object;
      }
    }
    return $objects;
  }

  /**
   * Commit current transaction.
   *
   * @param int $flags [optional]
   *   A bitmask of <var>MYSQLI_TRANS_COR_*</var> constants, defaults to <var>MYSQLI_TRANS_COR_AND_NO_CHAIN</var>.
   * @return this
   * @throws \MovLib\Exception\DatabaseException
   */
  protected function transactionCommit($flags = MYSQLI_TRANS_COR_AND_NO_CHAIN) {
    if (!isset(self::$mysqli[$this->database]) || $this->transactionActive === false) {
      throw new DatabaseException("No active transaction, nothing to commit.");
    }
    if (self::$mysqli[$this->database]->commit($flags) === false) {
      $e = new DatabaseException("Commit failed", self::$mysqli[$this->database]->error, self::$mysqli[$this->database]->errno);
      $this->transactionRollback();
      throw $e;
    }
    $this->transactionActive = false;
    return $this;
  }

  /**
   * Rollback current transaction.
   *
   * @param int $flags [optional]
   *   A bitmask of <var>MYSQLI_TRANS_COR_*</var> constants, defaults to <var>MYSQLI_TRANS_COR_AND_NO_CHAIN</var>.
   * @return this
   * @throws \MovLib\Exception\DatabaseException
   */
  protected function transactionRollback($flags = MYSQLI_TRANS_COR_AND_NO_CHAIN) {
    if (!isset(self::$mysqli[$this->database]) || $this->transactionActive === false) {
      throw new DatabaseException("No active transaction, nothing to rollback.");
    }
    if (self::$mysqli[$this->database]->rollback($flags) === false) {
      throw new DatabaseException("Rollback failed", self::$mysqli[$this->database]->error, self::$mysqli[$this->database]->errno);
    }
    $this->transactionActive = false;
    return $this;
  }

  /**
   * Start a transaction.
   *
   * Executes a SQL native <code>START TRANSACTION</code> against the database and establishes a connection if not
   * connection is available.
   *
   * @param int $flags [optional]
   *   One of the <var>MYSQLI_TRANS_START_*</var> constants, defaults to <var>MYSQLI_TRANS_START_READ_WRITE</var>.
   * @return this
   * @throws \MovLib\Data\DatabaseException
   */
  protected function transactionStart($flags = MYSQLI_TRANS_START_READ_WRITE) {
    if (!isset(self::$mysqli[$this->database])) {
      $this->connect();
    }
    if (self::$mysqli[$this->database]->begin_transaction($flags) === false) {
      throw new DatabaseException("Could not start transaction", self::$mysqli[$this->database]->error, self::$mysqli[$this->database]->errno);
    }
    $this->transactionActive = true;
    return $this;
  }


  // ------------------------------------------------------------------------------------------------------------------- Private Methods


  /**
   * Connect to database.
   *
   * @return this
   * @throws \MovLib\Exception\DatabaseException
   */
  private function connect() {
    if (!$this->database) {
      $this->database = $GLOBALS["movlib"]["default_database"];
    }
    if (!self::$stmtBindParam) {
      self::$stmtBindParam = new ReflectionFunction("mysqli_stmt_bind_param");
    }
    $mysqli = new mysqli();
    try {
      $mysqli->real_connect();
    }
    // If we have a broken pipe (e.g. database restart) kill this thread and directly re-connect. If this fails again
    // (every unlikely) an ErrorException is thrown again and the error_all_handler() can take care of it.
    catch (ErrorException $e) {
      $mysqli->kill($mysqli->thread_id);
      $mysqli->real_connect();
    }
    if ($mysqli->connect_error) {
      throw new DatabaseException("Connecting to database server failed", $mysqli->error, $mysqli->errno);
    }
    if ($mysqli->select_db($GLOBALS["movlib"]["default_database"]) === false) {
      throw new DatabaseException("Selecting database failed", $mysqli->error, $mysqli->errno);
    }
    self::$mysqli[$this->database] = $mysqli;
    return $this;
  }

  /**
   *
   * @param type $query
   * @param type $types
   * @param type $params
   * @return \mysqli_result
   * @throws DatabaseException
   */
  private function getResult($query, $types = null, $params = null) {
    $this->prepareAndExecute($query, $types, $params);
    $mysqliResult       = $this->stmt->get_result();
    $this->affectedRows = $mysqliResult->num_rows;
    return $mysqliResult;
  }

  /**
   * Prepare a statement for execution.
   *
   * @param string $query
   *   The query to be executed.
   * @param string $types [optional]
   *   The type string in <code>\mysqli_stmt::bind_param</code> syntax.
   * @param array $params [optional]
   *   The parameters to bind.
   * @return this
   * @throws \MovLib\Exception\DatabaseException
   */
  private function prepareAndExecute($query, $types = null, $params = null) {
    $this->affectedRows = -1;
    $this->insertId     = 0;
    if (!isset(self::$mysqli[$this->database])) {
      $this->connect();
    }
    if (($this->stmt = self::$mysqli[$this->database]->prepare($query)) === false) {
      throw new DatabaseException("Preparation of statement failed", self::$mysqli[$this->database]->error, self::$mysqli[$this->database]->errno);
    }
    if ($types && $params) {
      $c = count($params);
      $refParams = [ $this->stmt, $types ];
      for ($i = 0; $i < $c; ++$i) {
        $refParams[$i + 2] =& $params[$i];
      }
      if (self::$stmtBindParam->invokeArgs($refParams) === false) {
        throw new DatabaseException("Binding parameters to prepared statement failed", $this->stmt->error, $this->stmt->errno);
      }
    }
    if ($this->stmt->execute() === false) {
      throw new DatabaseException("Execution of prepared statement failed", $this->stmt->error, $this->stmt->errno);
    }
    return $this;
  }

}
