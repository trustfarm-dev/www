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
namespace MovLib\Data\Rating;

use \MovLib\Core\Database\Database;

/**
 * Defines the default implementation for the rating interface.
 *
 * @see \MovLib\Data\RatingInterface
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
trait RatingTrait {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The entity's Bayes rating.
   *
   * @var null|float
   */
  public $ratingBayes;

  /**
   * The entity's mean rating.
   *
   * @var float
   */
  public $ratingMean;

  /**
   * The entity's global rank.
   *
   * @var null|integer
   */
  public $ratingRank;

  /**
   * The entity's votes.
   *
   * @var null|integer
   */
  public $ratingVotes;


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * Rate this entity.
   *
   * @todo Update Baye's rating.
   *
   * @param integer $rating
   *   A valid rating between 1 and 5.
   * @param integer $userId
   *   The user to rate for.
   * @param integer|null $userRating
   *   The user's current rating for this entity.
   * @return this
   */
  public function rate($rating, $userId, $userRating) {
    $connection = Database::getConnection();

    $connection->autocommit(false);
    try {
      // User hans't voted for this entity yet.
      if ($userRating === null) {
        $connection->real_query("INSERT INTO `{$this::$tableName}_ratings` SET `rating` = {$rating}, `{$this->set->singularKey}_id` = {$this->id}, `user_id` = {$userId}");
        ++$this->ratingVotes;
      }
      // User already voted for this entity.
      else {
        $connection->real_query("UPDATE `{$this::$tableName}_ratings` SET `rating` = {$rating} WHERE `{$this->set->singularKey}_id` = {$this->id} AND `user_id` = {$userId}");
      }

      // Retrieve the new cummulated ratings from the database.
      $cummulatedRating = (float) $connection->query(
        "SELECT SUM(`rating`) FROM `{$this::$tableName}_ratings` WHERE `{$this->set->singularKey}_id` = {$this->id} FOR UPDATE"
      )->fetch_all()[0][0];

      // Calculate the entity's new ratings.
      $this->ratingMean  = round($cummulatedRating / $this->ratingVotes, 1);
      //$this->ratingBayes = round();

      // Update the entity's rating statistics cache.
      $connection->real_query(
        "UPDATE `{$this::$tableName}` SET `mean_rating` = {$this->ratingMean}, `votes` = {$this->ratingVotes} WHERE `id` = {$this->id}"
      );

      $connection->commit();
    }
    catch (\Exception $e) {
      $connection->rollback();
      throw $e;
    }
    finally {
      $connection->autocommit(true);
    }

    return $this;
  }

}
