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
namespace MovLib\Presentation\Email;

/**
 * Template for emails that are sent to the MovLib developers.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class MovDevEmail extends \MovLib\Presentation\Email\AbstractBase {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The message in HTML format.
   *
   * @var string
   */
  public $html;

  /**
   * The message in plain text format.
   *
   * @var string
   */
  public $text;


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * Instantiate new email that will be sent to all devs.
   *
   * @param string $subject
   *   The text to appear in the email subject.
   * @param string $text
   *   The message in plain text format.
   * @param string $html
   *   The message in HTML format.
   */
  public function __construct($subject, $text, $html) {
    $this->recipient = $GLOBALS["movlib"]["developer_mailinglist"];
    $this->subject = $subject;
    $this->text = $text;
    $this->html = $html;
  }

  /**
   * @inheritdoc
   */
  protected function getHtmlBody() {
    return "<p>Hi devs!</p>{$this->html}";
  }

  /**
   * @inheritdoc
   */
  protected function getPlainBody() {
    return "Hi devs!\n\n{$this->text}";
  }

}