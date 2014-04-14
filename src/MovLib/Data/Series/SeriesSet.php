<?php

/* !
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
 * Defines the series set object.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
final class SeriesSet extends \MovLib\Data\AbstractSet {

  /**
   * {@inheritdoc}
   */
  protected function getEntitiesQuery($where = null, $orderBy = null) {
    return <<<SQL
SELECT
  `series`.`id` AS `id`,
  `series`.`changed` AS `changed`,
  `series`.`created` AS `created`,
  `series`.`deleted` AS `deleted`,
  `series`.`end_year` AS `endYear`,
  `series`.`mean_rating` AS `meanRating`,
  `series`.`original_title` AS `originlTitle`,
  `series`.`original_title_language_code` AS `originalTitleLanguageCode`,
  `series`.`rank` AS `rank`,
  `series`.`rating` AS `rating`,
  `series`.`start_year` AS `startYear`,
  `series`.`status` AS `status`,
  `series_titles`.`title` AS `title`,
  `series`.`count_awards` AS `awardCount`,
  `series`.`count_seasons` AS `seasonCount`,
  `series`.`count_releases` AS `releaseCount`
FROM `series`
  LEFT JOIN `series_titles`
    ON `series_titles`.`series_id` = `series`.`id`
    AND `series_titles`.`language_code` = '{$this->intl->languageCode}'
    AND `series_titles`.`display` = true
{$where}
{$orderBy}
SQL;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntitySetsQuery(\MovLib\Data\AbstractSet $set, $in) {
    return <<<SQL

SQL;
  }

  /**
   * {@inheritdoc}
   */
  protected function init() {
    $this->pluralKey   = "series";
    $this->singularKey = "series";
    return parent::init();
  }

}
