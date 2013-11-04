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
namespace MovLib\Tool\Console\Command\Development;

use \MovLib\Data\UnixShell as sh;
use \Symfony\Component\Console\Input\InputOption;

/**
 * Extended cache inspector with some very special commands.
 *
 * @link http://www.linuxatemyram.com/play.html
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class CacheInspector extends \MovLib\Tool\Console\Command\Production\CacheInspector {

  /**
   * @inheritdoc
   */
  protected function configure() {
    global $kernel;
    parent::configure();
    if ($kernel->production === false) {
      $this->addInputOption("disk", InputOption::VALUE_NONE, "Empty the server's disk cache (requires super user privileges).");
    }
  }

  public function emptyDiskCache() {
    $this->checkPrivileges();
    $file = "/proc/sys/vm/drop_caches";
    if (!is_file($file)) {
      throw new FileSystemException("Couldn't find '{$file}'!");
    }
    if (sh::execute("echo 3 | tee /proc/sys/vm/drop_caches") === false) {
      throw new \RuntimeException("Couldn't purge disk cache!");
    }
    $this->write("Purged disk cache!", self::MESSAGE_TYPE_INFO);
    return $this;
  }

}