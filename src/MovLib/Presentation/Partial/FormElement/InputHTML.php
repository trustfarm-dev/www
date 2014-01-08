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

use \MovLib\Exception\ValidationException;

/**
 * HTML contenteditable text form element.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/Content_Editable
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class InputHTML extends \MovLib\Presentation\Partial\FormElement\AbstractFormElement {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * Associative array containing the allowed HTML tags.
   *
   * @var array
   */
  protected $allowedTags = [
    "a"      => "&lt;a&gt;",
    "b"      => "&lt;b&gt;",
    "br"     => "&lt;br&gt;",
    "em"     => "&lt;em&gt;",
    "i"      => "&lt;i&gt;",
    "p"      => "&lt;p&gt;",
    "strong" => "&lt;strong&gt;",
  ];

  /**
   * Whether external links are allowed or not for anchor elements.
   *
   * @var boolean
   */
  protected $allowExternalLinks = false;

  /**
   * Whether we are inside a <code><blockquote></code> or not.
   *
   * @var boolean
   */
  protected $blockquote = false;

  /**
   * Associative array containing all HTML tags that aren't allowed within a <code><blockquote></code>.
   *
   * @var array
   */
  protected $blockquoteDisallowedTags = [
    "blockquote" => false,
    "figure"     => false,
    "ul"         => false,
    "ol"         => false,
  ];

  /**
   * Associative array to identify empty HTML tags.
   *
   * @var array
   */
  protected $emptyTags = [
    "br"  => true,
    "img" => true,
  ];

  /**
   * Whether to insert last child and clear it or not.
   *
   * @var null|string
   */
  protected $insertLastChild;

  /**
   * Used for some elements that have a required last child element.
   *
   * @var null|\tidyNode
   */
  protected $lastChild;

  /**
   * The level within the HTML text DOM we are currently traversing.
   *
   * @var integer
   */
  protected $level = 0;

  /**
   * The list information array used to determine when and if to close a list.
   *
   * Format: <code>[ "tag" => "ol"|"ul", "level" => "$level of first list opening", "allowed_tags" => "backup of allowed tags" ]</code>
   *
   * @var boolean|array
   */
  protected $list = false;

  /**
   * Associative array to identify allowed user CSS classes.
   *
   * @var array
   */
  protected $userClasses = [
    "user-left"   => true,
    "user-center" => true,
    "user-right"  => true,
  ];

  /**
   * The text's HTML encoded content.
   *
   * @var null|string
   */
  public $value;

  /**
   * The text's HTML decoded content.
   *
   * @var null|string
   */
  protected $valueRaw;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new HTML form element.
   *
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Kernel $kernel
   * @param string $id
   *   The text's global identifier.
   * @param string $label
   *   The text's label text.
   * @param mixed $value [optional]
   *   The form element's value, defaults to <code>NULL</code> (no value).
   * @param array $attributes [optional]
   *   Additional attributes for the text, defaults to <code>NULL</code> (no additional attributes).
   * @param string $help [optional]
   *   The text's help text, defaults to <code>NULL</code> (no help text).
   * @param boolean $helpPopup
   *   Whether the help should be displayed as popup or not, defaults to <code>TRUE</code> (display as popup).
   */
  public function __construct($id, $label, $value = null, array $attributes = null, $help = null, $helpPopup = true) {
    global $i18n, $kernel;
    parent::__construct($id, $label, $attributes, $help, $helpPopup);
    // We don't need the JS, because we only use <textarea> for now. This will change when InputHTML is finished.
    //    $kernel->javascripts[]              = "InputHTML";
    $kernel->stylesheets[]              = "inputhtml";
    $this->attributes["aria-multiline"] = "true";

    if (!empty($_POST[$this->id])) {
      $this->value    = $kernel->htmlEncode($_POST[$this->id]);
      $this->valueRaw = $this->autoParagraph($_POST[$this->id]);
    }
    elseif ($value) {
      $this->value    = $value;
      $this->valueRaw = $kernel->htmlDecode($this->value);
    }
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * @inheritdoc
   */
  protected function render() {
    global $i18n;

    // Important note: The majority of the code is commented out for a purpose, it is NOT dead code.
    // We will need it again once the WYSIWYG editor works. For now we'll stick with a plain <textarea>.

    // Pretty print HTML, since we only use <textarea> for now. This will change when InputHTML is finished.
    $tidy = tidy_parse_string("<!doctype html><html><head><title>MovLib</title></head><body>{$this->autoParagraph($this->valueRaw)}</body></html>");
    $tidy->cleanRepair();
    if ($tidy->getStatus() === 2) {
      throw new \ErrorException;
    }
    $content = \Normalizer::normalize(tidy_get_output($tidy));
    $content = str_replace([ "<br>\n", "<br>", "<p>", "</p>" ], "", $content);

    // We need to alter the div attributes in order to make them valid for this kind of HTML element. The div element
    // also needs a class for easy identification via CSS and JS whilst the textarea doesn't need anything because the
    // tag is more than sufficient for identification.
    //    $iframeAttributes                    = $this->attributes;
    //    $iframeAttributes["contenteditable"] = "true";
    //    $iframeAttributes["role"]            = "textbox";
    //    $iframeAttributes["seamless"]        = "seamless";
    //    $iframeAttributes["tabindex"]        = 0;
    //    $this->addClass("content", $iframeAttributes);
    //
    //    // The name attribute is always present for the textarea but nonsense for our div.
    //    unset($iframeAttributes["id"], $iframeAttributes["name"]);
    //
    //    // The required attribute isn't allowed on our div element.
    //    if (($key = array_search("required", $iframeAttributes)) !== false) {
    //      unset($iframeAttributes[$key]);
    //    }

    // Use default placeholder text if none was provided.
    if (!isset($this->attributes["placeholder"])) {
      $this->attributes["placeholder"] = $i18n->t("Enter “{0}” text here …", [ $this->label ]);
    }
    //    // Unset the placeholder in the div attributes, since this attribute is not allowed by HTML standard.
    //    else {
    //      unset($iframeAttributes["placeholder"]);
    //    }
    //
    //    // We need to add the aria-labelledby attribute to the textarea, since it won't have a label.
    //    $this->attributes["aria-labelledby"] = "{$this->id}-legend";
    //
    //    // Build the editor based on allowed tags.
    //    $editor = null;
    //
    //    // Check if any heading is allowed (checking against level 6 is enough as it's always part of the party if any level
    //    // is allowed) and include the block level selector if we have any. Ommit if no headings are allowed.
    //    if (isset($this->allowedTags["h6"])) {
    //      $editor .= "<li data-handler='formatBlock' data-tag='p' href=''>{$i18n->t("Paragraph")}</li>";
    //      for ($i = 2; $i <= 6; ++$i) {
    //        if (isset($this->allowedTags["h{$i}"])) {
    //          $editor .= "<li data-handler='formatBlock' data-tag='h{$i}' href=''>{$i18n->t("Heading {0, number, integer}", [ $i ])}</li>";
    //        }
    //      }
    //      $editor = "<div class='btn formats' data-handler='formats'><span class='expander'>{$i18n->t("Paragraph")}</span><ul class='concealed no-list'>{$editor}</ul></div>";
    //    }
    //
    //    $external = $this->allowExternalLinks === true ? " external" : null;
    //    $editor .=
    //      // Add the font styles.
    //      "<span class='btn ico ico-bold' data-handler='formatInline' data-tag='bold'><span class='vh'>{$i18n->t("Bold")}</span></span>" .
    //      "<span class='btn ico ico-italic' data-handler='formatInline' data-tag='italic'><span class='vh'>{$i18n->t("Italic")}</span></span>" .
    //      // Add the alignment buttons.
    //      "<span class='btn ico ico-align-left' data-direction='left' data-handler='align'><span class='vh'>{$i18n->t("Align left")}</span></span>" .
    //      "<span class='btn ico ico-align-center' data-direction='center' data-handler='align'><span class='vh'>{$i18n->t("Align center")}</span></span>" .
    //      "<span class='btn ico ico-align-right' data-direction='right' data-handler='align'><span class='vh'>{$i18n->t("Align right")}</span></span>" .
    //      // Add the insert section according to configuration.
    //      "<span class='btn ico ico-link{$external}' data-handler='link'><span class='vh'>{$i18n->t("Insert link")}</span></span>" .
    //      "<span class='btn ico ico-unlink' data-handler='formatInline' data-tag='unlink'><span class='vh'>{$i18n->t("Unlink selection")}</span></span>"
    //    ;
    //
    //    if (isset($this->allowedTags["blockquote"])) {
    //      $editor .= "<span class='btn ico ico-quotation' data-handler='quotation'><span class='vh'>{$i18n->t("Insert quotation")}</span></span>";
    //    }
    //
    //    if (isset($this->allowedTags["figure"])) {
    //      $editor .= "<span class='btn ico ico-image' data-handler='image'><span class='vh'>{$i18n->t("Insert image")}</span></span>";
    //    }
    //
    //    // Add list section, if lists are allowed.
    //    if (isset($this->allowedTags["ul"])) {
    //      $editor .=
    //        "<span class='btn ico ico-ul' data-handler='list'><span class='vh'>{$i18n->t("Insert unordered list")}</span></span>" .
    //        "<span class='btn ico ico-ol' data-handler='list'><span class='vh'>{$i18n->t("Insert ordered list")}</span></span>" .
    //        "<span class='btn ico ico-indent-left' data-direction='left' data-handler='indent'><span class='vh'>{$i18n->t("Indent list item left")}</span></span>" .
    //        "<span class='btn ico ico-indent-right' data-direction='right' data-handler='indent'><span class='vh'>{$i18n->t("Indent list item right")}</span></span>"
    //      ;
    //    }

    //    return
    //      "{$this->help}<fieldset class='inputhtml'>" .
    //        // Set an id for the legend, since it labels our textarea.
    //        "<legend id='{$this->id}-legend'>{$this->label}</legend>" .
    //        // The jshidden class uses display:none to hide its elements, this means that these elements aren't part of the
    //        // DOM tree and aren't parsed by user agents.
    //        "<p class='jshidden'><textarea{$this->expandTagAttributes($this->attributes)}>{$this->valueRaw}</textarea></p>" .
    //        // Same situation above but for user agents with disabled JavaScript. The content for the editable div is copied
    //        // over from the textarea by the JS module. But we directly include the placeholder because it's very short.
    //         "<div class='editor nojshidden'>{$editor}<iframe{$this->expandTagAttributes($iframeAttributes)}></iframe></div>" .
    //      "</fieldset>"
    //    ;
    return "{$this->help}<p><label for='{$this->id}'>{$this->label}</label><textarea{$this->expandTagAttributes($this->attributes)}>{$content}</textarea></p>";
  }

  /**
   * Allow <code><blockquote></code> elements.
   *
   * @return $this
   */
  public function allowBlockqoutes() {
    $this->allowedTags["blockquote"] = "&lt;blockquote&gt;";
    return $this;
  }

  /**
   * Allow external links.
   *
   * @return $this
   */
  public function allowExternalLinks() {
    $this->allowExternalLinks = true;
    return $this;
  }

  /**
   * Allow <code><h$level></code> to <code><h6></code> headings.
   *
   * @param integer $level [optional]
   *   The starting level of the headings. Allowed values are <code>2</code> to <code>6</code>. Defaults to <code>3</code>.
   * @return $this
   */
  public function allowHeadings($level = 3) {
    for ($i = $level; $i <= 6; ++$i) {
      $this->allowedTags["h{$i}"] = "&lt;h{$i}&gt;";
    }
    return $this;
  }

  /**
   * Allow images.
   *
   * @return $this
   */
  public function allowImages() {
    $this->allowedTags["figure"] = "&lt;figure&gt;";
    return $this;
  }

  /**
   * Allow unordered and ordered lists.
   *
   * @return $this
   */
  public function allowLists() {
    $this->allowedTags["ul"] = "&lt;ul&gt;";
    $this->allowedTags["ol"] = "&lt;ol&gt;";
    return $this;
  }

  /**
   * @inheritdoc
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Kernel $kernel
   */
  public function validate() {
    global $i18n, $kernel;

    // Validate if we have input and throw an Exception if the field is required.
    if (empty($this->valueRaw)) {
      if (in_array("required", $this->attributes)) {
        throw new ValidationException($i18n->t("“{label}” is mandatory.", [ "label" => $this->label ]));
      }
      $this->value = null;
      return $this;
    }

    // Parse the HTML input with tidy and clean it.
    try {
      /* @var $tidy \tidy */
      $tidy = tidy_parse_string("<!doctype html><html><head><title>MovLib</title></head><body>{$this->valueRaw}</body></html>");
      $tidy->cleanRepair();
      if ($tidy->getStatus() === 2) {
        throw new \ErrorException;
      }
    }
    catch (\ErrorException $e) {
      throw new ValidationException($i18n->t("Invalid HTML in “{label}” text.", [ "label" => $this->label ]));
    }

    $output = $this->validateDOM($tidy->body(), $this->allowedTags, $this->level);

    // Reset the level to its default state.
    $this->level = 0;

    // Parse and format the validated HTML output.
    // Please note that this error is impossible to provoke from the outside.
    // @codeCoverageIgnoreStart
    try {
      $tidy = tidy_parse_string("<!doctype html><html><head><title>MovLib</title></head><body>{$output}</body></html>");
      $tidy->cleanRepair();
      if ($tidy->getStatus() === 2) {
        throw new \ErrorException;
      }
    }
    catch (\ErrorException $e) {
      error_log($e);
      throw new ValidationException($i18n->t("Invalid HTML after the validation in “{label}” text.", [ "label" => $this->label ]));
    }
    // @codeCoverageIgnoreEnd

    // Replace redundant newlines, normalize UTF-8 characters and encode HTML characters.
    $this->valueRaw = \Normalizer::normalize(str_replace("\n\n", "\n", tidy_get_output($tidy)));
    $this->value    = $kernel->htmlEncode($this->valueRaw);

    return $this;
  }

  /**
   * Validates and sanitizes HTML anchors.
   *
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Kernel $kernel
   * @param \tidyNode $node
   *   The anchor.
   * @return string
   *   The tag name with the validated attributes.
   */
  protected function validateA($node) {
    global $i18n, $kernel;
    $attributes = [];
    $validateURL = null;

    // Check if the <code>href</code> attribute was set and validate the URL.
    if (!isset($node->attribute) || empty($node->attribute["href"])) {
      throw new ValidationException($i18n->t("Links without a link target in “{label}” text.", [ "label" => $this->label ]));
    }

    // Parse and validate the parts of the URL.
    if (($parts = parse_url($node->attribute["href"])) === false || !isset($parts["host"])) {
      throw new ValidationException($i18n->t(
        "Invalid link in “{label}” text ({link_url}).",
        [ "label" => $this->label, "link_url" => "<code>{$kernel->htmlEncode($node->attribute["href"])}</code>" ]
      ));
    }

    // Make protocol relative URLs for the internal domain and omit query string.
    if ($parts["host"] == $kernel->domainDefault || strpos($parts["host"], ".{$kernel->domainDefault}") !== false) {
      $attributes["href"] = "//{$parts["host"]}";
      // This is needed, because filter_var doesn't accept protocol relative URLs.
      $validateURL = "{$kernel->scheme}:{$attributes["href"]}";
      $parts["query"] = null;
    }
    // Add rel="nofollow" to external links, sanitize the protocol and fill query string offset if it doesn't exist.
    // If external links are not allowed, abort.
    else {
      if ($this->allowExternalLinks === false) {
        throw new ValidationException($i18n->t("No external links are allowed in “{label}” text.", [ "label" => $this->label ]));
      }
      if (isset($parts["scheme"]) && ($parts["scheme"] == "http" || $parts["scheme"] == "https")) {
        $attributes["href"] = "{$parts["scheme"]}://";
      }
      else {
        $attributes["href"] = "http://";
      }
      $attributes["rel"]  = "nofollow";
      $attributes["href"] = "{$attributes["href"]}{$parts["host"]}";
      $validateURL        = $attributes["href"];
      $parts["query"]     = isset($parts["query"]) ? "?{$parts["query"]}" : null;
    }

    // Initialize the path offset with "/" if it doesn't exist.
    $parts["path"] = isset($parts["path"]) ? $parts["path"] : "/";

    // Initialize the fragment offset with null if it doesn't exist.
    $parts["fragment"] = isset($parts["fragment"]) ? "#{$parts["fragment"]}" : null;

    // Append path, query and fragment to the URL.
    $attributes["href"] = "{$attributes["href"]}{$parts["path"]}{$parts["query"]}{$parts["fragment"]}";
    $validateURL        = "{$validateURL}{$parts["path"]}{$parts["query"]}{$parts["fragment"]}";

    // Validate user, password and port, since we don't allow them.
    if (isset($parts["user"]) || isset($parts["pass"])) {
      throw new ValidationException($i18n->t(
        "Credentials are not allowed in “{label}” text ({link_url}).",
        [ "label" => $this->label, "link_url" => "<code>{$kernel->htmlEncode($node->attribute["href"])}</code>" ]
      ));
    }
    if (isset($parts["port"])) {
      throw new ValidationException($i18n->t(
        "Ports are not allowed in “{label}” text ({link_url}).",
        [ "label" => $this->label, "link_url" => "<code>{$kernel->htmlEncode($node->attribute["href"])}</code>" ]
      ));
    }

    if (filter_var($validateURL, FILTER_VALIDATE_URL, FILTER_REQUIRE_SCALAR | FILTER_FLAG_HOST_REQUIRED) === false) {
      throw new ValidationException($i18n->t(
        "Invalid link in “{label}” text ({link_url}).",
        [ "label" => $this->label, "link_url" => "<code>{$kernel->htmlEncode($node->attribute["href"])}</code>" ]
      ));
    }

    return "a{$this->expandTagAttributes($attributes)}";
  }

  /**
   * Validate <code><blockquote></code>.
   *
   * @global \MovLib\Data\I18n $i18n
   * @param \tidyNode $node
   *   The blockquote node to validate.
   * @return string
   *   The starting tag including allowed attributes.
   * @throws \MovLib\Exception\ValidationException
   */
  protected function validateBlockquote($node) {
    global $i18n;
    $this->blockquote      = true;
    $this->insertLastChild = "blockquote";

    // We don't have to check for children, because tidy already purges empty <blockquote> tags.

    /* @var $lastChild \tidyNode */
    $lastChild = array_pop($node->child);

    // Validate that <cite> only contains text and/or anchor nodes.
    if ($lastChild->name == "cite" && count($lastChild->child) > 0) {
      $citeAllowedTags = [
        "a"      => "&lt;a&gt;",
        "b"      => "&lt;b&gt;",
        "em"     => "&lt;em&gt;",
        "i"      => "&lt;i&gt;",
        "strong" => "&lt;strong&gt;",
      ];
      $citeContent = $this->validateDOM($lastChild, $citeAllowedTags);
      $this->lastChild = "<cite>{$citeContent}</cite>";
    }
    // A <blockquote> without a <cite> is invalid.
    else {
      throw new ValidationException(
        $i18n->t("The “{label}” text contains a quotation without source.",
        [ "label" => $this->label ]
      ));
    }

    // Do not allow quotations without content.
    if (!isset($node->child[0])) {
      throw new ValidationException(
        $i18n->t("The “{label}” text contains quotation without text.",
        [ "label" => $this->label ]
      ));
    }

    return "blockquote{$this->validateUserClasses($node)}";
  }

  /**
   * Validate a DOM tree starting at <code>$node</code>.
   *
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Kernel $kernel
   * @param \tidyNode $node
   *   The node to start from.
   * @param array $allowedTags
   *   Associative array containing the tag names as keys and the encoded tags as values.
   * @param integer $level [optional]
   *   The level to use (global or local). Defaults to <code>0</code>.
   * @return string
   *   The parsed and sanitized output.
   * @throws ValidationException
   */
  protected function validateDOM($node, &$allowedTags, &$level = 0) {
    global $i18n, $kernel;
    $nodes       = [ $level => [ $node]];
    $endTags     = [];
    $output      = null;

    // Traverse through the constructed document and validate its contents.
    do {
      while (!empty($nodes[$level])) {
        // Retrieve the next node from the stack.
        $node = array_shift($nodes[$level]);

        if ($level > 0) {
          // If we encounter a text node, simply encode its contents and continue with the next node.
          if ($node->type === TIDY_NODETYPE_TEXT) {
            $output .= $kernel->htmlEncode($node->value);
          }
          // If we encounter an allowed HTML tag validate it.
          elseif (isset($allowedTags[$node->name])) {
            // If we're already inside <blockquote>, ensure it doesn't contain any disallowed elements.
            if ($this->blockquote === true && isset($this->blockquoteDisallowedTags[$node->name])) {
              throw new ValidationException($i18n->t("Found disallowed tag {tag} in quotation.", [ "tag" => "<code>&lt;{$node->name}&gt;</code>" ]));
            }

            // Stack a closing tag to the current level, if needed.
            if (!isset($this->emptyTags[$node->name])) {
              $endTags[$level][] = "</{$node->name}>";
            }

            // Directly take care of the most common element that has allowed attributes, the content is validated in
            // the next iteration.
            if ($node->name == "p") {
              $node->name = "p{$this->validateUserClasses($node)}";
            }
            // If there are more complex validations to be done for the tag, invoke the corresponding method.
            else {
              $methodName = "validate{$node->name}";
              if (method_exists($this, $methodName)) {
                $node->name = $this->{$methodName}($node);
              }
            }

            // Append a starting tag including valid attributes (if any) of the current node to the output.
            $output .= "<{$node->name}>";
          }
          // Encountered a tag that is not allowed, abort.
          else {
            $allowedTagsList = implode(" ", $allowedTags);
            throw new ValidationException($i18n->t("Found disallowed HTML tags, allowed tags are: {taglist}", [ "taglist" => "<code>{$allowedTagsList}</code>" ]));
          }
        }

        // Stack the child nodes to the next level if there are any.
        if (!empty($node->child)) {
          $nodes[++$level] = $node->child;
        }
      }

      // There are no more nodes to process in this level (while loop above has already handled them).
      // Go one level down and proceed with the next node.
      $level--;

      // Append all ending tags of the current level to the output, if we are greater than level 0 and if there are any.
      if ($level > 0 && isset($endTags[$level])) {
        while (($endTag = array_pop($endTags[$level]))) {
          if ($endTag == "</{$this->insertLastChild}>") {
            $this->blockquote      = false;
            $output               .= "{$this->lastChild}{$endTag}";
            $this->insertLastChild = null;
            $this->lastChild       = null;
          }
          else {
            $output .= $endTag;
          }
          // Check if we are at the end of a list and if the level fits.
          // If so, restore allowed tags and list flag.
          if ($this->list && "</{$this->list["tag"]}>" == $endTag && $this->list["level"] === $level) {
            $this->allowedTags = $this->list["allowed_tags"];
            $this->list        = false;
          }
        }
      }
    }
    while ($level > 0);

    return $output;
  }

  /**
   * Validate figure.
   *
   * @todo We have to keep reference of images in texts in order to update their cache buster string and remove them
   *       if the image is deleted.
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Kernel $kernel
   * @param \tidyNode $node
   *   The figure node to validate.
   * @param integer $level
   *   The current level in the DOM tree.
   * @return string
   *   The starting tag including allowed attributes.
   * @throws ValidationException
   */
  protected function validateFigure($node) {
    global $i18n, $kernel;
    $this->insertLastChild = "figure";
    // @todo: set figure flag and change error message in validateDOM().

    // Of course we can communicate the caption as seperate element, as it's visible to the user.
    if (count($node->child) !== 2 || $node->child[1]->name != "figcaption" || empty($node->child[1]->child)) {
      throw new ValidationException($i18n->t("The image caption is mandatory and cannot be empty."));
    }
    // Always communicate the <figure> element as image, the actual implementation isn't the user's concern and might
    // change with future web technologies.
    elseif ($node->child[0]->name != "img" || empty($node->child[0]->attribute["src"])) {
      throw new ValidationException($i18n->t("The image is mandatory and cannot be empty."));
    }

    // Validate the caption.
    $captionAllowedTags = [
      "a"      => "&lt;a&gt;",
      "b"      => "&lt;b&gt;",
      "em"     => "&lt;em&gt;",
      "i"      => "&lt;i&gt;",
      "strong" => "&lt;strong&gt;",
    ];
    $caption            = $this->validateDOM($node->child[1], $captionAllowedTags);
    // Clean the caption from any tags for the image's alt text.
    $alt                = strip_tags($caption);

    // Validate the image's src URL.
    if (($url = parse_url($node->child[0]->attribute["src"])) === false || !isset($url["host"])) {
      throw new ValidationException($i18n->t("Image URL seems to be invalid."));
    }

    // If a host is present check if it's from MovLib.
    if (isset($url["host"]) && $url["host"] != $kernel->domainStatic && strpos($url["host"], ".{$kernel->domainDefault}") === false) {
      throw new ValidationException($i18n->t("Only images from {movlib} are allowed.", [ "movlib" => $kernel->siteName ]));
    }

    // Check that the image actually exists and set width and height.
    try {
      $imgAttributes = getimagesize("{$kernel->documentRoot}/public{$url["path"]}")[3];
    }
    catch (\ErrorException $e) {
      throw new ValidationException($i18n->t("Image doesn’t exist ({image_src}).", [ "image_src" => "<code>{$node->child[0]->attribute["src"]}</code>" ]));
    }

    // Build the image tag.
    $this->lastChild = "<img alt='{$alt}' {$imgAttributes} src='//{$kernel->domainStatic}{$url["path"]}'><figcaption>{$caption}</figcaption>";

    // Delete all children, since they are already validated.
    $node->child = null;

    // Increase the level, since we need an ending tag, but have no children.
    $this->level++;

    return "figure{$this->validateUserClasses($node)}";
  }

  /**
   * Validate list.
   *
   * @param \tidyNode $node
   *   The list node to validate.
   * @return string
   *   The starting tag including allowed attributes.
   */
  protected function validateList($node) {
    // If this is the first opening list tag, set the list information array accordingly and constrain the allowed tags.
    if ($this->list === false) {
      $this->list = [
        "tag"          => $node->name,
        "level"        => $this->level,
        "allowed_tags" => $this->allowedTags,
      ];
      $this->allowedTags = [
        "a"      => "&lt;a&gt;",
        "b"      => "&lt;b&gt;",
        "em"     => "&lt;em&gt;",
        "i"      => "&lt;i&gt;",
        "li"     => "&lt;li&gt;",
        "ol"     => "&lt;ol&gt;",
        "strong" => "&lt;strong&gt;",
        "ul"     => "&lt;ul&gt;",
      ];
    }
    return "{$node->name}{$this->validateUserClasses($node)}";
  }

  /**
   * Validate ordered list.
   *
   * @param \tidyNode $node
   *   The list node to validate.
   * @return string
   *   The starting tag including allowed attributes.
   */
  protected function validateOl($node) {
    return $this->validateList($node);
  }

  /**
   * Validate unordered list.
   *
   * @param \tidyNode $node
   *   The list node to validate.
   * @return string
   *   The starting tag including allowed attributes.
   */
  protected function validateUl($node) {
    return $this->validateList($node);
  }

  /**
   * Validate that the node only contains allowed user CSS classes.
   *
   * @global \MovLib\Data\I18n $i18n
   * @param \tidyNode $node
   *   The node to validate.
   * @return null|string
   *   The expanded class attribute if present, otherwise <code>NULL</code>.
   * @throws \MovLib\Exception\ValidationException
   */
  protected function validateUserClasses($node) {
    global $i18n;
    if (isset($node->attribute) && !empty($node->attribute["class"])) {
      if (!isset($this->userClasses[$node->attribute["class"]])) {
        $classes = implode(" ", array_keys($this->userClasses));
        throw new ValidationException($i18n->t(
          "Disallowed CSS classes in “{label}” text, allowed values are: {classes}",
          [ "label" => $this->label, "classes" => "<code>{$classes}</code>" ]
        ));
      }
      else {
        return " class='{$node->attribute["class"]}'";
      }
    }
  }

  /**
   * Auto insert paragraphs (and breaks).
   *
   * @link http://core.trac.wordpress.org/browser/trunk/src/wp-includes/formatting.php WordPress source code
   * @param string $html
   *   The text which has to be formatted.
   * @return string
   *   Text which has been converted into correct paragraph tags.
   */
  protected function autoParagraph($html) {
    // Just to make things a little easier, pad the end
    $html = $this->normalizeLineFeeds("{$html}\n");

    // Normalize break tags.
    $html = preg_replace("#<br */?>#", "<br>", $html);

    // Replace more than one break in a row with to line feeds.
    $html = preg_replace("#<br>\s*<br>#", "\n\n", $html);

    // Space things out a little
    $allblocks = "(?:dl|dd|dt|ul|ol|li|blockquote|p|h[2-6]|figure|figcaption|img)";

    // Insert one line feed before each block level tag.
    $html = preg_replace("#(<{$allblocks}[^>]*>)#", "\n$1", $html);

    // Insert two line feeds after each block level tag.
    $html = preg_replace("#(</{$allblocks}>)#", "$1\n\n", $html);

    // Take care of duplicates
    $html = preg_replace("#\n\n+#", "\n\n", $html);

    // Make paragraphs, including one at the end
    $lines = preg_split("#\n\s*\n#", $html, -1, PREG_SPLIT_NO_EMPTY);

    // Enclose all paragraphs.
    $html = null;
    $c   = count($lines);
    for ($i = 0; $i < $c; ++$i) {
      $lines[$i] = trim($lines[$i], "\n");
      $html     .= "<p>{$lines[$i]}</p>\n";
    }

    // Don't pee all over a tag
    $html = preg_replace("#<p>\s*(</?{$allblocks}[^>]*>)\s*</p>#", "$1", $html);

    // Problem with nested lists and figure captions.
    $html = preg_replace("#<p>(<(li|figcaption).+?)</p>#", "$1", $html);

    \FB::send($html);

    // Move the opening paragraph inside the blockquote (opening and closing.
    $html = preg_replace("#<p><blockquote([^>]*)>#i", "<blockquote$1><p>", $html);
    $html = str_replace("</blockquote></p>", "</p></blockquote>", $html);

    // Don't pee all over a block tag.
    $html = preg_replace("#<p>\s*(</?{$allblocks}[^>]*>)#", "$1", $html);
    $html = preg_replace("#(</?{$allblocks}[^>]*>)\s*</p>#", "$1", $html);

    // Make line breaks
    $html = preg_replace("#(?<!<br>)\s*\n#", "<br>\n", $html);

    // No breaks behind block elements.
    $html = preg_replace("#(</?{$allblocks}[^>]*>)\s*<br>#", "$1", $html);

    // No breaks before closing block elements if there is only whitespace.
    $html = preg_replace("#<br>(\s*</?(?:p|li|dl|dd|dt|ul|ol)[^>]*>)#", "$1", $html);

    // Remove excess line feeds before closing paragraph tags.
    return preg_replace("#\n</p>$#", "</p>", $html);
  }

}
