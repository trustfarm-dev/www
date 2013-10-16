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
namespace MovLib\Test\Data;

use \MovDev\Database;
use \MovLib\Data\User;

/**
 * @coversDefaultClass \MovLib\Data\User
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class UserTest extends \PHPUnit_Framework_TestCase {


  // ------------------------------------------------------------------------------------------------------------------- Data Provider


  public static function dataProviderTestConstruct() {
    return [
      [ User::FROM_EMAIL, "richard@fussenegger.info" ],
      [ User::FROM_ID, 1 ],
      [ User::FROM_NAME, "Fleshgrinder" ],
      [ User::FROM_NAME, "fleshgrinder" ],
      [ User::FROM_NAME, "FlEsHgRiNdEr" ],
      [ User::FROM_NAME, "FLESHGRINDER" ],
    ];
  }

  public static function dataProviderTestConstructException() {
    return [
      [ User::FROM_EMAIL, "phpunit@movlib.org" ],
      [ User::FROM_ID, -1 ],
      [ User::FROM_NAME, "PHPUnit" ],
    ];
  }

  public static function dataProviderTestCheckNameExists() {
    return [
      [ "Fleshgrinder" ],
      [ "fleshgrinder" ],
      [ "FlEsHgRiNdEr" ],
      [ "FLESHGRINDER" ],
    ];
  }

  public static function dataProviderTestGetImageStyleAttributes() {
    return [
      [ User::IMAGE_STYLE_DEFAULT, [ "alt" => "PHPUnit" ] ],
      [ User::IMAGE_STYLE_THUMBNAIL, [ "alt" => "PHPUnit", "height" => 999, "width" => 999, "src" => "https://movlib.org/phpunit.jpg" ] ],
    ];
  }


  // ------------------------------------------------------------------------------------------------------------------- Fixtures


  public function tearDown() {
    exec("movdev db -s users");
  }


  // ------------------------------------------------------------------------------------------------------------------- Tests


  /**
   * @covers ::checkEmail
   * @group Database
   */
  public function testCheckEmailExists() {
    $this->assertTrue((new User(User::FROM_ID, 1))->checkEmail());
  }

  /**
   * @covers ::checkEmail
   * @group Database
   */
  public function testCheckEmailNotExists() {
    $user        = new User();
    $user->email = "phpunit@movlib.org";
    $this->assertFalse($user->checkEmail());
  }

  /**
   * @covers ::checkName
   * @dataProvider dataProviderTestCheckNameExists
   * @group Database
   */
  public function testCheckNameExists($name) {
    // We have to check that the query itself is agnostic to case changes (same as we did in the constructor test).
    $user       = new User();
    $user->name = $name;
    $this->assertTrue($user->checkName());
  }

  /**
   * @covers ::checkName
   * @group Database
   */
  public function testCheckNameNotExists() {
    $user       = new User();
    $user->name = "PHPUnit";
    $this->assertFalse($user->checkName());
  }

  /**
   * @covers ::commit
   * @group Database
   * @group FileSystem
   * @group Uploads
   */
  public function testCommit() {
    $user = new User(User::FROM_ID, 1);
    $user->birthday           = "2000-01-01";
    $user->countryId          = 1;
    $user->profile            = "PHPUnit";
    $user->private            = true;
    $user->realName           = "PHPUnit PHPUnit";
    $user->sex                = 10;
    $user->systemLanguageCode = "xx";
    $user->timeZoneId         = "PHPUnit/PHPUnit";
    $user->website            = "http://phpunit.net/";
    $user->commit();

    $user = new User(User::FROM_ID, 1);
    $this->assertEquals("2000-01-01", $user->birthday);
    $this->assertEquals(1, $user->countryId);
    $this->assertEquals("PHPUnit", $user->profile);
    $this->assertTrue($user->private);
    $this->assertEquals("PHPUnit PHPUnit", $user->realName);
    $this->assertEquals(10, $user->sex);
    $this->assertEquals("xx", $user->systemLanguageCode);
    $this->assertEquals("PHPUnit/PHPUnit", $user->timeZoneId);
    $this->assertEquals("http://phpunit.net/", $user->website);
  }

  /**
   * @covers ::__construct
   * @dataProvider dataProviderTestConstruct
   * @group Database
   */
  public function testConstruct($from, $value) {
    $user = new User($from, $value);
    $this->assertEquals(1, $user->id);
    $this->assertEquals("Fleshgrinder", $user->name);
    $this->assertEquals("richard@fussenegger.info", $user->email);
    $this->assertTrue(is_bool($user->private));
    $this->assertTrue(is_bool($user->deactivated));
  }

  /**
   * @covers ::__construct
   * @dataProvider dataProviderTestConstructException
   * @expectedException \MovLib\Exception\UserException
   * @group Database
   */
  public function testConstructNoUser($from, $value) {
    new User($from, $value);
  }

  /**
   * @covers ::deactivate
   * @covers ::deleteImageOriginalAndStyles
   * @group Database
   * @group FileSystem
   * @group Uploads
   */
  public function testDeactivate() {
    $user = new User(User::FROM_ID, 1);
    $user->deactivate();

    $user = new User(User::FROM_ID, 1);
    $this->assertFalse(is_file(get_reflection_method($user, "getImagePath")->invokeArgs($user, [ User::IMAGE_STYLE_DEFAULT ])));
    $this->assertNull(get_reflection_property($user, "imageChanged")->getValue($user));
    $this->assertNull(get_reflection_property($user, "imageExtension")->getValue($user));
    $this->assertNull($user->birthday);
    $this->assertNull($user->countryId);
    $this->assertTrue($user->deactivated);
    $this->assertEmpty($user->profile);
    //$this->assertNull($user->facebook);
    //$this->assertNull($user->googlePlus);
    $this->assertFalse($user->private);
    $this->assertNull($user->realName);
    $this->assertEquals(0, $user->sex);
    $this->assertEquals(ini_get("date.timezone"), $user->timeZoneId);
    //$this->assertNull($user->twitter);
    $this->assertNull($user->website);
  }

  /**
   * @covers ::deleteImageOriginalAndStyles
   * @group FileSystem
   * @group Uploads
   */
  public function testDeleteImageOriginalAndStyles() {
    $user             = new User(User::FROM_ID, 1);
    $rmGetImagePath   = get_reflection_method($user, "getImagePath");
    $rpImageChanged   = get_reflection_property($user, "imageChanged");
    $rpImageExtension = get_reflection_property($user, "imageExtension");

    $this->assertTrue(is_file($rmGetImagePath->invokeArgs($user, [ User::IMAGE_STYLE_DEFAULT ])));
    $this->assertTrue(is_file($rmGetImagePath->invokeArgs($user, [ User::IMAGE_STYLE_THUMBNAIL ])));
    $this->assertTrue($user->imageExists);
    $this->assertNotEmpty($rpImageChanged->getValue($user));
    $this->assertNotEmpty($rpImageExtension->getValue($user));

    get_reflection_method($user, "deleteImageOriginalAndStyles")->invoke($user);
    $this->assertFalse(is_file($rmGetImagePath->invokeArgs($user, [ User::IMAGE_STYLE_DEFAULT ])));
    $this->assertFalse(is_file($rmGetImagePath->invokeArgs($user, [ User::IMAGE_STYLE_THUMBNAIL ])));
    $this->assertFalse($user->imageExists);
    $this->assertNull($rpImageChanged->getValue($user));
    $this->assertNull($rpImageExtension->getValue($user));
  }

  /**
   * @covers ::getImageStyleAttributes
   * @dataProvider dataProviderTestGetImageStyleAttributes
   * @group Presentation
   */
  public function testGetImageStyleAttributes($style, $dataProviderAttributes) {
    $user          = new User(User::FROM_ID, 1);
    $regExImageURL = preg_quote(get_reflection_method($user, "getImageURL")->invokeArgs($user, [ $style ]));
    $span          = get_reflection_property($user, "span")->getValue($user);
    $attributes    = $user->getImageStyleAttributes($style, $dataProviderAttributes);
    $this->assertEquals($span[$style], $attributes["height"]);
    $this->assertEquals($span[$style], $attributes["width"]);
    $this->assertRegExp("#^{$regExImageURL}\?c=[0-9]+$#", $attributes["src"]);
    $this->assertArrayHasKey("height", $dataProviderAttributes);
    $this->assertEquals($attributes["height"], $dataProviderAttributes["height"]);
    $this->assertArrayHasKey("width", $dataProviderAttributes);
    $this->assertEquals($attributes["width"], $dataProviderAttributes["width"]);
    $this->assertArrayHasKey("src", $dataProviderAttributes);
    $this->assertEquals($attributes["src"], $dataProviderAttributes["src"]);
  }

  /**
   * @covers ::getRandomPassword
   * @group Presentation
   */
  public function testGetRandomPassword() {
    $this->assertRegExp("/.{20}/", User::getRandomPassword());
  }

  /**
   * @covers ::getRegistrationData
   * @group Database
   */
  public function testGetRegistrationData() {
    $user        = new User();
    $user->name  = "PHPUnit";
    $user->email = "phpunit@movlib.org";
    $user->prepareRegistration("Test1234");
    $data        = $user->getRegistrationData();
    foreach ([ "name", "email", "password", "attempts" ] as $key) {
      $this->assertArrayHasKey($key, $data);
    }
    $this->assertEquals("PHPUnit", $data["name"]);
    $this->assertEquals("phpunit@movlib.org", $data["email"]);
    $this->assertEquals(1, $data["attempts"]);
    $this->assertTrue(password_verify("Test1234", $data["password"]));
    return $user;
  }

  /**
   * @covers ::getRegistrationData
   * @expectedException \MovLib\Exception\UserException
   * @expectedExceptionMessage No data found
   * @group Database
   */
  public function testGetRegistrationDataExpired() {
    $user        = new User();
    $user->name  = "PHPUnit";
    $user->email = "phpunit@movlib.org";
    $user->prepareRegistration("Test1234");
    (new Database())->query("UPDATE `tmp` SET `created` = FROM_UNIXTIME(?) WHERE `key` = ?", "ss", [ strtotime("-25 hours"), "registration-phpunit@movlib.org" ]);
    $user->getRegistrationData();
  }

  /**
   * @covers ::getRegistrationData
   * @expectedException \MovLib\Exception\UserException
   * @expectedExceptionMessage No data found
   * @group Database
   */
  public function testGetRegistrationDataNoRecord() {
    (new User())->getRegistrationData();
  }

  /**
   * @covers ::moveUploadedImage
   * @group Database
   * @group FileSystem
   * @group Uploads
   */
  public function testMoveUploadedImage() {
    $user           = new User(User::FROM_ID, 1);
    $rmGetImagePath = get_reflection_method($user, "getImagePath");
    $source         = tempnam(sys_get_temp_dir(), "phpunit");
    copy("{$_SERVER["DOCUMENT_ROOT"]}/db/seeds/uploads/user/fleshgrinder.2.jpg", $source);
    get_reflection_method($user, "deleteImageOriginalAndStyles")->invoke($user);
    $user->moveUploadedImage($source, 220, 220, "jpg");
    $this->assertFalse(is_file($source));
    $this->assertTrue(is_file($rmGetImagePath->invokeArgs($user, [ User::IMAGE_STYLE_DEFAULT ])));
    $this->assertTrue(is_file($rmGetImagePath->invokeArgs($user, [ User::IMAGE_STYLE_THUMBNAIL ])));
    $this->assertTrue($user->imageExists);
    $this->assertEquals($_SERVER["REQUEST_TIME"], get_reflection_property($user, "imageChanged")->getValue($user));
    $this->assertEquals("jpg", get_reflection_property($user, "imageExtension")->getValue($user));
  }

  /**
   * @covers ::passwordHash
   * @group Database
   */
  public function testPasswordHash() {
    $user = new User();
    $this->assertTrue(password_verify("Test1234", get_reflection_method($user, "passwordHash")->invokeArgs($user, [ "Test1234" ])));
  }

  /**
   * @covers ::prepareRegistration
   * @group Database
   */
  public function testPrepareRegistration() {
    $user        = new User();
    $user->name  = "PHPUnit";
    $user->email = "phpunit@movlib.org";
    $this->assertEquals($user, $user->prepareRegistration("Test1234"));
    (new Database())->query("TRUNCATE TABLE `tmp`");
  }

  /**
   * @covers ::prepareRegistration
   * @expectedException \MovLib\Exception\UserException
   * @expectedExceptionMessage Too many registration attempts
   * @group Database
   */
  public function testPrepareRegistrationTooManyAttempts() {
    $user        = new User();
    $user->name  = "PHPUnit";
    $user->email = "phpunit@movlib.org";
    try {
      while (true) {
        $user->prepareRegistration("Test1234");
      }
    }
    finally {
      (new Database())->query("TRUNCATE TABLE `tmp`");
    }
  }

  /**
   * @covers ::prepareRegistration
   * @group Database
   */
  public function testPrepareRegistrationTooManyExpiredAttempts() {
    $user        = new User();
    $user->name  = "PHPUnit";
    $user->email = "phpunit@movlib.org";
    $db          = new Database();
    $c           = User::MAXIMUM_ATTEMPTS * 2;
    $time        = strtotime("-25 hours");
    $key         = "registration-{$user->email}";
    for ($i = 0; $i < $c; ++$i) {
      $user->prepareRegistration("Test1234");
      $db->query("UPDATE `tmp` SET `created` = FROM_UNIXTIME({$time}) WHERE `key` = '{$key}'");
    }
    $db->query("TRUNCATE TABLE `tmp`");
  }

  /**
   * @covers ::reactivate
   * @group Database
   */
  public function testReactivate() {
    $user = new User(User::FROM_ID, 1);
    $user->deactivate()->reactivate();
    $this->assertFalse($user->deactivated);
    $this->assertFalse((bool) (new Database())->selectAssoc("SELECT `deactivated` FROM `users` WHERE `user_id` = ?", "d", [ 1 ])["deactivated"]);
  }

  /**
   * @covers ::register
   * @group Database
   */
  public function testRegister() {
    global $i18n;
    $user        = new User();
    $user->name  = "PHPUnit";
    $user->email = "phpunit@movlib.org";
    $user->prepareRegistration("Test1234");
    $data = $user->getRegistrationData();
    $user->register($data["password"]);
    $this->assertEquals("PHPUnit", $user->name);
    $this->assertEquals("phpunit@movlib.org", $user->email);
    $this->assertEquals(get_reflection_property($user, "insertId")->getValue($user), $user->id);

    $result = (new Database())->selectAssoc("SELECT * FROM `users` WHERE `user_id` = ?", "d", [ $user->id ]);
    $this->assertEmpty($result["dyn_profile"]);
    $this->assertEquals("PHPUnit", $result["name"]);
    $this->assertEquals("phpunit", $result["avatar_name"]);
    $this->assertEquals("phpunit@movlib.org", $result["email"]);
    $this->assertEquals($i18n->languageCode, $result["system_language_code"]);
    $this->assertTrue(password_verify("Test1234", $result["password"]));
  }

  /**
   * @covers ::updateEmail
   * @group Database
   */
  public function testUpdateEmail() {
    $user = new User(User::FROM_ID, 1);
    $this->assertEquals("richard@fussenegger.info", $user->email);
    $user->updateEmail("phpunit@movlib.org");
    $this->assertEquals("phpunit@movlib.org", $user->email);
    $this->assertEquals("phpunit@movlib.org", (new Database())->selectAssoc("SELECT `email` FROM `users` WHERE `user_id` = ?", "d", [ 1 ])["email"]);
  }

  /**
   * @covers ::updatePassword
   * @group Database
   */
  public function testUpdatePassword() {
    $db      = new Database();
    $session = new \MovLib\Data\Session();
    $session->authenticate("richard@fussenegger.info", "Test1234");
    $oldHash = $db->selectAssoc("SELECT `password` FROM `users` WHERE `user_id` = ?", "d", [ 1 ])["password"];
    $user = new User(User::FROM_ID, 1);
    $user->updatePassword("phpunitPassword");
    $session->authenticate("richard@fussenegger.info", "phpunitPassword");
    $newHash = $db->selectAssoc("SELECT `password` FROM `users` WHERE `user_id` = ?", "d", [ 1 ])["password"];
    $this->assertNotEquals($oldHash, $newHash);
  }

}