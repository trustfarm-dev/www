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
namespace MovLib\Presentation\Job;

use \MovLib\Data\Job;
use \MovLib\Presentation\Partial\Alert;
use \MovLib\Presentation\Partial\Listing\MovieListing as MoviesPartial;

/**
 * Movies with a certain job associated.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Movies extends \MovLib\Presentation\Job\AbstractBase {


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new job movie presentation.
   *
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Kernel $kernel
   */
  public function __construct() {
    global $i18n, $kernel;
    $this->job = new Job((integer) $_SERVER["JOB_ID"]);
    $this->initPage($i18n->t("Movies with {0}", [ $this->job->name ]));
    $this->pageTitle = $i18n->t("Movies with {0}", [ "<a href='{$this->job->route}'>{$this->job->name}</a>" ]);
    $this->breadcrumbTitle = $i18n->t("Movies");
    $this->initLanguageLinks("/job/{0}/movies", [ $this->job->id ], true);
    $this->initJobBreadcrumb();
    $this->sidebarInit();

    $kernel->stylesheets[] = "job";
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * @inheritdoc
   * @global \MovLib\Data\I18n $i18n
   * @return \MovLib\Presentation\Partial\Listing\MovieListing
   */
  protected function getPageContent() {
    global $i18n;
    return new MoviesPartial(
      $this->job->getMoviesResult(),
      new Alert($i18n->t("Check back later"), $i18n->t("No movies found."), Alert::SEVERITY_INFO)
    );
  }

}
