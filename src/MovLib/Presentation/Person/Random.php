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
namespace MovLib\Presentation\Person;

use \MovLib\Data\Person\Person;
use \MovLib\Presentation\Partial\Alert;
use \MovLib\Presentation\Redirect\SeeOther as SeeOtherRedirect;

/**
 * Random person presentation.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Random {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * A random person ID.
   *
   * @var integer
   */
  private $personId;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Redirect to random person presentation.
   *
   * @throws \MovLib\Presentation\Redirect\SeeOther
   */
  public function __construct() {
    $this->personId = Person::getRandomPersonId();
    if (isset($this->personId)) {
      throw new SeeOtherRedirect($this->intl->r("/person/{0}", [ $this->personId ]));
    }
    else {
      $kernel->alerts .= new Alert(
        $this->intl->t("There is currently no person in our database"),
        $this->intl->t("Check back later"),
        Alert::SEVERITY_INFO
      );
      throw new SeeOtherRedirect("/");
    }
  }

}
