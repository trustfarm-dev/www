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
namespace MovLib\Presentation\Event;

use \MovLib\Data\Award\Award;
use \MovLib\Data\Event\Event;
use \MovLib\Partial\Date;

/**
 * Defines the event show presentation.
 *
 * @link http://schema.org/Event
 * @link http://www.google.com/webmasters/tools/richsnippets?q=https://en.movlib.org/event/{id}
 * @link http://www.w3.org/2012/pyRdfa/extract?validate=yes&uri=https://en.movlib.org/event/{id}
 * @link http://validator.w3.org/check?uri=https://en.movlib.org/event/{id}
 * @link http://gsnedders.html5.org/outliner/process.py?url=https://en.movlib.org/event/{id}
 *
 * @property \MovLib\Data\Event\Event $entity
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
final class Show extends \MovLib\Presentation\AbstractShowPresenter {

  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "Show";
  // @codingStandardsIgnoreEnd
  use \MovLib\Presentation\Event\EventTrait;

  /**
   * {@inheritdoc}
   */
  public function init() {
    $this->entity = new Event($this->container, $_SERVER["EVENT_ID"]);
    $this->entity->award && $this->entity->award = new Award($this->container, $this->entity->award);
    $this
      ->initPage($this->entity->name)
      ->initShow($this->entity, $this->intl->t("Events"), "Event", null, $this->getSidebarItems())
    ;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getContent() {
    $this->entity->links     && $this->infoboxAdd($this->intl->t("Sites"), $this->formatWeblinks($this->entity->links));
    $this->entity->award     && $this->infoboxAdd($this->intl->t("Award"), "<a href='{$this->intl->r("/award/{0}", $this->entity->award->id)}'>{$this->entity->award->name}</a>");
    $this->entity->startDate && $this->infoboxAdd($this->intl->t("Start Date"), (new Date($this->intl, $this))->format($this->entity->startDate));
    $this->entity->endDate   && $this->infoboxAdd($this->intl->t("End Date"), (new Date($this->intl, $this))->format($this->entity->endDate));

    $this->entity->description && $this->sectionAdd($this->intl->t("Description"), $this->entity->description);
    $this->entity->aliases     && $this->sectionAdd($this->intl->t("Also Known As"), $this->formatAliases($this->entity->aliases), false);
    if ($this->sections) {
      return $this->sections;
    }

    return $this->callout(
      $this->intl->t("Would you like to {0}add additional information{1}?", [ "<a href='{$this->intl->r("/event/{0}/edit", $this->entity->id)}'>", "</a>" ]),
      $this->intl->t("{sitename} doesn’t have further details about this event.", [ "sitename" => $this->config->sitename ])
    );
  }

}
