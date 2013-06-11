<?php

/*!
 * This file is part of {@link https://github.com/MovLib MovLib}.
 *
 * Copyright © 2013-present {@link http://movlib.org/ MovLib}.
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
namespace MovLib\Presenter;

use \MovLib\Exception\MovieException;
use \MovLib\Model\MovieModel;
use \MovLib\Model\ReleasesModel;


/**
 * Description of MoviePresenter
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class MoviePresenter extends AbstractPresenter {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * Associative array containing the complete data of this movie.
   *
   * @var \MovLib\Model\MovieModel
   */
  public $movieModel;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * {@inheritdoc}
   */
  public function __construct() {
    return $this
      ->{__FUNCTION__ . $this->getMethod()}()
      ->setPresentation()
    ;
  }

  /**
   * Render the movie's page.
   *
   * @global \MovLib\Model\I18nModel $i18n
   *   The global i18n model instance.
   * @return $this
   */
  protected function __constructGet() {
    try {
      $this->movieModel = (new MovieModel())->__constructFromId($_SERVER["MOVIE_ID"]);
      if ($this->movieModel->deleted === true) {
        return $this->setPresentation("Error\\GoneMovie");
      }
      $this->releasesModel = (new ReleasesModel())->__constructFromMovieId($this->movieModel->id);
      return $this->setPresentation("Movie\\MovieShow");
    } catch (MovieException $e) {
      return $this->setPresentation("Error\\NotFound");
    }
  }


  // ------------------------------------------------------------------------------------------------------------------- Public Methods


  /**
   * {@inheritdoc}
   */
  public function getBreadcrumb() {
    global $i18n;
    return [[ "href" => $i18n->r("/movies"), "text" => $i18n->t("Movies") ]];
  }

  /**
   * Get the full path to the poster art.
   *
   * @param string $style
   *   The desired image style.
   *   @todo Examples
   * @return string
   *   Absolute path to the poster art for the desired image style.
   */
  public function getMoviePoster($style) {
    if ($this->movieModel["poster"]) {
      return "/uploads/poster/{$this->movieModel["id"]}/{$style}/{$this->movieModel["poster"]["file_name"]}.{$this->movieModel["poster"]["file_id"]}.{$this->movieModel["poster"]["extension"]}";
    }
  }

}
