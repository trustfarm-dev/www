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
namespace MovLib\Presentation\History\Movie;

/**
 * The movie title revisions page.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class MovieTitlesRevisions extends \MovLib\Presentation\History\AbstractHistory {
  use \MovLib\Presentation\Movie\TraitMoviePage;

  /**
   * Instatiate new movie title history presentation.
   *
   * @global \MovLib\Data\I18n $i18n
   */
  public function __construct($context = "history") {
    global $i18n;
    $this->initMovie();
    $this->init($i18n->t("History of {0}", [ $this->title ]));

    $this->historyModel = new \MovLib\Data\History\MovieTitles($this->model->id, $context);
  }
  
  /**
   * @inheritdoc
   */
  protected function getPageContent() {
    $this->addClass("active", $this->secondaryNavigation->menuitems[4][2]);
    return $this->revisionsPage();
  }
}
