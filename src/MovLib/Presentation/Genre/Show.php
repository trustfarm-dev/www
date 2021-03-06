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
namespace MovLib\Presentation\Genre;

use \MovLib\Data\Genre\Genre;

/**
 * Presentation of a single genre.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Show extends \MovLib\Presentation\AbstractShowPresenter {

  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "Show";
  // @codingStandardsIgnoreEnd
  use \MovLib\Presentation\Genre\GenreTrait;

  /**
   * {@inheritdoc}
   */
  public function init() {
    $this->entity = new Genre($this->container, $_SERVER["GENRE_ID"]);
    $this
      ->initPage($this->entity->name)
      ->initShow($this->entity, $this->intl->t("Genres"), "Genre", null, $this->getSidebarItems())
    ;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContent() {
    if(empty($this->entity->description)) {
      return $this->callout(
        $this->intl->t("Would you like to {0}add additional information{1}?", [ "<a href='{$this->intl->r("/genre/{0}/edit", $this->entity->id)}'>", "</a>" ]),
        $this->intl->t("{sitename} doesn’t have further details about this genre.", [ "sitename" => $this->config->sitename ])
      );
    }
    return $this->htmlDecode($this->entity->description);
  }

}
