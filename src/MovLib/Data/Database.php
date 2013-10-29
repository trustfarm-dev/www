<?php

/*!
 * This file is part of {@link https://github.com/MovLib MovLib}.
 *
 * Copyright © 2013-present {@link https://movlib.org/ MovLib}.
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

use \MovLib\Exception\DatabaseException;

/**
 * Base class for all database related classes.
 *
 * Implements the most basic methods to query the database and handles connecting and disconnecting globally for all
 * models. We ensure that we only have a single database connection per request in this class.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Database {
  use \MovLib\Data\TraitUtilities;


  // ------------------------------------------------------------------------------------------------------------------- Constants


  /**
   * TTL value for records in the temporary table that are deleted on a daily basis.
   *
   * @var int
   */
  const TMP_TTL_DAILY = "@daily";


  // ------------------------------------------------------------------------------------------------------------------- Protected Properties


  /**
   * Name of the database to which this instance is connected.
   *
   * @var string
   */
  protected $database = "movlib";


  // ------------------------------------------------------------------------------------------------------------------- Private Properties


  /**
   * Associative array containing a single MySQLi instance for each database.
   *
   * @var \mysqli
   */
  private static $mysqli = [];

  /**
   * Used to cache the reference to <code>stmt_bind_param()</code> function, which allow us to invoke the function with
   * an array of parameters. Using a shared reflection function among all instances of this function is faster than
   * calling <code>call_user_func_array()</code>.
   *
   * @var \ReflectionFunction
   */
  private static $stmtBindParam;


  // ------------------------------------------------------------------------------------------------------------------- Protected Final Methods


  /**
   * Generic query method.
   *
   * @param string $query
   *   The query to be executed.
   * @param string $types [optional]
   *   The type string in <code>\mysqli_stmt::bind_param</code> syntax.
   * @param array $params [optional]
   *   The parameters to bind.
   * @return \mysqli_stmt
   * @throws \MovLib\Exception\DatabaseException
   */
  protected function query($query, $types = null, $params = null) {
    /* @var $stmt \mysqli_stmt */
    if (($stmt = $this->connect()->prepare($query)) === false) {
      throw new DatabaseException("Preparation of statement failed",
        self::$mysqli[$this->database]->error,
        self::$mysqli[$this->database]->errno
      );
    }
    if ($types && $params) {
      $refParams = [ $stmt, $types ];
      $c         = count($params);
      for ($i = 0, $j = 2; $i < $c; ++$i, ++$j) {
        $refParams[$j] =& $params[$i];
      }
      if (self::$stmtBindParam->invokeArgs($refParams) === false) {
        throw new DatabaseException("Binding parameters to prepared statement failed", $stmt->error, $stmt->errno);
      }
    }
    if ($stmt->execute() === false) {
      throw new DatabaseException("Execution of prepared statement failed", $stmt->error, $stmt->errno);
    }
    return $stmt;
  }


  // ------------------------------------------------------------------------------------------------------------------- Private Methods


  /**
   * Connect to database.
   *
   * @return \mysqli
   * @throws \MovLib\Exception\DatabaseException
   */
  private function connect() {
    if (!isset(self::$mysqli[$this->database])) {
      // A cached reflection function is faster than call_user_func_array()!
      if (!self::$stmtBindParam) {
        self::$stmtBindParam = new \ReflectionFunction("mysqli_stmt_bind_param");
      }
      $mysqli = new \mysqli();
      try {
        $mysqli->real_connect();
      }
      // If we have a broken pipe (e.g. database restart) kill this thread and directly re-connect. If this fails again
      // (very unlikely) an ErrorException is thrown again and the error andler can take care of it.
      catch (\ErrorException $e) {
        $mysqli->kill($mysqli->thread_id);
        $mysqli->real_connect();
      }
      if ($mysqli->connect_error) {
        throw new DatabaseException("Connecting to database server failed", $mysqli->error, $mysqli->errno);
      }
      if ($mysqli->select_db($this->database) === false) {
        throw new DatabaseException("Selecting database '{$this->database}' failed", $mysqli->error, $mysqli->errno);
      }
      self::$mysqli[$this->database] = $mysqli;
    }
    return self::$mysqli[$this->database];
  }

}
