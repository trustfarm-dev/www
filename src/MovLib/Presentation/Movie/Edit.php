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
namespace MovLib\Presentation\Movie;

use \MovLib\Data\Movie\FullMovie;
use \MovLib\Presentation\Partial\Alert;

/**
 * Movie deletion presentation.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Edit extends \MovLib\Presentation\Movie\AbstractBase {
  use \MovLib\Presentation\TraitFormPage;


  // ------------------------------------------------------------------------------------------------------------------- Properties




  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new delete movie presentation.
   *
   * @global \MovLib\Data\I18n $i18n
   */
  public function __construct() {
    global $i18n;
    $this->movie = new FullMovie($_SERVER["MOVIE_ID"]);
    $this->initPage($i18n->t("Edit {title}", [ "title" => $this->movie->displayTitleWithYear ]));
    $this->initLanguageLinks("/movie/{0}/edit", [ $this->movie->id ]);
    $this->initBreadcrumb();
    $this->breadcrumbTitle = $i18n->t("Edit");
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * @inheritdoc
   */
  protected function getContent() {
    global $i18n;
    $this->alerts .= new Alert(
      $i18n->t("The edit movie feature isn’t implemented yet."),
      $i18n->t("Check back later"),
      Alert::SEVERITY_INFO
    );
  }

  /**
   * @inheritdoc
   */
  protected function valid() {
    return $this;
  }

}
