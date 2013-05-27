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
namespace MovLib\View\HTML\Movie;

use \MovLib\Entity\Language;
use \MovLib\View\HTML\AbstractView;

/**
 * Description of MovieView
 *
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class MovieShowView extends AbstractView {
  
  /**
   * The Constructor for the movie show view.
   *
   * @param \MovLib\Presenter\MoviePresenter $presenter
   *  The MoviePresenter to be used.
   */
  public function __construct($presenter) {
    parent::__construct($presenter, SITENAME);
    $this->title = $presenter->getTitle();
    $this->title .= $presenter->getYear() == "0000" ? "" : " ({$presenter->getYear()})";
    $this->addStylesheet("/assets/css/modules/movie.css");
  }

  
  public function getRenderedContent() {
    return
    "";
  }

}