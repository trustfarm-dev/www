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
namespace MovLib\Data\Series;

/**
 * Represents a single series.
 *
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Series extends \MovLib\Data\AbstractEntity {

  /**
   * Unknown status.
   *
   * @var integer
   */
  const STATUS_UNKNOWN = 0;

  /**
   * New status.
   *
   * @var integer
   */
  const STATUS_NEW = 1;

  /**
   * Returning status.
   *
   * @var integer
   */
  const STATUS_RETURNING = 2;

  /**
   * Ended status.
   *
   * @var integer
   */
  const STATUS_ENDED = 3;

  /**
   * Cancelled status.
   *
   * @var integer
   */
  const STATUS_CANCELLED = 4;

}