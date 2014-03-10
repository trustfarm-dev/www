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
namespace MovLib\Presentation\Jobs;

use \MovLib\Data\Job;
use \MovLib\Presentation\Partial\Alert;
use \MovLib\Presentation\Partial\Lists\Jobs as JobsPartial;

/**
 * List of all jobs.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Show extends \MovLib\Presentation\Page {
  use \MovLib\Presentation\TraitPagination;
  use \MovLib\Presentation\TraitSidebar;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new jobs show presentation.
   *
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Kernel $kernel
   */
  public function __construct() {
    global $i18n, $kernel;
    $this->initPage($i18n->t("Jobs"));
    $this->initBreadcrumb();
    $this->initLanguageLinks("/jobs", null, true);
    $this->paginationInit(Job::getTotalCount());
    $this->sidebarInit([
      [ $kernel->requestPath, $this->title, [ "class" => "ico ico-job" ] ],
      [ $i18n->rp("/awards"), $i18n->t("Awards"), [ "class" => "ico ico-award" ] ],
      [ $i18n->rp("/genres"), $i18n->t("Genres"), [ "class" => "ico ico-genre" ] ],
    ]);
    $this->headingBefore = "<a class='btn btn-large btn-success fr' href='{$i18n->r("/job/create")}'>{$i18n->t("Create New Job")}</a>";
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * @inheritdoc
   */
  protected function getPageContent() {
    global $i18n;
    $list = new JobsPartial(
      Job::getJobs($this->paginationOffset, $this->paginationLimit),
      new Alert(
        $i18n->t(
          "We couldn’t find any job matching your filter criteria, or there simply aren’t any jobs available. Would you like to {0}create a new entry{1}?",
          [ "<a href='{$i18n->r("/job/create")}'>", "</a>" ]
        ),
        $i18n->t("No Jobs"),
        Alert::SEVERITY_INFO
      ),
      null,
      null,
      10,
      true
    );
    return "<div id='filter' class='tar'>{$i18n->t("Filter")}</div>{$list}";
  }
}