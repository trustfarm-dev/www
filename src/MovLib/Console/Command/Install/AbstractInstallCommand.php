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
namespace MovLib\Console\Command\Install;

use \MovLib\Data\FileSystem;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * Base class for all install commands.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
abstract class AbstractInstallCommand extends \MovLib\Console\Command\AbstractCommand {

  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "AbstractInstallCommand";
  // @codingStandardsIgnoreEnd


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


//  /**
//   * {@inheritdoc}
//   */
//  public function __construct($name = null) {
//    parent::__construct($name);
//    $this->addOption("environment", "e", InputOption::VALUE_OPTIONAL, "The environment we're currenlty working with.", "dist");
//    $this->addOption("keep", "k", InputOption::VALUE_NONE, "Keep downloaded source files on disc.");
//  }


  // ------------------------------------------------------------------------------------------------------------------- Abstract Methods


  /**
   * Install the software.
   *
   * @return this
   */
  abstract protected function install($conf);

  /**
   * Get the configuration from the kernel for this command.
   *
   * @return mixed
   *   The configuration from the kernel for this command.
   */
  abstract protected function getConfiguration();

  /**
   * Validate the software's configuration.
   *
   * @return this
   */
  abstract protected function validate($conf);


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * Install package with checkinstall command.
   *
   * @param type $directory
   * @param type $name
   * @param type $version
   * @param array $arguments
   * @return type
   */
  final protected function checkinstall($directory, $name, $version, array $arguments = []) {
    if (strpos($name, "movlib-") === false) {
      $this->writeDebug("Adding 'movlib-' to package name, which results in 'movlib-{$name}'...");
      $name = "movlib-{$name}";
    }

    $release = 1;
    $oldVersion = $this->getVersion($name);
    if ($oldVersion === false) {
      $this->writeDebug("Couldn't determine old version of {$name} (may not be installed)...");
    }
    elseif (version_compare($version, $oldVersion, ">") === true) {
      $oldRelease = $this->getRelease($name);
      if ($oldRelease === false) {
        $this->writeDebug("Couldn't determine old release of {$name} (may not be installed yet)...");
      }
      else {
        $this->writeDebug("Old release of {$name} is '{$release}'...");
        $release++;
      }
    }
    $this->writeDebug("Release is going to be '{$release}'...");

    // Always purge old installations before attempting to install new package of the same software.
    $this->aptPurge($name);

    $command = "checkinstall --" . implode(" --", [
      "default",
      "install",
      "maintainer='{$kernel->configuration->webmaster}'",
      "nodoc",
      "pkgname='{$name}'",
      "pkgversion='{$version}'",
      "pkgrelease='{$release}'",
      "type='debian'",
    ] + $arguments);

    return $this->exec($command, $directory);
  }

  /**
   * Configure given source.
   *
   * @param string $directory
   *   Absolute path to the source that should be configured.
   * @param \MovLib\Stub\Configuration\Configure $options [optional]
   *   The configure options for the installation, defaults to <code>NULL</code> no options.
   * @return $this
   * @throws \RuntimeException
   */
  final protected function configureInstallation($directory, $options = null) {
    $defaultFlags = "'-O3 -m64 -march=native'";
    $command      = "";

    if (($cflags = isset($options->cflags) ? $options->cflags : $defaultFlags) !== false) {
      $command .= "CFLAGS={$cflags} ";
    }

    if (($cxxflags = isset($options->cxxflags) ? $options->cxxflags : $defaultFlags) !== false) {
      $command .= "CXXFLAGS={$cxxflags} CPPFLAGS={$cxxflags} ";
    }

    if (!empty($options->ldflags)) {
      $command .= "LDFLAGS='{$options->ldflags}' ";
    }

    $command .= "./configure";
    if (!empty($options->arguments)) {
      $command .= " --" . implode(" --", $options->arguments);
    }

    return $this->exec($command, $directory);
  }

  /**
   * Download a file.
   *
   * @param string $url
   *   The URL of the file to download.
   * @param null|string $destination [optional]
   *   The canonical absolute destination directory, defaults to <code>NULL</code> which means that the downloaded file
   *   will be stored in the temporary directory.
   * @param null|string $name [optional]
   *   The downloaded file's name, if no name is supplied the base name of the URL is used.
   * @param boolean $delete [optional]
   *   Whether the file should be deleted at the end of execution or not, defaults to <code>NULL</code>, the file will
   *   be deleted if the keep option wasn't supplied.
   * @return string
   *   Canonical absolute path to the downloaded file.
   * @throws \RuntimeException
   */
  final protected function download($url, $destination = null, $name = null, $delete = null) {
    if (!isset($name)) {
      $name = basename($url, ".git");
    }
    if (!isset($destination)) {
      $destination = "{$kernel->documentRoot}/tmp";
    }

    $scheme      = parse_url($url, PHP_URL_SCHEME);
    $destination = "{$destination}/{$name}";

    if (file_exists($destination)) {
      if ($scheme == "git") {
        $this->exec("git pull", $destination);
      }
      else {
        $this->writeVerbose("Destination '{$destination}' already exists, <comment>skipping download</comment>...");
      }
    }
    else {
      switch ($scheme) {
        case "http":
        case "https":
        case "ftp":
          $this->exec("wget --output-document='{$destination}' --timeout=30 --verbose '{$url}'");
          break;

        case "git":
          $this->exec("git clone '{$url}' '{$destination}'");
          break;

        default:
          throw new \UnexpectedValueException("Unsupported scheme '{$scheme}'");
      }
    }

    if (!isset($delete)) {
      $delete = !$this->input->getOption("keep");
    }
    if ($delete === true) {
      $kernel->registerFileForDeletion($destination);
    }

    return $destination;
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $this->writeDebug("Generating global configuration...", self::MESSAGE_TYPE_COMMENT);
    $etc = "{$kernel->documentRoot}/etc/movlib";
    $env = $input->getOption("environment");
    $cfn = FileSystem::getJSON("{$etc}/dist.json", true);

    if ($env != "dist") {
      $this->writeDebug("Merging dist with {$env}...");
      $cfn = array_replace_recursive($cfn, FileSystem::getJSON("{$etc}/{$env}.json", true));
    }

    $etc = "{$etc}/movlib.json";

    if (is_file($etc)) {
      $this->writeDebug("Deleting old global configuration...");
      unlink($etc);
    }

    $this->writeDebug("Writing new global configuration...");
    FileSystem::putJSON($etc, $cfn, LOCK_EX);

    $this->writeDebug("Exporting new global configuration to kernel...");
    $kernel->configuration = json_decode(json_encode($cfn));
    $kernel->systemUser    = $kernel->configuration->user;
    $kernel->systemGroup   = $kernel->configuration->group;

    $this->writeDebug("Changing global configuration ownership to {$kernel->systemUser}:{$kernel->systemGroup}...");
    FileSystem::changeOwner($etc, $kernel->systemUser, $kernel->systemGroup);

    $this->writeDebug("Changing global configuration mode to read-only...");
    FileSystem::changeMode($etc, "0444");

    $name = $this->getName();
    $conf = $this->getConfiguration();

    $this->write("Validating {$name} configuration...", self::MESSAGE_TYPE_COMMENT);
    $this->validate($conf);
    $this->write("{$name} configuration valid!", self::MESSAGE_TYPE_INFO);

    $this->write("Installing {$name}, this may take several minutes...", self::MESSAGE_TYPE_COMMENT);
    $this->install($conf);
    $this->write("Successfully installed {$name}!", self::MESSAGE_TYPE_INFO);

    return 0;
  }

  /**
   * Extract given source archive to target directory.
   *
   * @param string $source
   *   Absolute path to the source archive.
   * @param null|string $target [optional]
   *   Absolute path to the target directory, defaults to <code>NULL</code> and the archive is extracted to the
   *   directory it currently resides.
   * @param boolean $delete [optional]
   *   Whether the archive's content should be deleted at the end of execution or not, defaults to <code>NULL</code>,
   *   the archive's content will be deleted if the keep option wasn't supplied.
   * @return string
   *   Canonical absolute path to the extracted files.
   * @throws \BadMethodCallException
   * @throws \PharException
   * @throws \UnexpectedValueException
   */
  final protected function extract($source, $target = null, $delete = null) {
    if (realpath($source) === false) {
      throw new \UnexpectedValueException("\$source must point to an existing archive on the local file system");
    }

    if (!isset($target)) {
      $target = dirname($source);
    }

    $archive     = new \PharData($source);
    $destination = "{$target}/{$archive->getFilename()}";

    if (file_exists($destination)) {
      $this->writeVerbose("Destination '{$destination}' already exists, <comment>skipping extraction</comment>...");
    }
    else {
      $this->writeDebug("Extracting '{$source}' to '{$destination}'...");
      $archive->extractTo($target);

      $this->writeDebug("Changing ownership of '{$destination}' to 'root:root' for all files...");
      FileSystem::changeOwner($destination, "root", "root", true);
    }

    if (!isset($delete)) {
      $delete = !$this->input->getOption("keep");
    }
    if ($delete === true) {
      $kernel->registerFileForDeletion($destination);
    }

    return $destination;
  }

  /**
   * Expland placeholder tokens.
   *
   * @param string|array $placeholders
   *   The string or array containing the placeholders.
   * @param array $tokens
   *   Associative array where the key is the token and the value the replacement for the token.
   * @return this
   * @throws \RuntimeException
   */
  final public function expandPlaceholderTokens(&$placeholders, array $tokens) {
    $tokens = array_merge([
      "user"  => $kernel->configuration->user,
      "group" => $kernel->configuration->group,
    ], $tokens);
    $this->writeDebug("Expanding placeholder tokens...", self::MESSAGE_TYPE_COMMENT);
    $pregTokens = implode("|", array_keys($tokens));
    foreach ($placeholders as $delta => $argument) {
      $placeholders[$delta] = preg_replace_callback("/\{\{ (user|group|{$pregTokens}) \}\}/", function ($matches) use ($tokens) {
        if (isset($matches[1]) && isset($tokens[$matches[1]])) {
          $this->writeDebug("Replacing '{$matches[1]}' with '{$tokens[$matches[1]]}'...");
          return "'{$tokens[$matches[1]]}'";
        }
        throw new \RuntimeException("Unknown placeholder token '{$matches[0]}'!");
      }, $argument);
    }
    return $this;
  }

  /**
   * Get release of installed package.
   *
   * @param string $packageName
   *   The package's full name to get the release for.
   * @return integer|boolean
   *   The package's installed release if available, otherwise <code>FALSE</code>. Note that this method may return
   *   <code>0</code> and <code>FALSE</code>, be sure to check the returned value with <code>===</code>.
   */
  final protected function getRelease($packageName) {
    $release = preg_replace("/.*version: [a-z0-9\.]-([0-9])/im", "$1", `dpkg --status '{$packageName}' 2<&-`);
    if (empty($release)) {
      return false;
    }
    return (integer) $release;
  }

  /**
   * Get version string of installed package.
   *
   * @param string $packageName
   *   The package's full name to get the version for.
   * @return string|boolean
   *   The package's installed version string if available, otherwise <code>FALSE</code>.
   */
  final protected function getVersion($packageName) {
    $version = preg_replace("/.*version: ([a-z0-9\.])/im", "$1", `dpkg --status '{$packageName}' 2<&-`);
    if (empty($version)) {
      return false;
    }
    return $version;
  }

}
