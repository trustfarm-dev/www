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
namespace MovLib\Tool\Console\Command;

use \MovLib\Tool\Console\Command\AbstractInstall;

/**
 * @coversDefaultClass \MovLib\Tool\Console\Command\AbstractInstall
 * @author Skeleton Generator
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class AbstractInstallTest extends \MovLib\TestCase {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /** @var \MovLib\Tool\Console\Command\AbstractInstall */
  protected $abstractInstall;


  // ------------------------------------------------------------------------------------------------------------------- Fixtures


  /**
   * Called before each test.
   */
  protected function setUp() {
    $this->abstractInstall = new AbstractInstall();
  }

  /**
   * Called after each test.
   */
  protected function tearDown() {

  }


  // ------------------------------------------------------------------------------------------------------------------- Data Provider


  public function dataProviderExample() {
    return [];
  }


  // ------------------------------------------------------------------------------------------------------------------- Tests


  /**
   * @covers ::__construct
   * @todo Implement __construct
   */
  public function testConstruct() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::configureInstallation
   * @todo Implement configureInstallation
   */
  public function testConfigureInstallation() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::getInstallationName
   * @todo Implement getInstallationName
   */
  public function testGetInstallationName() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::getVersion
   * @todo Implement getVersion
   */
  public function testGetVersion() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::git
   * @todo Implement git
   */
  public function testGit() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::install
   * @todo Implement install
   */
  public function testInstall() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::setInstallationName
   * @todo Implement setInstallationName
   */
  public function testSetInstallationName() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::setVersion
   * @todo Implement setVersion
   */
  public function testSetVersion() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::tar
   * @todo Implement tar
   */
  public function testTar() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::uninstall
   * @todo Implement uninstall
   */
  public function testUninstall() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::wget
   * @todo Implement wget
   */
  public function testWget() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

}