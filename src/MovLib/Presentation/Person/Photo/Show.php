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

/**
 * Image details presentation for a person's photo.
 *
 * @route /person/{id}/photo
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Show extends \MovLib\Presentation\AbstractPresenter {

  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "Show";
  // @codingStandardsIgnoreEnd

  /**
   * Initialize person photo presentation.
   */
  public function init() {
    $this->person = new Person($this->container, (integer) $_SERVER["PERSON_ID"]);
    $this->initPage($this->intl->t("Photo of {0}", $this->person->name));
    $this->pageTitle        = $this->intl->t("Photo of {0}", [ "<a href='{$this->person->route}'>{$this->person->name}</a>" ]);
    $this->initLanguageLinks("/person/{0}/photo", [ $this->person->id ]);
    $this->breadcrumb
      ->addCrumb($this->person->set->route, $this->intl->t("Persons"))
      ->addCrumb($this->person->route, $this->person->lemma)
    ;
    $this->contentBefore = "<div class='c'>";
    $this->contentAfter  = "</div>";
  }

  public function getContent() {
    return $this->checkBackLater("person photo");
  }

}
