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
namespace MovLib\Tool;

use \MovLib\Tool\Configuration;
use \MovLib\Tool\Console\Command\Production\FixPermissions;
use \Composer\Script\Event;

/**
 * React on composer execution.
 *
 * Note that any exceptions are catched by composer and the program is automatically aborted.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Composer {
  use \MovLib\Data\TraitUtilities;


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The fired composer event.
   *
   * @var \Composer\Script\Event
   */
  protected $event;

  /**
   * Absolute path to composer packages (without trailing slash).
   *
   * @var string
   */
  protected $vendorPath;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new Composer object.
   *
   * @global \MovLib\Tool\Configuration $config
   * @param \Composer\Script\Event $event
   *   The fired composer event.
   */
  public function __construct(Event $event) {
    global $config;
    if (!$config) {
      $config = new Configuration();
    }
    $this->event      = $event;
    $this->vendorPath = "{$config->documentRoot}/{$event->getComposer()->getConfig()->get("vendor-dir")}";
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * Create symbolic link for apigen executable.
   *
   * @global \MovLib\Tool\Configuration $config
   * @return this
   */
  public function apigen() {
    global $config;
    $this->symlink("{$this->vendorPath}/bin/apigen.php", "{$config->usrBinaryPath}/apigen");
    return $this;
  }

  /**
   * Fix vendor directory permissions.
   *
   * @return this
   */
  public function fixPermissions() {
    (new FixPermissions())->fixPermissions($this->vendorPath);
    return $this;
  }

  /**
   * Install phpMyAdmin.
   *
   * @global \MovLib\Tool\Configuration $config
   * @global \MovLib\Tool\Database $db
   * @param string $fullName
   *   The packages full name including the name and slash.
   * @return this
   */
  public function phpmyadmin($fullName) {
    global $config, $db;
    symlink("{$config->documentRoot}/conf/phpmyadmin/config.inc.php", "{$this->vendorPath}/{$fullName}/config.inc.php");
    $db->queries(file_get_contents("{$this->vendorPath}/{$fullName}/examples/create_tables.sql"));
    return $this;
  }

  /**
   * Create symbolic link for phpunit executable.
   *
   * @global \MovLib\Tool\Configuration $config
   * @return this
   */
  public function phpunit() {
    global $config;
    $this->symlink("{$this->vendorPath}/bin/phpunit", "{$config->usrBinaryPath}/phpunit");
    return $this;
  }

  /**
   * Install VisualPHPUnit.
   *
   * @global \MovLib\Tool\Configuration $config
   * @param string $fullName
   *   The packages full name including the name and slash.
   * @return this
   */
  public function visualphpunit($fullName) {
    global $config;
    $path = "{$this->vendorPath}/{$fullName}/app";

    // The symbolic link is for correct routing.
    symlink("{$path}/public", "{$path}/visualphpunit");

    // Replace some vendor files with custom ones.
    copy("{$config->documentRoot}/conf/visualphpunit/index.php", "{$path}/public/index.php");
    copy("{$config->documentRoot}/conf/visualphpunit/bootstrap.php", "{$path}/config/bootstrap.php");
    return $this;
  }


  // ------------------------------------------------------------------------------------------------------------------- Static Methods


  /**
   * Automatically called after `composer install` execution.
   *
   * @global \MovLib\Tool\Configuration $config
   * @param \Composer\Script\Event $event
   *   The event fired by composer.
   */
  public static function postInstall(Event $event) {
    (new Composer($event))->fixPermissions();
  }

  /**
   * Automatically called after composer installed a package.
   *
   * @param \Composer\Script\Event $event
   *   The event fired by composer.
   */
  public static function postPackageInstall(Event $event) {
    $operation = $event->getOperation();
    if (method_exists($operation, "getPackage")) {
      $composer         = new Composer($event);
      $fullPackageName  = $operation->getPackage()->getName();
      $packageName      = substr($fullPackageName, strrpos($fullPackageName, "/") + 1);
      if (method_exists($composer, $packageName)) {
        $composer->{$packageName}($fullPackageName);
      }
    }
  }

  /**
   * Automatically called after composer updated a package.
   *
   * @param \Composer\Script\Event $event
   *   The event fired by composer.
   */
  public static function postPackageUpdate(Event $event) {
    self::postPackageInstall($event);
  }

  /**
   * Automatically called after `composer update` execution.
   *
   * @param \Composer\Script\Event $event
   *   The event fired by composer.
   */
  public static function postUpdate(Event $event) {
    self::postInstall($event);
  }

}
