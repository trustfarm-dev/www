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
namespace MovLib\Tool\Console;

/**
 * MovLib Command Line Interface
 *
 * The MovLib command line interface is a Symfony2 Console Application and combines all possible MovLib Symfony2 Console
 * Commands for easy execution. The CLI is used to run several administrative tasks. The MovLib software does not have
 * any administrative backend, instead all such tasks are handled with console applications.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Application extends \Symfony\Component\Console\Application {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * Array used to collect available commands.
   *
   * @var array
   */
  private $commands = [];


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new MovLib CLI application.
   *
   * @global \MovLib\Tool\Kernel $kernel
   */
  public function __construct() {
    global $kernel;

    // Call the Symfony console constructor.
    parent::__construct("MovLib Command Line Interface (CLI)", $kernel->version);

    // Always include production commands.
    $this->importCommands("Production");

    // Only include development commands if the flag is set to false.
    if ($kernel->production === false) {
      $this->importCommands("Development");
    }

    // This loop allows development commands to override / extend production commands.
    foreach ($this->commands as $shortName => $command) {
      $this->add(new $command());
    }
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * Imports all instantiable commands from the given directory (subnamespace directory of Command).
   *
   * @param string $directory
   *   The directory name.
   * @return this
   */
  protected function importCommands($directory) {
    foreach (glob(__DIR__ . "/Command/{$directory}/*.php") as $command) {
      $shortName = basename($command, ".php");
      $command   = "\\MovLib\\Tool\\Console\\Command\\{$directory}\\{$shortName}";

      // Make sure we don't include any abstract classes, interfaces or traits.
      if ((new \ReflectionClass($command))->isInstantiable()) {
        $this->commands[$shortName] = $command;
      }
    }

    return $this;
  }

}
