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
namespace MovLib\Component;

/**
 * Defines the URL object.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
final class URL {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  public $scheme;

  public $host;

  public $port;

  public $user;

  public $pass;

  public $path;

  public $query;

  public $fragment;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new URL.
   *
   * @param mixed $url
   *   The URL to parse and export into class scope.
   */
  public function __construct($url) {

  }

  /**
   * Get the URL's string representation.
   *
   * @return string
   *   The URL's string representation.
   */
  public function __toString() {
    return "";
  }


  // ------------------------------------------------------------------------------------------------------------------- Static Methods


  /**
   * Encode URL path preserving slashes.
   *
   * @param string $path
   *   The URL path to encode.
   * @return string
   *   The encoded URL path.
   */
  public static function encodePath($path) {
    if (empty($path) || $path == "/") {
      return $path;
    }
    return str_replace("%2F", "/", rawurlencode($path));
  }

}