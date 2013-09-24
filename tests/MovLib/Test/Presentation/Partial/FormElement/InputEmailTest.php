<?php

/*!
 * This file is part of {@link https://github.com/MovLib MovLib}.
 *
 * Copyright © 2013-present {@link http://movlib.org/ MovLib}.
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
namespace MovLib\Test\Presentation\Partial\FormElement;

use \MovLib\Presentation\Partial\FormElement\InputEmail;
use \MovLib\Presentation\Validation\EmailAddress;

/**
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class InputEmailTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers InputEmail::__construct
   */
  public function testConstruct() {
    $input = new InputEmail();
    $this->assertEquals("email", $input->attributes["type"]);
    $this->assertEquals(EmailAddress::MAX_LENGTH, $input->attributes["maxlength"]);
    $this->assertEquals(EmailAddress::PATTERN, $input->attributes["pattern"]);
  }

  /**
   * @covers InputEmail::__toString
   */
  public function testToString() {
    $input = (new InputEmail())->__toString();
    $this->assertContains("pattern='" . htmlspecialchars(EmailAddress::PATTERN, ENT_QUOTES|ENT_HTML5) . "'", $input);
    $this->assertContains("maxlength='" . EmailAddress::MAX_LENGTH . "'", $input);
    $this->assertContains("type='email'", $input);
  }

}
