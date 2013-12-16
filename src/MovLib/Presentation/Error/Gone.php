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
namespace MovLib\Presentation\Error;

use \MovLib\Presentation\ErrorPage;
use \MovLib\Presentation\Partial\Alert;

/**
 * Represents the "gone" client error.
 *
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Gone extends \MovLib\Exception\AbstractClientException {

  /**
   * @inheritdoc
   */
  public function getPresentation() {
    global $i18n;
    http_response_code(410);
    return (new ErrorPage(
      $i18n->t("Gone"),
      new Alert(
        $i18n->t(
          "There can be various reasons why you might see this error message. If you feel that receiving this error is a mistake please {0}contact us{1}.",
          [ "<a href='{$i18n->r("/contact")}'>", "</a>" ]
        ),
        $i18n->t("The requested page is no longer available."),
        Alert::SEVERITY_ERROR
      )
    ))->getPresentation();
  }

}