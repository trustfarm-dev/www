<#assign licenseFirst="/*!">
<#assign licensePrefix=" *">
<#assign licenseLast=" */">
<#import "../${project.license}.ftl" as prj>
<#include "../Licenses/license-${project.license}.txt">

/* jshint browser:true */

/**
 * @module ${prj.name}
 * @namespace modules
 * @submodule ${name}
 * @author ${user}
 * @copyright © ${date?date?string("yyyy")} ${prj.name}
 * @license ${prj.licenseLink} ${prj.licenseName}
 * @link ${prj.website}
 * @since ${prj.version}
 * @param {window} window
 * @param {document} document
 * @param {MovLib} MovLib
 * @return {undefined}
 */
(function (window, document, MovLib) {
  "use strict";

  /**
   * Attach ${name} to the MovLib modules.
   *
   * @param {HTMLCollection} context
   *   The context we are currently working with.
   * @returns {MovLib}
   */
  MovLib.modules.${name} = function (context) {
    return MovLib;
  };

})(window, window.document, window.MovLib);
