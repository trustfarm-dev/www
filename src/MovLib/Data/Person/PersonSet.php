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
namespace MovLib\Data\Person;

/**
 * Defines the person set object.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
final class PersonSet extends \MovLib\Data\AbstractEntitySet {


  // ------------------------------------------------------------------------------------------------------------------- Constants


  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "PersonSet";
  // @codingStandardsIgnoreEnd


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * {@inheritdoc}
   */
  public static $tableName = "persons";


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * {@inheritdoc}
   */
  public function __construct(\MovLib\Core\Container $container) {
    parent::__construct($container, "Persons", "Person", $container->intl->tp(-1, "Persons", "Person"));
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * {@inheritdoc}
   */
  protected function getEntitiesQuery($where = null, $orderBy = null) {
    return <<<SQL
SELECT
  `persons`.`id`,
  `persons`.`name`,
  `persons`.`sex`,
  `persons`.`birthdate` AS `birthDate`,
  `persons`.`born_name` AS `bornName`,
  `persons`.`deathdate` AS `deathDate`,
  `persons`.`count_awards` AS `countAwards`,
  `persons`.`count_movies` AS `countMovies`,
  `persons`.`count_releases` AS `countReleases`,
  `persons`.`count_series` AS `countSeries`,
  `persons`.`deleted`,
  `persons`.`changed`,
  `persons`.`created`,
  HEX(`persons`.`image_cache_buster`) AS `imageCacheBuster`,
  `persons`.`image_extension` AS `imageExtension`,
  `persons`.`image_styles` AS `imageStyles`
FROM `persons`
{$where}
{$orderBy}
SQL;
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntitySetsQuery(\MovLib\Data\AbstractEntitySet $set, $in) {
    return <<<SQL

SQL;
  }

  /**
   * {@inheritdoc}
   */
  public function init() {
    $this->pluralKey   = "persons";
    $this->singularKey = "person";
    return parent::init();
  }

}
