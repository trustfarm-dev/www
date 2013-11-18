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
namespace MovLib\Data\Image;

use \MovLib\Data\Country;
use \MovLib\Data\Image\Style;
//use \MovLib\Data\License;
use \MovLib\Data\User\User;
use \MovLib\Exception\ImageException;

/**
 * @todo Description of MoviePoster
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class MoviePoster extends \MovLib\Data\Image\AbstractImage {


  // ------------------------------------------------------------------------------------------------------------------- Constants


  const IMAGE_STYLE_SPAN_08 = \MovLib\Data\Image\SPAN_08;


  // ------------------------------------------------------------------------------------------------------------------- Properties

  /**
   * The image's alternative text.
   *
   * @var string
   */
  protected $alternativeText;

  /**
   * The image's country ID.
   *
   * @var null|integer
   */
  public $countryId;

  /**
   * The image's translated description (current display language).
   *
   * @var null|string
   */
  public $description;

  /**
   * The image's path within the upload directory.
   *
   * @see MoviePoster::__construct()
   * @var string
   */
  protected $imageDirectory = "movie";

  /**
   * The image's ID (unique together with the movie ID).
   *
   * @var integer
   */
  public $id;

  /**
   * The image's license ID.
   *
   * @var integer
   */
  public $licenseId;

  /**
   * The image's unique movie ID.
   *
   * @var integer
   */
  protected $movieId;

  /**
   * The image's route to its own details page or to the upload page if this image doesn't exist yet.
   *
   * @var string
   */
  public $route;

  /**
   * The image's source URL.
   *
   * @var string
   */
  public $source;

  /**
   * The image's type ID.
   *
   * @var integer
   */
  protected $typeId = 0;

  /**
   * The image's total upvotes.
   *
   * @var integer
   */
  public $upvotes;

  /**
   * The image's unique user's ID who uploaded this image.
   *
   * @var integer
   */
  public $userId;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new movie poster image.
   *
   * @global \MovLib\Data\I18n $i18n
   * @param integer $movieId
   *   The unique movie ID this poster belongs to.
   * @param string $displayTitleWithYear
   *   The display title (with year) of the movie this image belongs to.
   * @param null|integer $imageId [optional]
   *   The image ID to load, if none is given (default) no image is loaded.
   */
  public function __construct($movieId, $displayTitleWithYear, $imageId = null) {
    global $i18n;
    if ($imageId) {
      $stmt = $this->query(
        "SELECT
          `id`,
          `user_id`,
          `license_id`,
          `country_id`,
          `width`,
          `height`,
          `size`,
          `extension`,
          UNIX_TIMESTAMP(`changed`),
          UNIX_TIMESTAMP(`created`),
          `upvotes`,
          COLUMN_GET(`dyn_descriptions`, ? AS BINARY),
          `source`,
          `styles`
        FROM `movies_images`
        WHERE `id` = ? AND `movie_id` = ? AND `type_id` = ?
        LIMIT 1",
        "sdd",
        [ $i18n->languageCode, $imageId, $movieId, $this->typeId ]
      );
      $stmt->bind_result(
        $this->id,
        $this->userId,
        $this->licenseId,
        $this->countryId,
        $this->imageWidth,
        $this->imageHeight,
        $this->imageSize,
        $this->imageExtension,
        $this->imageChanged,
        $this->imageCreated,
        $this->upvotes,
        $this->description,
        $this->source,
        $this->imageStyles
      );
      if (!$stmt->fetch()) {
        throw new ImageException("Couldn't find image with ID '{$imageId}' of type '{$this->typeId}' for movie with ID '{$movieId}'.");
      }
      $stmt->close();
    }
    else {
      $stmt     = $this->query(
        "SELECT IFNULL(MAX(`id`), 1) FROM `movies_images` WHERE `movie_id` = ? AND `type_id` = ? LIMIT 1",
        "di",
        [ $movieId, $this->typeId ]
      );
      $this->id = $stmt->get_result()->fetch_row()[0];
      $stmt->close();
    }
    $this->alternativeText = $i18n->t("Poster for {0}.", [ $displayTitleWithYear ]);
    $this->imageDirectory .= "/{$movieId}/poster";
    $this->imageExists     = (boolean) $this->imageExists;
    $this->imageName       = $this->id;
    $this->movieId         = $movieId;
    if ($this->imageExists === true) {
      $this->route = $i18n->r("/movie/{0}/poster/{1}", [ $movieId, $this->id ]);
    }
    else {
      $this->route = $i18n->t("/movie/{0}/posters/upload");
    }
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * @inheritdoc
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Data\User\Session $session
   */
  protected function generateImageStyles($source) {
    global $i18n, $session;

    // Generate the various image's styles and always go from best quality down to worst quality.
    $span08 = $this->convertImage($source, self::IMAGE_STYLE_SPAN_08);
    $span02 = $this->convertImage($span08, self::IMAGE_STYLE_SPAN_02);
    $this->convertImage($span02, self::IMAGE_STYLE_SPAN_01);

    // Update the record with the new data if this is an update.
    if ($this->imageExists === true) {
      $this->query(
        "UPDATE `movies_images` SET
          `license_id`       = ?,
          `country_id`       = ?,
          `width`            = ?,
          `height`           = ?,
          `size`             = ?,
          `extension`        = ?,
          `dyn_descriptions` = COLUMN_ADD(`dyn_descriptions`, ?, ?),
          `source`           = ?,
          `styles`           = ?
        WHERE `id` = ? AND `movie_id` = ? AND `type_id` = ?",
        "ddi",
        [ $this->id, $this->movieId, $this->typeId ]
      );
    }
    // If this is a new upload insert the record and create the new details route for this upload.
    else {
      $this->query(
        "INSERT INTO `movies_images` SET
          `id`               = ?,
          `movie_id`         = ?,
          `type_id`          = ?,
          `user_id`          = ?,
          `license_id`       = ?,
          `country_id`       = ?,
          `width`            = ?,
          `height`           = ?,
          `size`             = ?,
          `extension`        = ?,
          `dyn_descriptions` = COLUMN_CREATE(?, ?),
          `source`           = ?,
          `styles`           = ?,
          `upvotes`          = 0",
        "ddidiiiiisssss",
        [
          $this->id,
          $this->movieId,
          $this->typeId,
          $session->userId,
          $this->licenseId,
          $this->countryId,
          $this->imageWidth,
          $this->imageHeight,
          $this->imageSize,
          $this->imageExtension,
          $i18n->languageCode,
          $this->description,
          $this->source,
          serialize($this->imageStyles),
        ]
      );
      $this->route = $i18n->r("/movie/{0}/poster/{1}", [ $this->movieId, $this->id ]);
    }

    return $this;
  }

  /**
   * @inheritdoc
   */
  public function getImageStyle($style = self::IMAGE_STYLE_SPAN_02) {
    if (!isset($this->imageStyles[$style])) {
      $this->imageStyles = unserialize($this->imageStyles);
    }
    if (!isset($this->imageStylesCache[$style])) {
      $this->imageStylesCache[$style] = new Style(
        $this->alternativeText,
        $this->getImageURL($style),
        $this->imageStyles[$style]["width"],
        $this->imageStyles[$style]["height"],
        $this->route
      );
    }
    return $this->imageStylesCache[$style];
  }

}