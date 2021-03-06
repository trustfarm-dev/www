<?php

/*!
 * This file is part of {@link https://github.com/MovLib MovLib}.
 *
 * Copyright © 2014-present {@link https://movlib.org/ MovLib}.
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
namespace MovLib\Presentation\Person;

use \MovLib\Data\Person\Person;
use \MovLib\Partial\Form;
use \MovLib\Partial\FormElement\InputDateSeparate;
use \MovLib\Partial\FormElement\InputSex;
use \MovLib\Partial\FormElement\InputText;
use \MovLib\Partial\FormElement\InputWikipedia;
use \MovLib\Partial\FormElement\TextareaHTMLExtended;
use \MovLib\Partial\FormElement\TextareaLineURLArray;

/**
 * Allows editing of a person's information.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Edit extends \MovLib\Presentation\AbstractEditPresenter {

  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "Edit";
  // @codingStandardsIgnoreEnd

  /**
   * {@inheritdoc}
   */
  public function init() {
    $this->entity = new Person($this->container, $_SERVER["PERSON_ID"]);
    $pageTitle    = $this->intl->t("Edit {0}", [ $this->entity->name ]);
    return $this
      ->initPage($pageTitle, $pageTitle, $this->intl->t("Edit"))
      ->initEdit($this->entity, $this->intl->t("Persons"))
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function getContent() {
    $form = (new Form($this->container))
      ->addHiddenElement("revision_id", $this->entity->changed->formatInteger())
      ->addElement(new InputText($this->container, "name", $this->intl->t("Name"), $this->entity->name, [
        "placeholder" => $this->intl->t("Enter the persons’s name."),
        "autofocus"   => true,
        "required"    => true,
      ]))
      ->addElement(new InputSex($this->container, "sex", $this->intl->t("Sex"), $this->entity->sex))
      ->addElement(new InputText($this->container, "born-name", $this->intl->t("Birth Name"), $this->entity->bornName, [
        "placeholder" => $this->intl->t("Enter the persons’s birth name."),
      ]))
      ->addElement(new InputDateSeparate($this->container, "birth-date", $this->intl->t("Birthdate"), $this->entity->birthDate))
      ->addElement(new InputDateSeparate($this->container, "death-date", $this->intl->t("Deathdate"), $this->entity->deathDate))
      ->addElement(new TextareaHTMLExtended($this->container, "biography", $this->intl->t("Biography"), $this->entity->biography, [
        "data-allow-external" => "true",
        "placeholder"         => $this->intl->t("Describe the person."),
      ]))
      ->addElement(new InputWikipedia($this->container, "wikipedia", $this->intl->t("Wikipedia"), $this->entity->wikipedia, [
        "placeholder"         => "http://{$this->intl->code}.wikipedia.org/…",
        "data-allow-external" => "true",
      ]))
      ->addElement(new TextareaLineURLArray($this->container, "links", $this->intl->t("Weblinks (line by line)"), $this->entity->links, [
        "placeholder" => $this->intl->t("Enter the persons’s related weblinks, line by line."),
      ]))
      ->addAction($this->intl->t("Update"), [ "class" => "btn btn-large btn-success" ])
      ->init([ $this, "valid" ])
    ;
    return
      $form->open() .
      $form->elements["name"] .
      $form->elements["born-name"] .
      $form->elements["sex"] .
      "<div class='r'><div class='s s5'>{$form->elements["birth-date"]}</div><div class='s s5'>{$form->elements["death-date"]}</div></div>" .
      $form->elements["biography"] .
      $form->elements["wikipedia"] .
      $form->elements["links"] .
      $form->close()
    ;
  }

}
