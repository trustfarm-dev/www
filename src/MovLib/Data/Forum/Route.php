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
namespace MovLib\Data\Forum;

use \MovLib\Component\String;

/**
 * Defines the forum route object.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
final class Route extends \MovLib\Core\Routing\Route {

  /**
   * The route's forum.
   *
   * @var \MovLib\Data\Forum\Forum
   */
  protected $forum;

  /**
   * {@inheritdoc}
   */
  public function __construct(\MovLib\Core\Intl $intl, \MovLib\Data\Forum\Forum $forum, $path, array $options = array()) {
    parent::__construct($intl, $path, $options);
    $this->forum = $forum;
  }

  /**
   * {@inheritdoc}
   */
  public function compile($languageCode = null) {
    if ($languageCode !== $this->intl->code) {
      $this->args[0] = String::sanitizeFilename($this->forum->getTitle($languageCode));
    }
    return parent::compile($languageCode);
  }

}
