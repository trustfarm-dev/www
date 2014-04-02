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
namespace MovLib\Data\Job;

/**
 * Defines the job set object.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
final class JobSet extends \MovLib\Data\AbstractDatabaseSet {

  /**
   * {@inheritdoc}
   */
  public function getEntityClassName() {
    return "\\MovLib\\Data\\Job\\Job";
  }

  /**
   * {@inheritdoc}
   */
  public function getOrdered($by, $offset, $limit) {
    // @todo Store counts as columns in table.
    return $this->getMySQLi()->query(<<<SQL
SELECT
  `jobs`.`id` AS `id`,
  COLUMN_GET(`dyn_names_sex0`, '{$this->intl->languageCode}' AS CHAR) AS `name`,
  COUNT(DISTINCT `movies_crew`.`movie_id`) AS `movieCount`,
  COUNT(DISTINCT `episodes_crew`.`series_id`) AS `seriesCount`
FROM `jobs`
  LEFT JOIN `movies_crew`
    ON `movies_crew`.`job_id` = `jobs`.`id`
  LEFT JOIN `episodes_crew`
    ON `episodes_crew`.`job_id` = `jobs`.`id`
WHERE `deleted` = false
GROUP BY `id`, `name`
ORDER BY {$by} LIMIT {$limit} OFFSET {$offset}
SQL
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getTableName() {
    return "jobs";
  }

}