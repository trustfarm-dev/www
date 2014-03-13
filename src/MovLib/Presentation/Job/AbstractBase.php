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

/**
 * Base presenation of all job pages.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
abstract class AbstractBase extends \MovLib\Presentation\Page {
  use \MovLib\Presentation\TraitGone {
    goneGetContent as private traitGetGoneContent;
  }
  use \MovLib\Presentation\TraitSidebar {
    sidebarInit as traitSidebarInit;
  }


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The job to present.
   *
   * @var \MovLib\Data\Job
   */
  protected $job;


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * Build content for gone page.
   *
   * @global \MovLib\Data\I18n $i18n
   * @return $this
   * @throws \LogicException
   */
  protected function goneGetContent() {
    global $i18n;
    // @devStart
    // @codeCoverageIgnoreStart
    if (!($this->job instanceof \MovLib\Data\Job)) {
      throw new \LogicException($i18n->t("\$this->job has to be a valid job object!"));
    }
    // @codeCoverageIgnoreEnd
    // @devEnd

    $routeArgs = [ $this->job->id ];

    $this->goneAlertMessage = $i18n->t(
        "The job and all its content have been deleted. Take a look at the {0}history{2} or {1}discussion{2} page " .
        "for further information. Please discuss with the person responsible for this deletion before " .
        "you restore this entry from its {0}history{2}.",
        [
          "<a href='{$i18n->r("/job/{0}/history", $routeArgs)}'>",
          "<a href='{$i18n->r("/job/{0}/discussion", $routeArgs)}'>",
          "</a>"
        ]
      );
    return $this->traitGetGoneContent();
  }

  /**
   * Init job breadcrumb.
   *
   * @global \MovLib\Data\I18n $i18n
   * @return $this
   * @throws \LogicException
   */
  protected function initJobBreadcrumb() {
    global $i18n;
    // @devStart
    // @codeCoverageIgnoreStart
    if (!($this->job instanceof \MovLib\Data\Job)) {
      throw new \LogicException($i18n->t("\$this->job has to be a valid job object!"));
    }
    // @codeCoverageIgnoreEnd
    // @devEnd

    return $this->initBreadcrumb([
      [ $i18n->rp("/jobs"), $i18n->t("Jobs") ],
      [ $this->job->route, $this->job->name ]
    ]);
  }

  /**
   * Init job sidebar.
   *
   * @global \MovLib\Data\I18n $i18n
   * @return $this
   * @throws \LogicException
   */
  protected function sidebarInit() {
    global $i18n;
    // @devStart
    // @codeCoverageIgnoreStart
    if (!($this->job instanceof \MovLib\Data\Job)) {
      throw new \LogicException($i18n->t("\$this->job has to be a valid job object!"));
    }
    // @codeCoverageIgnoreEnd
    // @devEnd

    // Compile array once.
    $routeArgs = [ $this->job->id ];

    // Reduce the sidebar if the job was deleted.
    if ($this->job->deleted === true) {
      return $this->traitSidebarInit([
        [ $this->job->route, $i18n->t("View"), [ "class" => "ico ico-view" ] ],
        [ $i18n->r("/job/{0}/discussion", $routeArgs), $i18n->t("Discuss"), [ "class" => "ico ico-discussion" ] ],
        [ $i18n->r("/job/{0}/history", $routeArgs), $i18n->t("History"), [ "class" => "ico ico-history" ] ]
      ]);
    }

    return $this->traitSidebarInit([
      [ $this->job->route, $i18n->t("View"), [ "class" => "ico ico-view" ] ],
      [ $i18n->r("/job/{0}/discussion", $routeArgs), $i18n->t("Discuss"), [ "class" => "ico ico-discussion" ] ],
      [ $i18n->r("/job/{0}/edit", $routeArgs), $i18n->t("Edit"), [ "class" => "ico ico-edit" ] ],
      [ $i18n->r("/job/{0}/history", $routeArgs), $i18n->t("History"), [ "class" => "ico ico-history" ] ],
      [ $i18n->r("/job/{0}/delete", $routeArgs), $i18n->t("Delete"), [ "class" => "ico ico-delete separator" ] ],

      [ $i18n->rp("/job/{0}/movies", $routeArgs), "{$i18n->t("Movies")} <span class='fr'>{$i18n->format("{0,number}", [ $this->job->getMovieCount() ])}</span>", [ "class" => "ico ico-movie" ] ],
      [ $i18n->rp("/job/{0}/series", $routeArgs), "{$i18n->t("Series")} <span class='fr'>{$i18n->format("{0,number}", [ $this->job->getSeriesCount() ])}</span>", [ "class" => "ico ico-series separator" ] ]
    ]);
  }

}