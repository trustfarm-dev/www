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

use \MovLib\Data\DateTime;

/**
 * Defines the base class for database entity objects.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
abstract class AbstractEntity extends \MovLib\Data\AbstractConfig {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The entity's unique identifier.
   *
   * @var integer
   */
  public $id;

  /**
   * The entity's changed date and time.
   *
   * @var \DateTime
   */
  public $changed;

  /**
   * The entity's creation date and time.
   *
   * @var \DateTime
   */
  public $created;

  /**
   * The entity's deletion status.
   *
   * @var boolean
   */
  public $deleted = false;


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * Get the count of a relationship.
   *
   * <b>EXAMPLE</b><br>
   * <code><?php
   *
   * public function getCount($from, $where = null, $what = "*") {
   *   return $this->getMySQLi()->query("SELECT COUNT({$what}) FROM `{$from}` WHERE {$where}")->fetch_row()[0];
   * }
   *
   * ?></code>
   *
   * @param string $from
   *   The table defining the relationship that is to be counted.
   * @param string $where [Optional]
   *   The WHERE clause of the SQL query (without WHERE).
   * @param string $what
   *   The content of the <code>COUNT()</code> function in the SQL query.
   * @return integer
   *   The count of the relationship.
   */
  public function getCount($from, $where = null, $what = "*") {
    if ($where) {
      $where = " WHERE {$where}";
    }
    $result = $this->getMySQLi()->query("SELECT COUNT({$what}) FROM `{$from}`{$where} LIMIT 1");
    $count  = $result->fetch_row()[0];
    $result->free();
    return $count;
  }

  /**
   * Initialize the entity.
   *
   * Further initialization work after an entity was initialized via PHP's built-in {@see \mysqli_result::fetch_object}
   * method or continued initialization after the own constructor was called.
   *
   * @return this
   */
  protected function init() {
    $this->changed = new DateTime($this->changed);
    $this->created = new DateTime($this->created);
    $this->deleted = (boolean) $this->deleted;
    return parent::init();
  }

  /**
   * Whether this entity is gone or not.
   *
   * @return boolean
   *   <code>TRUE</code> if the entity is gone, <code>FALSE</code> otherwise.
   */
  public function isGone() {
    return $this->deleted;
  }

}
