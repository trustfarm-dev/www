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


/**
 * Series with a certain award associated.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Series extends \MovLib\Presentation\Award\AbstractBase {


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new award series presentation.
   *
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Kernel $kernel
   */
  public function __construct() {
    global $i18n, $kernel;
    $this->award = new Award((integer) $_SERVER["AWARD_ID"]);
    $this->initPage($i18n->t("Series from {0}", [ $this->award->name ]));
    $this->pageTitle       = $i18n->t("Series from {0}", [ "<a href='{$this->award->route}'>{$this->award->name}</a>" ]);
    $this->breadcrumbTitle = $i18n->t("Series");
    $this->initLanguageLinks("/award/{0}/series", [ $this->award->id ], true);
    $this->initAwardBreadcrumb();
    $this->sidebarInit();

    $kernel->stylesheets[] = "award";
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


 /**
   * @inheritdoc
   * @global \MovLib\Data\I18n $i18n
   * @return \MovLib\Presentation\Partial\Alert
   */
  protected function getPageContent() {
    global $i18n;
    return new \MovLib\Presentation\Partial\Alert($i18n->t("The {0} feature isn’t implemented yet.", [ $i18n->t("series with award") ]), $i18n->t("Check back later"), \MovLib\Presentation\Partial\Alert::SEVERITY_INFO);
  }

}