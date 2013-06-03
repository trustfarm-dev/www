<?php

/* !
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
namespace MovLib\View\HTML\User;

use \MovLib\Model\UserModel;
use \MovLib\View\HTML\AbstractFormView;

/**
 * Description of UserResetPasswordView
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class UserResetPasswordView extends AbstractFormView {

  /**
   * The user presenter controlling this view.
   *
   * @var \MovLib\Presenter\UserPresenter
   */
  protected $presenter;

  /**
   * {@inheritdoc}
   */
  public function __construct($presenter) {
    global $i18n;
    parent::__construct($presenter, $i18n->t("Reset password"));
    $this->addStylesheet("/assets/css/modules/user.css");
    $this->attributes = [ "class" => "span span--0" ];
  }

  /**
   * {@inheritdoc}
   */
  public function getRenderedFormContent() {
    global $i18n;
    return
      "<div class='page-header--no-border'><h1>{$this->title}</h1></div>" .
      "<p>" .
        "<label for='email'>{$i18n->t("Email address")}</label>" .
        "<input autofocus class='input input-text input--block-level' id='email' maxlength='" . UserModel::MAIL_MAX_LENGTH . "' name='email' placeholder='{$i18n->t("Enter your email address")}' required role='textbox' tabindex='{$this->getTabindex()}' title='{$i18n->t("Plase enter the email address with which you registered your account.")}' type='email' value='{$this->presenter->getPostValue("email")}'>" .
      "</p>" .
      "<p><button class='button button--success button--large input input-submit' name='submitted' tabindex='{$this->getTabindex()}' title='{$i18n->t("Fill in the email address you registered with and we will generate a new secure password for you.")}' type='submit'>{$i18n->t("Reset password")}</button></p>"
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function getRenderedView() {
    return $this->getRenderedViewWithoutFooter("row span--3");
  }

}
