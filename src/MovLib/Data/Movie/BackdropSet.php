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
namespace MovLib\Data\Movie;

/**
 * Defines the backdrop set object.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
final class BackdropSet extends \MovLib\Data\AbstractSet {

  /**
   * {@inheritdoc}
   */
  protected function getEntitiesQuery($where = null, $orderBy = null) {

  }

  /**
   * {@inheritdoc}
   */
  protected function getEntitySetsQuery(\MovLib\Data\AbstractSet $set, $in) {

  }

  /**
   * {@inheritdoc}
   */
  protected function init() {
    $this->pluralKey   = "backdrops";
    $this->singularKey = "backdrop";
    return parent::init();
  }

}