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
namespace MovLib\Presentation\Partial\FormElement;

use \MovLib\Presentation\Partial\FormElement\InputHTML;

/**
 * @coversDefaultClass \MovLib\Presentation\Partial\FormElement\Textarea
 * @author Skeleton Generator
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class TextareaTest extends \MovLib\TestCase {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /** @var \MovLib\Presentation\Partial\FormElement\InputHTML */
  protected $textarea;


  // ------------------------------------------------------------------------------------------------------------------- Fixtures


  /**
   * Called before each test.
   */
  protected function setUp() {
    $this->textarea = new InputHTML();
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
   * @covers ::__toString
   * @todo Implement __toString
   */
  public function testToString() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::validate
   * @todo Implement validate
   */
  public function testValidate() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

}
