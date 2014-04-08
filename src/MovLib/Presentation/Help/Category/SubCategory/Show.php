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
namespace MovLib\Presentation\Help\Category\SubCategory;

use \MovLib\Data\Help\Article;

/**
 * Presentation of a single article.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
final class Show extends \MovLib\Presentation\AbstractPresenter {
  use \MovLib\Partial\SidebarTrait;

  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The article to present.
   *
   * @var \MovLib\Data\Help\Article
   */
  protected $article;


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * {@inheritdoc}
   */
  public function init() {
    $this->article     = new Article($this->diContainerHTTP, $_SERVER["HELP_ARTICLE_ID"]);

    $this->initPage($this->article->title);
    $this->initBreadcrumb([
      [ $this->intl->r("/help"), $this->intl->t("Help") ],
      [ $this->intl->r($this->article->category->routeKey), $this->article->category->title ],
      [ $this->intl->r($this->article->subCategory->routeKey), $this->article->subCategory->title ]
    ]);
    $this->sidebarInit([]);
    $this->initLanguageLinks($this->article->routeKey, [ $this->article->id ]);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContent() {
    return $this->htmlDecode($this->article->text);
  }

}
