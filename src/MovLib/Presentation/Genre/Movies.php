<?php

/*!
 *  This file is part of {@link https://github.com/MovLib MovLib}.
 *
 *  Copyright © 2013-present {@link http://movlib.org/ MovLib}.
 *
 *  MovLib is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public
 *  License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 *  version.
 *
 *  MovLib is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 *  of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License along with MovLib.
 *  If not, see {@link http://www.gnu.org/licenses/ gnu.org/licenses}.
 */
namespace MovLib\Presentation\Genre;

use \MovLib\Presentation\Partial\Alert;
use \MovLib\Presentation\Partial\Lists\Movies as MoviesPartial;

/**
 * Movies with a certain genre.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Movies extends \MovLib\Presentation\Genre\AbstractBase {

  /**
   * Instantiate new movies presentation of a genre.
   *
   * @global \MovLib\Data\I18n $i18n
   */
  public function __construct() {
    global $i18n;

    parent::__construct();

    $this->initLanguageLinks("/genre/{0}/movies", [ $this->genre->id ], true);
    $this->breadcrumbTitle = $i18n->t("Movies");

    $title  = $i18n->t("Movies that are {genre_name}");
    $search = "{genre_name}";
    $this->initPage(str_replace($search, $this->genre->name, $title));
    $this->pageTitle = str_replace($search, "<a href='{$this->genre->route}'>{$this->genre->name}</a>", $title);
  }

  /**
   * @global \MovLib\Data\I18n $i18n
   * @return \MovLib\Presentation\Partial\Lists\Movies
   */
  protected function getPageContent() {
    global $i18n;
    return new MoviesPartial($this->genre->getMovieResult(), (new Alert($i18n->t("No movies found."), null, Alert::SEVERITY_INFO))->__toString());
  }
}
