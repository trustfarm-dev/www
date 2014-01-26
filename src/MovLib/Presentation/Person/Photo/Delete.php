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
namespace MovLib\Presentation\Person\Photo;

use \MovLib\Data\Person\Person;
use \MovLib\Presentation\Partial\Alert;

/**
 * Description of Delete
 *
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 */
class Delete extends \MovLib\Presentation\Page {

  public function __construct() {
    global $i18n;

    // Try to load person data.
    $this->person = new Person($_SERVER["PERSON_ID"]);

    // Initialize language links.
    $routeArgs = [ $this->person->id ];
    $this->initLanguageLinks("/person/{0}/photo", $routeArgs);

    // Initialize page.
    $this->initPage($i18n->t("Delete Photo of {person_name}", [ "person_name" => $this->person->name ]));
    $this->pageTitle = $i18n->t("Delete Photo of {person_name}", [ "person_name" => "<a href='{$this->person->route}'>{$this->person->name}</a>" ]);

    // Initialize breadcrumbs.
    $this->initBreadcrumb([
      [ $i18n->rp("/persons"), $i18n->t("Persons") ],
      [ $this->person->route, $this->person->name ],
    ]);
  }

  public function getContent() {
    global $i18n;
    $this->alerts .= new Alert(
      $i18n->t("The delete person photo feature isn’t implemented yet."),
      $i18n->t("Check back later"),
      Alert::SEVERITY_INFO
    );
  }
}