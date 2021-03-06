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
namespace MovLib\Presentation\SystemPage;

use \MovLib\Data\SystemPage\SystemPage;

/**
 * Defines the base class for all system page presenters.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
abstract class AbstractShow extends \MovLib\Presentation\AbstractPresenter {
  use \MovLib\Partial\SidebarTrait;
  use \MovLib\Presentation\SystemPage\SystemPageTrait;

  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "Show";
  // @codingStandardsIgnoreEnd

  /**
   * The system page to present.
   *
   * @var \MovLib\Data\SystemPage
   */
  protected $entity;

  /**
   * Initialize the system page.
   *
   * @param integer $id
   *   The system page's unique identifier.
   * @param string $headTitle
   *   The presenter's <code><title></code> title.
   * @param string $pageTitle [optional]
   *   The presenter's <code><h1></code> title.
   * @param string $breadcrumbTitle [optional]
   *   The presenter's title for the breadcrumb's entry of the current presentation.
   */
  public function initSystemPage($id, $headTitle, $pageTitle = null, $breadcrumbTitle = null) {
    $this->entity = new SystemPage($this->container, $id);
    return $this
      ->initPage($headTitle, $pageTitle, $breadcrumbTitle)
      ->initLanguageLinks($this->entity->route->route)
      ->sidebarInit($this->getSidebarItems())
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function getContent() {
    return "<div property='mainContentOfPage'>{$this->htmlDecode($this->entity->text)}</div>";
  }

}
