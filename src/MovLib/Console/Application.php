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
namespace MovLib\Console;

use \MovLib\Core\Kernel;
use \MovLib\Core\Container;

/**
 * MovLib Command Line Interface Application.
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
final class Application extends \Symfony\Component\Console\Application {

  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "Application";
  // @codingStandardsIgnoreEnd

  /**
   * Instantiate new MovLib CLI application.
   *
   * @param \MovLib\Core\Container $container
   *   The dependency injection container.
   * @param string $basename
   *   The basename of the executed binary (without extension).
   */
  public function __construct(Container $container, $basename) {
    parent::__construct($basename, Kernel::VERSION);
    cli_set_process_title($basename);

    // Guess the directory containing the commands, if the result is "Lig" then the "movlib.php" binary was invoked
    // directly and we simply assume that "Admin" was meant.
    $commandDirectory = ucfirst(str_replace("mov", "", $basename));
    if ($commandDirectory == "Lib") {
      $commandDirectory = "Admin";
    }

    /* @var $fileinfo \SplFileInfo */
    foreach ($container->fs->getRecursiveIterator("dr://src/MovLib/Console/Command/{$commandDirectory}", \RecursiveIteratorIterator::SELF_FIRST) as $fileinfo) {
      if ($fileinfo->isFile() && $fileinfo->getExtension() == "php") {
        $command   = strtr(str_replace([ "dr://src/", ".php" ], "", $fileinfo->getPathname()), "/", "\\");
        $reflector = new \ReflectionClass($command);
        if ($reflector->isInstantiable() && $reflector->isSubclassOf("\\Symfony\\Component\\Console\\Command\\Command")) {
          $this->add(new $command($container));
        }
      }
    }
  }

}
