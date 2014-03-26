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

use \MovLib\Data\Genre;
use \MovLib\Presentation\Partial\Alert;
use \MovLib\Presentation\Partial\Listing\MovieListing as MoviesPartial;

/**
 * Movies with a certain genre associated.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Movies extends \MovLib\Presentation\Genre\AbstractBase {


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new genre movie presentation.
   *
   */
  public function __construct() {
    $this->genre = new Genre((integer) $_SERVER["GENRE_ID"]);
    $this->initPage($this->intl->t("Movies with {0}", [ $this->genre->name ]));
    $this->pageTitle = $this->intl->t("Movies with {0}", [ "<a href='{$this->genre->route}'>{$this->genre->name}</a>" ]);
    $this->breadcrumbTitle = $this->intl->t("Movies");
    $this->initLanguageLinks("/genre/{0}/movies", [ $this->genre->id ], true);
    $this->initGenreBreadcrumb();
    $this->sidebarInit();

    $kernel->stylesheets[] = "genre";
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * @inheritdoc
   * @return \MovLib\Presentation\Partial\Listing\MovieListing
   */
  protected function getPageContent() {
    return new MoviesPartial(
      $this->genre->getMoviesResult(),
      new Alert($this->intl->t("Check back later"), $this->intl->t("No movies found."), Alert::SEVERITY_INFO)
    );
  }

}
