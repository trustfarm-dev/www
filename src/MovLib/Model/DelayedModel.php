<?php

/*!
 *  This file is part of {@link https://github.com/MovLib MovLib}.
 *
 *  Copyright © 2013-present {@link http://movlib.org/ MovLib}.
 *
 *  MovLib is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public
 *  License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 *  version.
 *
 *  MovLib is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 *  of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License along with MovLib.
 *  If not, see {@link http://www.gnu.org/licenses/ gnu.org/licenses}.
 */
namespace MovLib\Model;

use \MovLib\Model\AbstractModel;

/**
 * The delayed model is a special model to execute any query after the response was sent to the user.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @author Franz Torghele <ftorghele.mmt-m:2012@fh-salzburg.ac.at>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class DelayedModel extends AbstractModel {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * Current instance of this class. Keeping this private makes sure that nobody will mess with our instance.
   *
   * @var null|$this
   */
  private static $instance = null;

  /**
   * The stack contains all queries that were collected throughout the execution.
   *
   * @var string
   */
  private $stack = [];


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Singleton!
   */
  private function __construct() {
    parent::__construct();
  }

  /**
   * Singleton!
   */
  private function __clone();


  // ------------------------------------------------------------------------------------------------------------------- Public Static Methods


  /**
   * Get instance of class.
   *
   * @global array $delayedObjects
   *   Global array to collect delayed objects for execusion after response was sent to the user.
   * @return $this
   */
  public static function getInstance() {
    global $delayedObjects;
    if (self::$instance === null) {
      $class = get_called_class();
      self::$instance = new $class();
      $delayedObjects[] = self::$instance;
    }
    return self::$instance;
  }

  /**
   * Add a delayed query to the stack.
   *
   * @param string $query
   *   The query to be executed.
   * @param string $types
   *   The type string in <code>\mysqli_stmt::bind_param</code> syntax.
   * @param array $values
   *   The values that should be inserted.
   * @param function $callback
   *   [Optional] Execute a function after the query was executed. The instance of this class and the values will be
   *   passed to the callback (in this order).
   * @return $this
   */
  public static function delayedQuery($query, $types, $values, $callback = null) {
    $instance = self::getInstance();
    $instance->stack[] = [ $query, $types, $values, $callback ];
    return $instance;
  }


  // ------------------------------------------------------------------------------------------------------------------- Public Methods


  /**
   * Execute all delayed queries.
   *
   * @throws \Exception
   * @throws \MovLib\Exception\DatabaseException
   */
  public function run() {
    foreach ($this->stack as list($query, $types, $values, $callback)) {
      $this->query($query, $types, $values);
      if ($callback) {
        $callback($this, $values);
      }
    }
  }

}
