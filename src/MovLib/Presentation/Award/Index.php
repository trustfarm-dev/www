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
namespace MovLib\Presentation\Award;

use \MovLib\Data\Award;
use \MovLib\Presentation\Partial\Alert;
use \MovLib\Presentation\Partial\Listing\Award as AwardPartial;

/**
 * The latest Awards.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Index extends \MovLib\Presentation\Page {
  use \MovLib\Presentation\TraitSidebar;
  use \MovLib\Presentation\TraitPagination;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new latest awards presentation.
   *
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Kernel $kernel
   */
  public function __construct() {
    global $i18n, $kernel;
    $this->initPage($i18n->t("Awards"));
    $this->initBreadcrumb();
    $this->initLanguageLinks("/awards", null, true);
    $this->paginationInit(Award::getTotalCount());
    $this->sidebarInit([
      [ $kernel->requestPath, $this->title, [ "class" => "ico ico-award" ] ],
      [ $i18n->r("/award/random"), $i18n->t("Random") ],
    ]);
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * @inheritdoc
   */
  protected function getPageContent() {
    global $i18n;

    $this->headingBefore =
      "<a class='btn btn-large btn-success fr' href='{$i18n->r("/award/create")}'>{$i18n->t("Create New Award")}</a>"
    ;

    $result      = Award::getAwards($this->paginationOffset, $this->paginationLimit);
    $noItemText  = new Alert(
      $i18n->t(
        "We couldn’t find any awards matching your filter criteria, or there simply aren’t any awards available."
      ), $i18n->t("No Awards"), Alert::SEVERITY_INFO
    );
    $noItemText .=
      $i18n->t("<p>Would you like to {0}create a new entry{1}?</p>", [ "<a href='{$i18n->r("/award/create")}'>", "</a>" ]);

    return new AwardPartial($result, $noItemText);
  }

}
