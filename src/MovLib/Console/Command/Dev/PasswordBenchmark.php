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
namespace MovLib\Console\Command\Dev;

use \Symfony\Component\Console\Input\InputArgument;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Run benchmark for password costs.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class PasswordBenchmark extends \MovLib\Console\Command\AbstractCommand {

  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "PasswordBenchmark";
  // @codingStandardsIgnoreEnd

  /**
   * {@inheritdoc}
   */
  public function configure() {
    $this->setName("password-benchmark");
    $this->setDescription("Run password cost benchmark.");
    $this->addArgument("time-target", InputArgument::OPTIONAL, "The desired time target.", 0.5);
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $timeTarget = $input->getArgument("time-target");
    $cost       = 9;
    $this->write("Starting password cost benchmark ...");
    $this->progressStart();
    do {
      $cost++;
      $start  = microtime(true);
      password_hash("password-benchmark", $this->config->passwordAlgorithm, $this->config->passwordOptions);
      $end    = microtime(true);
      $actual = $end - $start;
      $this->progressAdvance();
    }
    while ($actual < $timeTarget);
    $this->progressFinish();
    $this->write(
      "Optimal password cost setting for the desired time target of {$timeTarget} seconds would be " .
      "<info>{$cost}</info> (hashing will take ~{$actual} seconds).\n"
    );

    return 0;
  }

}
