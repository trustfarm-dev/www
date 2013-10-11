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

/**
 * @coversDefaultClass \MovLib\Presentation\Partial\FormElement\AbstractFormElement
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class AbstractFormElementTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers ::__construct
   * @group Presentation
   */
  public function testConstruct() {
    $stub = $this->getMockForAbstractClass("\\MovLib\\Presentation\\Partial\\FormElement\\AbstractFormElement", [ "phpunit", "PHPUnit", [ "foo" => "bar" ]]);
    foreach ([ "foo", "id", "name", "tabindex" ] as $key) {
      $this->assertArrayHasKey($key, $stub->attributes);
    }
    $this->assertEquals("PHPUnit", get_reflection_property($stub, "label")->getValue($stub));
    $this->assertEquals("bar", $stub->attributes["foo"]);
    $this->assertEquals("phpunit", $stub->attributes["id"]);
    $this->assertEquals("phpunit", $stub->attributes["name"]);
    $this->assertEquals("phpunit", $stub->id);
    $this->assertTrue(is_int($stub->attributes["tabindex"]));
    return $stub;
  }

  /**
   * @covers ::invalid
   * @group Presentation
   */
  public function testInvalid() {
    $stub = $this->getMockForAbstractClass("\\MovLib\\Presentation\\Partial\\FormElement\\AbstractFormElement", [ "phpunit", "PHPUnit" ]);
    $this->assertEquals($stub, $stub->invalid());
    $this->assertArrayHasKey("aria-invalid", $stub->attributes);
    $this->assertArrayHasKey("class", $stub->attributes);
    $this->assertContains("invalid", $stub->attributes["class"]);
    $this->assertEquals("true", $stub->attributes["aria-invalid"]);
  }

  /**
   * @covers ::setHelp
   * @group Presentation
   */
  public function testSetHelp() {
    $stub = $this->getMockForAbstractClass("\\MovLib\\Presentation\\Partial\\FormElement\\AbstractFormElement", [ "phpunit", "PHPUnit" ]);
    $this->assertEquals($stub, $stub->setHelp("help message", false));
    $this->assertArrayHasKey("aria-describedby", $stub->attributes);
    $this->assertEquals("phpunit-help", $stub->attributes["aria-describedby"]);
    $this->assertInstanceOf("\\MovLib\\Presentation\\Partial\\Help", get_reflection_property($stub, "help")->getValue($stub));
  }

}
