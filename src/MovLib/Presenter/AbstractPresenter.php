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
namespace MovLib\Presenter;

use \MovLib\Entity\Language;

/**
 * Base class for any presenter.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
abstract class AbstractPresenter {

  /**
   * String buffer used to concatenate all the output and send the whole shebang at once.
   *
   * @var string
   */
  protected $output = '';

  /**
   * The currently active language.
   *
   * @var \MovLib\Entity\Language
   */
  protected $language;

  /**
   * Instantiate new presenter object.
   */
  public function __construct() {
    try {
      $this->language = new Language();
      $this->init();
    } catch (LanguageException $e) {
      $_SERVER['LANGUAGE_CODE'] = 'en';
      $this->output = new ErrorView(new Language(), $e);
    } catch (Exception $e) {
      $this->output = new ErrorView($this->language, $e);
    }
  }

  /**
   * Initialize the presenter and set the output.
   */
  protected abstract function init();

  /**
   * Get the whole output of this presenter.
   *
   * @return string
   */
  public final function getOutput() {
    return $this->output;
  }

  /**
   * Magic function that is automatically called if somebody tries to echo the object itself.
   *
   * @return string
   */
  public function __toString() {
    if (xdebug_is_enabled()) {
      ob_start();
      var_dump($this);
      return ob_get_clean();
    }
    else {
      return '<pre>' . print_r($this, true) . '</pre>';
    }
  }

}
