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
namespace MovLib\Data\Award;

/**
 * Defines the award set object.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
final class AwardSet extends \MovLib\Core\AbstractDatabase implements \MovLib\Data\SetInterface {

  /**
   * {@inheritdoc}
   */
  public function getEntityClassName() {
    return "\\MovLib\\Data\\Award\\Award";
  }

  /**
   * {@inheritdoc}
   */
  public function getCount() {
    return $this->getMySQLi()->query("SELECT COUNT(*) FROM `awards` WHERE `deleted` = false")->fetch_row()[0];
  }

  /**
   * {@inheritdoc}
   */
  public function getOrdered($by, $offset, $rowCount) {
    // @devStart
    // @codeCoverageIgnoreStart
    assert(is_string($by));
    assert(!empty($by));
    assert(is_integer($offset));
    assert(is_integer($rowCount));
    // @codeCoverageIgnoreEnd
    // @devEnd
    return $this->getMySQLi()->query(<<<SQL
SELECT
  `id`,
  IFNULL(COLUMN_GET(`dyn_names`, '{$this->intl->languageCode}' AS CHAR), COLUMN_GET(`dyn_names`, '{$this->intl->defaultLanguageCode}' AS CHAR)) AS `name`,
  `first_awarding_year` AS `firstAwardingYear`,
  `last_awarding_year` AS `lastAwardingYear`
FROM `awards`
ORDER BY {$by} LIMIT {$rowCount} OFFSET {$offset}
SQL
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getRandom() {
    if (($result = $this->getMySQLi()->query("SELECT `id` FROM `awards` WHERE `deleted` = false ORDER BY RAND() LIMIT 1"))) {
      return $result->fetch_row()[0];
    }
  }

}
