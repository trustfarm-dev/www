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
namespace MovLib\Presentation\Gallery;

use \MovLib\Presentation\Gallery\MoviePhotoGallery;

/**
 * @coversDefaultClass \MovLib\Presentation\Gallery\MoviePhotoGallery
 * @author Skeleton Generator
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class MoviePhotoGalleryTest extends \MovLib\TestCase {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /** @var \MovLib\Presentation\Gallery\MoviePhotoGallery */
  protected $moviePhotoGallery;


  // ------------------------------------------------------------------------------------------------------------------- Fixtures


  /**
   * Called before each test.
   */
  protected function setUp() {
    $this->moviePhotoGallery = new MoviePhotoGallery();
  }

  /**
   * Called after each test.
   */
  protected function tearDown() {

  }


  // ------------------------------------------------------------------------------------------------------------------- Data Provider


  public static function dataProviderExample() {
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
   * @covers ::getBreadcrumbs
   * @todo Implement getBreadcrumbs
   */
  public function testGetBreadcrumbs() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::getGoneContent
   * @todo Implement getGoneContent
   */
  public function testGetGoneContent() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::getSecondaryNavigationMenuitems
   * @todo Implement getSecondaryNavigationMenuitems
   */
  public function testGetSecondaryNavigationMenuitems() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::getPageContent
   * @todo Implement getPageContent
   */
  public function testGetPageContent() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

  /**
   * @covers ::getImageDetails
   * @todo Implement getImageDetails
   */
  public function testGetImageDetails() {
    $this->markTestIncomplete("This test has not been implemented yet.");
  }

}
