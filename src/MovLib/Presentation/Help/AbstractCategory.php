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
namespace MovLib\Presentation\Help;

use \MovLib\Data\Help\ArticleSet;
use \MovLib\Data\Help\SubCategorySet;

/**
 * Defines the abstract help category index presentation.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
abstract class AbstractCategory extends \MovLib\Presentation\AbstractIndexPresenter {

  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "AbstractCategory";
  // @codingStandardsIgnoreEnd


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The articles to present.
   *
   * @var \MovLib\Data\Help\Article\ArticleSet
   */
  protected $articleSet;

  /**
   * The category to present.
   *
   * @var \MovLib\Data\Help\Category\Category
   */
  protected $category;


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * {@inheritdoc}
   * @param \MovLib\Data\Help\Category $category
   *   The help category to show.
   */
  public function initCategory(\MovLib\Data\Help\Category $category) {
    $this->category    = $category;
    $this->articleSet  = new ArticleSet($this->container);
    $this->set         = new SubCategorySet($this->container);

    $this->initPage($this->category->title);
    $this->initBreadcrumb([
      [ $this->intl->r("/help"), $this->intl->t("Help") ]
    ]);
    $this->initLanguageLinks($this->category->route->route);

    $sidebarItems = [ [ $this->category->route, "{$this->category->title} <span class='fr'>{$this->intl->formatInteger($this->category->articleCount)}</span>", [ "class" => "ico {$this->category->icon} separator" ] ] ];
    foreach ($this->set->getAllBelongingToCategory($this->category->id) as $id => $entity) {
      $sidebarItems[] = [ $entity->route, "{$entity->title} <span class='fr'>{$this->intl->formatInteger($entity->articleCount)}</span>", [ "class" => "ico {$entity->icon}" ] ];
    }
    $this->sidebarInit($sidebarItems);

    $this->headingBefore = "<a class='btn btn-large btn-success fr' href='{$this->category->r("/create")}'>{$this->intl->t("Create Help Article")}</a>";

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContent() {
    $items = null;
    foreach ($this->articleSet->getAllBelongingToCategory($this->category->id) as $id => $entity) {
      $items .= $this->formatListingItem($entity, $id);
    }
    return isset($items)? $this->getListing($items) : $this->getNoItemsContent();
  }

  /**
   * {@inheritdoc}
   */
  protected function formatListingItem(\MovLib\Core\Entity\EntityInterface $entity, $delta) {
    return
      "<li class='hover-item r'>" .
        "<article>" .
          "<div class='s s10'>" .
            "<h2 class='para'><a href='{$entity->route}' property='url'><span property='name'>{$entity->title}</span></a></h2>" .
          "</div>" .
        "</article>" .
      "</li>"
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function getNoItemsContent() {
    return $this->calloutInfo(
      $this->intl->t("We couldn’t find any articles in this category."),
      $this->intl->t("No Help In This Category")
    );
  }

}
