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
namespace MovLib\Presentation\Partial;

/**
 * (Date)Time presentation.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Time extends \MovLib\Presentation\AbstractBase {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The time's {@see \DateTime} instance.
   *
   * @var \DateTime
   */
  protected $time;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new time partial.
   *
   * @param string $time [optional]
   *   A date/time string in a valid format as explined in {@link http://www.php.net/manual/en/datetime.formats.php Date
   *   and Time Formats} or an integer, which is treated as UNIX timestamp. Defaults to <code>"now"</code>.
   */
  public function __construct($time = "now") {
    if (is_int($time)) {
      $time = "@{$time}";
    }
    $this->time = new \DateTime($time);
  }

  /**
   * Get a relative string representation of the time.
   *
   * The usage of magic numbers is intended, these calculations will never change!
   *
   * @link http://stackoverflow.com/questions/11
   * @global \MovLib\Data\I18n $i18n
   * @return string
   *   Relative string representation of the time.
   */
  public function formatRelative() {
    global $i18n;
    $delta = $_SERVER["REQUEST_TIME"] - $this->time->getTimestamp();
    if ($delta < 60) {
      return $delta < 2 ? $i18n->t("one second ago") : $i18n->t("{0,number,integer} seconds ago", [ $delta ]);
    }
    if ($delta < 120) {
      return $i18n->t("a minute ago");
    }
    if ($delta < 2700) { // 45 minutes
      return $i18n->t("{0,number,integer} minutes ago", [ ($delta / 60) ]);
    }
    if ($delta < 5400) { // 90 minutes
      return $i18n->t("an hour ago");
    }
    if ($delta < 86400) { // 1 day
      return $i18n->t("{0,number,integer} hours ago", [ ($delta / 3600) ]);
    }
    if ($delta < 172800) { // 2 days
      return $i18n->t("yesterday");
    }
    if ($delta < 2592000) { // 30 days
      return $i18n->t("{0,number,integer} days ago", [ ($delta / 86400) ]);
    }
    if ($delta < 3.15569e7) { // 1 year
      $months = $delta / 2592000;
      return $months < 2 ? $i18n->t("one month ago") : $i18n->t("{0,number,integer} months ago", [ $months ]);
    }
    $years = $delta / 3.15569e7;
    return $years < 2 ? $i18n->t("one year ago") : $i18n->t("{0,number,integer} years ago", [ $years ]);
  }

}
