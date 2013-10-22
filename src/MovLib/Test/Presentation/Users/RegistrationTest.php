<?php

/* !
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
namespace MovLib\Test\Presentation\Users;

use \MovLib\Data\User\Session;
use \MovLib\Data\User\Full as User;
use \MovLib\Presentation\Email\Users\Registration as RegistrationEmail;
use \MovLib\Presentation\Email\Users\RegistrationEmailExists;
use \MovLib\Presentation\Partial\Alert;
use \MovLib\Presentation\Users\Registration;

/**
 * @coversDefaultClass \MovLib\Presentation\Users\Registration
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class RegistrationTest extends \MovLib\Test\TestCase {

  // ------------------------------------------------------------------------------------------------------------------- Properties

  /** @var \MovLib\Data\User\Session */
  private static $sessionBackup;

  // ------------------------------------------------------------------------------------------------------------------- Fixtures


  public static function setUpBeforeClass() {
    global $session;
    self::$sessionBackup = clone $session;
    $session              = new Session();
    $_SERVER["PATH_INFO"] = "/users/registration";
  }

  public static function tearDownAfterClass() {
    global $session;
    $session = self::$sessionBackup;
    unset($_SERVER["PATH_INFO"]);
    unset($_POST);
    unset($_GET);
    exec("movdev db -a");
  }

  // ------------------------------------------------------------------------------------------------------------------- Helpers

  /**
   * Get registration instance with <var>$post</var> exported to <var>$_POST</var>.
   *
   * @param array $post [optional]
   *   Overrides default <var>$_POST</var> values.
   * @return \MovLib\Presentation\Users\Registration
   *   The created instance.
   */
  private function _getRegistration(array $post = [ ]) {
    unset($_POST);
    $_POST = array_merge([
      "username" => "PHPUnit",
      "email"    => "phpunit@movlib.org",
      "password" => "Test1234",
      "terms"    => "on",
      "form_id"  => "users-registration",
      ], $post);
    return new Registration();
  }

  /**
   * Helper method to test the various username tests.
   *
   * @param string $username
   *   The username to test.
   * @param string $contains
   *   The string that should be part of the presentation.
   * @return this
   */
  private function _testUsername($username, $contains) {
    $this->assertContains(
      $contains, $this->_getRegistration([ "username" => $username ])->getPresentation(), "Couldn't assert that username validation works correctly."
    );
    return $this;
  }

  // ------------------------------------------------------------------------------------------------------------------- Tests

  /**
   * @covers ::__construct
   * @expectedException \MovLib\Exception\Client\RedirectSeeOtherException
   * @expectedExceptionMessage Redirecting user to /my with status 303.
   */
  public function testRedirectIfAuthenticated() {
    global $session;
    try {
      $session = self::$sessionBackup;
      new Registration();
    }
    finally {
      $session = new Session();
    }
  }

  /**
   * @covers ::__construct
   * @covers ::getContent
   */
  public function testGetContent() {
    $registration = new Registration();
    $form         = $this->getProperty($registration, "form");
    $this->assertEquals(
      "<div class='container'><div class='row'>{$form}</div></div>", $this->invoke($registration, "getContent"), "Couldn't assert that registration content contains self-rendered form."
    );
  }

  /**
   * @covers ::__construct
   * @covers ::getContent
   */
  public function testGetContentValidRegistration() {
    $registration = $this->_getRegistration();
    $form         = $this->getProperty($registration, "form");
    $content      = $this->invoke($registration, "getContent");
    $this->assertNotContains((string) $form, $content);
    $this->assertContains("Mistyped something?", $content, "Couldn't assert that valid registration is accepted.");
  }

  /**
   * @covers ::validate
   */
  public function testUsernameSpaceAtBeginning() {
    $this->_testUsername(" PHPUnit", "username cannot begin with a space");
  }

  /**
   * @covers ::validate
   */
  public function testUsernameSpaceAtEnd() {
    $this->_testUsername("PHPUnit ", "username cannot end with a space");
  }

  /**
   * @covers ::validate
   */
  public function testUsernameMultipleSpaces() {
    $this->_testUsername("PHP  Unit", "username cannot contain multiple spaces in a row");
  }

  /**
   * @covers ::validate
   */
  public function testUsernameIllegalCharacters() {
    $this->_testUsername("PHP/Unit", "username cannot contain any of the following characters");
  }

  /**
   * @covers ::validate
   */
  public function testUsernameLength() {
    $this->_testUsername(str_repeat("PHPUnit ", 10), "username is too long");
  }

  /**
   * @covers ::validate
   */
  public function testUsernameExists() {
    $this->_testUsername("Fleshgrinder", "username is already taken");
  }

  /**
   * @covers ::validate
   */
  public function testTerms() {
    $registration = $this->_getRegistration([ "terms" => "" ]);
    $this->assertContains("You have to accept the", $registration->getPresentation(), "Couldn't assert that terms have to be accepted.");
  }

  /**
   * @covers ::validate
   */
  public function testEmailExists() {
    $this->_getRegistration([ "email" => "richard@fussenegger.info" ]);
    $found = false;
    foreach ($this->getProperty("\\MovLib\\Data\\Delayed\\Mailer", "emails") as $email) {
      if (($found = $email instanceof RegistrationEmailExists) === true) {
        break;
      }
    }
    $this->assertTrue($found);
  }

  /**
   * @covers ::validate
   */
  public function testValidRegistration() {
    $registration = $this->_getRegistration();
    $found        = false;
    foreach ($this->getProperty("\\MovLib\\Data\\Delayed\\Mailer", "emails") as $email) {
      if (($found = $email instanceof RegistrationEmail) === true) {
        break;
      }
    }
    $this->assertTrue($found);
    $this->assertTrue($this->getProperty($registration, "accepted"));
    $this->assertEquals(202, http_response_code());
    $this->assertContains("Registration Successful", $registration->alerts);
  }

  /**
   * @covers ::validate
   */
  public function testTooManyRegistrations() {
    for ($i = 0; $i < 6; ++$i) {
      $registration = $this->_getRegistration();
    }
    $this->assertContains("Too many registration attempts", $registration->getPresentation());
  }

}