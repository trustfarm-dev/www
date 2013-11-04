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
namespace MovLib\Presentation\Email;

/**
 * Base email implementation.
 * 
 * All email templates have to extend this class in order to work with the mailer system.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
abstract class AbstractEmail extends \MovLib\Presentation\AbstractBase {


  // ------------------------------------------------------------------------------------------------------------------- Constants


  /**
   * Email priority high.
   *
   * @var int
   */
  const PRIORITY_HIGH = 1;
  
  /**
   * Email priority normal.
   * 
   * @var int
   */
  const PRIORITY_NORMAL = 3;


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The email's priority.
   *
   * @var int
   */
  public $priority = self::PRIORITY_NORMAL;
  
  /**
   * The email's recipient.
   *
   * @var string
   */
  public $recipient;

  /**
   * The email's subject.
   *
   * @var string
   */
  public $subject;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new email.
   *
   * @param string $recipient
   *   The email's recipient email address, must comply at least with {@link http://www.ietf.org/rfc/rfc2822.txt RFC 2822}.
   * @param string $subject
   *   The email's subject, must comply with {@link http://www.ietf.org/rfc/rfc2047.txt RFC 2047}.
   */
  public function __construct($recipient, $subject) {
    $this->recipient = $recipient;
    $this->subject   = $subject;
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * Get the email's translated HTML message.
   *
   * @return string
   *   The email's translated HTML message.
   */
  public abstract function getHTML();

  /**
   * Get the email's translated plain text message.
   *
   * @return string
   *   The email's translated plain text message.
   */
  public abstract function getPlainText();

}
