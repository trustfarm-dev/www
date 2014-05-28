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
namespace MovLib\Core\Entity;

use \MovLib\Component\DateTime;
use \MovLib\Core\Routing\Route;

/**
 * Defines the abstract entity base class that provides a default implementation for all entity classes.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
abstract class AbstractEntity implements \MovLib\Core\Entity\EntityInterface, \MovLib\Core\Routing\RoutingInterface {
  use \MovLib\Core\Entity\EntityTrait;
  use \MovLib\Core\Routing\RoutingTrait;


  // ------------------------------------------------------------------------------------------------------------------- Constants


  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "AbstractEntity";
  // @codingStandardsIgnoreEnd


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The entity's changed date and time.
   *
   * @var \MovLib\Component\DateTime
   */
  public $changed;

  /**
   * The entity's creation date and time.
   *
   * @var \MovLib\Component\DateTime
   */
  public $created;

  /**
   * The entity's deletion status.
   *
   * @var boolean
   */
  public $deleted = false;

  /**
   * The entity's unique identifier.
   *
   * @var integer
   */
  public $id;

  /**
   * The entity's translated lemma.
   *
   * @var string
   */
  public $lemma;

  /**
   * The entity's parent entities and sets.
   *
   * <b>NOTE</b><br>
   * Key zero always points to {@see AbstractEntity::$set}.
   *
   * @var array
   */
  public $parents = [];

  /**
   * The entity's set.
   *
   * @var \MovLib\Core\Entity\EntitySetInterface|\MovLib\Core\Entity\AbstractEntitySet
   */
  public $set;

  /**
   * The entity's Wikipedia link in the current locale.
   *
   * @var null|string
   */
  public $wikipedia;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new entity.
   *
   * @param \MovLib\Core\Container $container
   *   {@inheritdoc}
   * @param array $values [optional]
   *   An array of values to set, keyed by property name, defaults to <code>NULL</code>.
   */
  public function __construct(\MovLib\Core\Container $container, array $values = null) {
    // Export the container and intl to their own properties. We can't abstract this into the trait because the set is
    // extending \ArrayObject.
    $this->container = $container;
    $this->intl      = $container->intl;

    // Build the set's class and instantiate it, we export the set into the set property as well as to the first index
    // in the parents array.
    $setClass = static::class . "Set";
    $this->parents[] = $this->set = new $setClass($container);

    // We can almost always reuse the table name from our set.
    if (!static::$tableName) {
      static::$tableName = $setClass::$tableName;
    }

    // We'll try to build the bundle from the namespace if no custom bundle was set. This should work in almost all
    // cases.
    if (!$this->bundle) {
      $this->bundle = explode("\\", static::class);
      array_pop($this->bundle);
      $this->bundle = end($this->bundle);
    }

    // We can always abstract the translation of the bundle. Note that we don't call out own bundle title's translation
    // method because its only a proxy method and costs us performance.
    $this->bundleTitle = $this->intl->tp(1, $this->set->bundleTitle, $this->bundle);

    // Always call init if we either have an id or values to export.
    if ($this->id || $values) {
      $this->init($values);
    }
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * {@inheritdoc}
   */
  final public function bundleTitle($locale) {
    return $this->intl->tp(-1, $this->set->bundleTitle, $this->bundleTitle, null, $locale);
  }

  /**
   * {@inheritdoc}
   */
  public function init(array $values = null) {
    // Export all values from the array if there are any.
    if ($values) {
      foreach ($values as $key => $value) {
        $this->$key = $value;
      }
    }

    // @devStart
    // @codeCoverageIgnoreStart
    // We have to have an id at this point, either via the values array or set during construction.
    assert(isset($this->id), "Seems like this object was incorrectly instantiated, you always have to set the id.");
    // @codeCoverageIgnoreEnd
    // @devEnd

    // Instantiate and cast everything that is shared among all entities.
    $this->changed = new DateTime($this->changed);
    $this->created = new DateTime($this->created);
    $this->deleted = (boolean) $this->deleted;

    // Allow concrete entity's to set a custom route, especially if they are sub entities.
    //
    // @todo Maybe we can abstract this by using the parents array?
    if (!$this->route) {
      $routeKey    = strtolower(static::name);
      $this->route = new Route($this->intl, "/{$routeKey}/{0}", [ "args" => $this->id ]);
    }

    return $this;
  }

}
