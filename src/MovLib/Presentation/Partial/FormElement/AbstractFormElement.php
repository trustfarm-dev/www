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
namespace MovLib\Presentation\Partial\FormElement;

use \MovLib\Presentation\Partial\Alert;
use \MovLib\Presentation\Partial\Help;

/**
 * Base class for any form element.
 *
 * @link https://developer.mozilla.org/en-US/docs/tag/Forms
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
abstract class AbstractFormElement extends \MovLib\Presentation\AbstractBase {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The form element's attributes.
   *
   * The following attributes are always set:
   * <ul>
   *   <li><code>"id"</code> is set to <var>AbstractFormElement::$id</var></li>
   *   <li><code>"name"</code> is set to <var>AbstractFormElement::$id</var></li>
   * </ul>
   *
   * @var array
   */
  public $attributes;

  /**
   * The form element's help.
   *
   * @see AbstractFormElement::setHelp
   * @var null|\MovLib\Presentation\Partial\Help
   */
  protected $help;

  /**
   * The form element's global identifier.
   *
   * The ID is used for the ID and name attributes of the element.
   *
   * @var string
   */
  public $id;

  /**
   * The form element's label content.
   *
   * @var string
   */
  protected $label;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new form element.
   *
   * @param string $id
   *   The form element's unique global identifier.
   * @param string $label
   *   The form element's translated label text.
   * @param array $attributes [optional]
   *   The form element's attributes array.
   */
  public function __construct($id, $label, array $attributes = null) {
    $this->attributes = $attributes;
    $this->id         = $this->attributes["id"] = $this->attributes["name"] = $id;
    $this->label      = $label;
  }

  /**
   * Get string representation of this form element.
   *
   * @global \MovLib\Data\I18n $i18n
   * @return string
   *   String representation of this form element.
   */
  public function __toString() {
    global $i18n;
    try {
      return $this->render();
    }
    catch (\Exception $e) {
      return (string) new Alert("<pre>{$e}</pre>", $i18n->t("Error Rendering Element"), Alert::SEVERITY_ERROR);
    }
  }


  // ------------------------------------------------------------------------------------------------------------------- Abstract Methods


  /**
   * Get the string representation of the form element.
   *
   * @return string
   *   The string representation of the form element.
   */
  protected abstract function render();

  /**
   * Validate the user submitted data.
   *
   * @global \MovLib\Data\I18n $i18n
   * @return this
   * @throws \MovLib\Exception\ValidationException
   */
  public abstract function validate();


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * Filter input variable.
   *
   * @see filter_var()
   * @see filter_input()
   * @param string $name
   *   The name of the variable to get.
   * @param integer $filter
   *   The identifier of the filter to apply. The {@link http://php.net/manual/filter.filters.php types of filters}
   *   manual page lists the available filters. Defaults to <var>FILTER_UNSAFE_RAW</var>.
   * @param mixed $options
   *   Associative array of options or bitwise disjunction of flags. If filter accepts options, flags can be provided
   *   in <code>"flags"</code> field of array. Defaults to no options.
   * @param array $type
   *   The input type to filter, defaults to <var>$_POST</var>.
   * @return mixed
   *   Value of the requested variable on success, <code>FALSE</code> if the filter fails, or <code>NULL</code> if the
   *   <var>$name</var> variable is not set. If the flag <var>FILTER_NULL_ON_FAILURE</var> is used, it returns
   *   <code>NULL</code> if the fitler fails.
   */
  protected function filterInput($name, $filter = FILTER_UNSAFE_RAW, $options = null, $type = null) {
    if (!$type) {
      $type = $_POST;
    }
    if (isset($type[$name])) {
      return filter_var($type[$name], $filter, $options);
    }
  }

  /**
   * Mark this form element as invalid.
   *
   * There is no attribute <code>invalid</code> in HTML, browsers apply the CSS pseudo-class <code>:invalid</code> on
   * elements that fail their own validations. We use the ARIA attribute <code>aria-invalid</code> and set it to
   * <code>true</code> to achieve the same goal. For easy CSS selection the class <code>invalid</code> is applied as
   * well.
   *
   * @return this;
   */
  public function invalid() {
    $this->addClass("invalid", $this->attributes);
    $this->attributes["aria-invalid"] = "true";
    return $this;
  }

  /**
   * Set the help for this form element.
   *
   * @param string $text
   *   The form element's translated help text, defaults to no help text.
   * @param boolean $popup [optional]
   *   Whether the help should be displayed as popup or not, defaults to display as popup.
   * @return this
   */
  public function setHelp($text, $popup = true) {
    $this->attributes["aria-describedby"] = "{$this->id}-help";
    $this->help                           = new Help($text, $this->id, $popup);
    return $this;
  }

}
