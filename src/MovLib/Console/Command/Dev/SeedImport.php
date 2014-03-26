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
namespace MovLib\Console\Command\Dev;

use \Elasticsearch\Client as ElasticClient;
use \MovLib\Console\Command\Admin\FixPermissions;
use \MovLib\Console\Command\Install\SeedAspectRatios;
use \MovLib\Console\Command\Install\SeedCountries;
use \MovLib\Console\Command\Install\SeedCurrencies;
use \MovLib\Console\Command\Install\SeedLanguages;
use \MovLib\Console\Command\Install\SeedSubtitles;
use \MovLib\Data\Shell;
use \MovLib\Exception\DatabaseException;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Input\StringInput;
use \Symfony\Component\Console\Output\ConsoleOutput;
use \Symfony\Component\Console\Output\OutputInterface;

/**
 * The seed import command can be used during development to import the complete seed data or only specific stuff.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class SeedImport extends \MovLib\Console\Command\AbstractCommand {


  // ------------------------------------------------------------------------------------------------------------------- Constants


  /**
   * Command option to create history repositories.
   *
   * @var string
   */
  const OPTION_HISTORY = "history";

  /**
   * Command option to import database data.
   *
   * @var string
   */
  const OPTION_DATABASE = "database";

  /**
   * Command option to import ElasticSearch index and types.
   *
   * @var string
   */
  const OPTION_ELASTICSEARCH = "elasticsearch";

  /**
   * Command option to import Intl ICU data.
   *
   * @var string
   */
  const OPTION_INTL_ICU = "intl-icu";

  /**
   * Command option to import upload data.
   *
   * @var string
   */
  const OPTION_UPLOAD = "upload";


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * Associative array containing the database insert script name (without extension) as key and the absolute path to
   * the script as value.
   *
   * @see SeedImport::__construct()
   * @var array
   */
  protected $databaseScripts = [];

  /**
   * Associative array containing all history supported types.
   *
   * Key have to be plural, value sigular.
   *
   * @var array
   */
  protected $historyTypes = [
    "movies" => "movie"
  ];

  /**
   * Absolute path to the seed path (document root is added in constructor).
   *
   * @see SeedImport::__construct()
   * @var string
   */
  protected $seedPath = "/etc/seed";

  /**
   * Associative array containing the upload directory names as key and the absolute path to the directory as value.
   *
   * @see SeedImport::__construct()
   * @var array
   */
  protected $uploadDirectories = [];


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * @inheritdoc
   */
  protected function configure() {
    $this->setName("seed-import");
    $this->setDescription("Import the complete seed data or only specific data via options.");
    // History needs a custom shortcut because '-h' is already defined by Symfony.
    $this->addOption(self::OPTION_HISTORY, "hi", InputOption::VALUE_NONE, "Create all history repositories.");
    $this->addOption(self::OPTION_DATABASE, "db", InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, "Import specific database data.");
    $this->addOption(self::OPTION_ELASTICSEARCH, "es", InputOption::VALUE_NONE, "Create Elasticsearch index and types.");
    $this->addOption(self::OPTION_INTL_ICU, "ii", InputOption::VALUE_NONE, "Import ICU data.");
    $this->addOption(self::OPTION_UPLOAD, "u", InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, "Import specific upload data.");
  }

  /**
   * Import one or more database seed data.
   *
   * @global \MovLib\Tool\Database $db
   * @param array $scriptNames [optional]
   *   Numeric array containing the database seed data names that should be imported, if left empty (<code>NULL</code>)
   *   all seed data will be imported.
   * @return this
   * @throws \ErrorException
   * @throws \RuntimeException
   */
  public function databaseImport(array $scriptNames = null) {
    global $db;
    $queries = $scripts = null;

    // If no scripts where specified for import Import all available scripts.
    if (empty($scriptNames)) {
      $truncate = false;
      $scripts  = $this->databaseScripts;
    }
    // Otherwise go through all specified scripts and check if they are available.
    else {
      // Be sure to truncate if we're importing some specific scripts.
      $truncate = true;

      foreach ($scriptNames as $scriptName) {
        try {
          $scripts[$scriptName] = $this->databaseScripts[$scriptName];
        }
        catch (\ErrorException $e) {
          throw new \InvalidArgumentException("No script with name '{$scriptName}' found!", null, $e);
        }
      }
    }

    // Build the queries for all scripts.
    if (!empty($scripts)) {
      foreach ($scripts as $table => $script) {
        $this->write("Importing database data for table '{$table}' ...");

        // We're only truncating if we're importing specific scripts.
        if ($truncate === true) {
          $queries .= "TRUNCATE TABLE `{$table}`;";
        }

        // Concatenate all scripts to one singel big query.
        if (($queries .= file_get_contents($script)) === false) {
          throw new \RuntimeException("Couldn't read '{$script}'!");
        }
      }
    }

    // Only continue if we have at least a single query to execute.
    if (!empty($queries)) {
      try {
        $db->transactionStart();
        $db->queries($queries, false);
        $db->transactionCommit();
      }
      catch (DatabaseException $e) {
        $db->transactionRollback();
        throw $e;
      }
    }

    return $this->write("Successfully imported database data for '" . implode("', '", array_keys($scripts)) . "'.", self::MESSAGE_TYPE_INFO);
  }

  /**
   * Create elastic search index and types.
   *
   * @return this
   */
  public function elasticImport() {
    $this->write("Deleting old ElasticSearch indices and creating new ...");

    $elasticClient = new ElasticClient();

    // Delete all indices and create movlib index.
    $elasticClient->indices()->delete([ "index" => "_all" ]);
    $elasticClient->indices()->create([ "index" => "movlib" ]);

    // Create movie type.
    $elasticClient->create([ "index" => "movlib", "type" => "movie", "body" => [
      "titles" => [ "type" => "string", "analyzer" => "simple" ],
      "year"   => [ "type" => "short", "index" => "not_analyzed" ],
    ]]);

    // Create person type.
    $elasticClient->create([ "index" => "movlib", "type" => "person", "body" => [
      "names" => [ "type" => "string", "analyzer" => "simple" ],
    ]]);

    return $this->write("Done, ElasticSearch is ready for indexing!", self::MESSAGE_TYPE_INFO);
  }

  /**
   * @inheritdoc
   * @global \MovLib\Tool\Kernel $kernel
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    global $kernel;

    $this->seedPath = "{$kernel->documentRoot}{$this->seedPath}";
    foreach (glob("{$this->seedPath}/" . self::OPTION_DATABASE . "/*.sql") as $seedScript) {
      $this->databaseScripts[basename($seedScript, ".sql")] = $seedScript;
    }

    // Only go through the public directory, the user images don't have private files.
    foreach (glob("{$this->seedPath}/" . self::OPTION_UPLOAD . "/public/*", GLOB_ONLYDIR) as $uploadDirectory) {
      $this->uploadDirectories[basename($uploadDirectory)] = $uploadDirectory;
    }

    $options = $input->getOptions();
    $all     = true;
    foreach ([ self::OPTION_DATABASE, self::OPTION_ELASTICSEARCH, self::OPTION_HISTORY, self::OPTION_INTL_ICU, self::OPTION_UPLOAD ] as $option) {
      if ($options[$option]) {
        $this->{"{$option}Import"}($options[$option]);
        $all = false;
      }
    }
    if ($all === true) {
      $this->seedImport();
    }

    return 0;
  }

  /**
   * Create history repositories.
   *
   * @global \MovLib\Tool\Database $db
   * @global \MovLib\Tool\Kernel $kernel
   * @param string $type [optional]
   *   If supplied, only repositories of this type (e.g. <code>"movie"</code>) are created, otherwise all repositories
   *   are created.
   * @return this
   * @throws \InvalidArgumentException
   */
  public function historyImport() {
    global $db, $kernel;

    foreach ($this->historyTypes as $typePlural => $typeSingular) {
      // Remove complete history repository if it's present in the file system.
      $path = "{$kernel->documentRoot}/private/history/{$typeSingular}";
      if (is_dir($path)) {
        Shell::execute("rm -rf '{$path}'");
      }

      // Creat new repository for each database entry we have.
      if (($result = $db->query("SELECT `{$typeSingular}_id` FROM `{$typePlural}`")->get_result())) {
        $class   = new \ReflectionClass("\\MovLib\\Data\\History\\" . ucfirst($typeSingular));
        $queries = null;
        while ($row = $result->fetch_row()) {
          $commitHash = $db->escapeString($class->newInstance($row[0])->createRepository());
          $queries   .= "UPDATE `{$typePlural}` SET `commit` = '{$commitHash}' WHERE `{$typeSingular}_id` = {$row[0]};";
        }
        if ($queries) {
          $db->queries($queries);
        }
      }
    }

    return $this;
  }

  /**
   * Import all ICU translations.
   *
   * @return this
   */
  public function icuImport() {
    $this->write("Importing ICU translations ...");
    $input  = new StringInput("");
    $output = new ConsoleOutput($this->output->getVerbosity());
    (new SeedAspectRatios())->run($input, $output);
    (new SeedCountries())->run($input, $output);
    (new SeedCurrencies())->run($input, $output);
    (new SeedLanguages())->run($input, $output);
    (new SeedSubtitles())->run($input, $output);
    return $this->write("Imported all ICU translations.", self::MESSAGE_TYPE_INFO);
  }

  /**
   * Import ICU currency translations.
   *
   * @global \MovLib\Tool\Kernel $kernel
   * @param string $source
   *   Absolute path to ICU source resources.
   * @return this
   */
  protected function icuImportCurrency($source) {
      $rb = $this->icuGetResourceBundle($source, $locale, $languageCode);
      foreach ($codes as $code) {
        $name         = $rb["Currencies"][$code][1];
        $symbol       = $rb["Currencies"][$code][0];
        $translation .= "  \"{$code}\" => [ \"name\" => \"{$name}\", \"symbol\" => \"{$symbol}\" ],\n";
      }
  }

  /**
   * ICU helper method to iterate over all available system languages and write the translated file.
   *
   * @global \MovLib\Tool\Kernel $kernel
   * @staticvar string $scaffold
   *   Used for caching of the scaffold file.
   * @param string $destination
   *   Absolute path to the directory where the translated files should be stored.
   * @param callable $callback
   *   The callback method that is called on each iteration.
   * @param string $comment
   *   The class comment for the docBlock.
   * @return this
   */
  protected function icuWriteTranslations($destination, callable $callback, $comment) {
    global $kernel;
    static $scaffold = null;
    if (!$scaffold) {
      $scaffold = file_get_contents("{$kernel->pathTranslations}/scaffold.php");
    }
    foreach ($kernel->systemLanguages as $languageCode => $locale) {
      $translation = "return [\n";
      $callback($translation, $locale, $languageCode);
      $translation .= "];\n// @codeCoverageIgnoreEnd\n";
      file_put_contents("{$destination}/{$locale}.php", str_replace("{classComment}", $comment, $scaffold) . $translation);
    }
    return $this;
  }

  /**
   * Import all seed data.
   *
   * @global \MovLib\Tool\Kernel $kernel
   * @return this
   * @throws \MovLib\Exception\DatabaseExeption
   * @throws \ErrorException
   * @throws \RuntimeException
   */
  public function seedImport() {
    global $kernel;

    // Array containing the names of all tasks that should be executed.
    $tasks = [
      "databaseImport",
      "elasticImport",
      "icuImport",
      "uploadImport",
    ];

    // The two additional operations are for the schema import itself.
    $this->write("Importing all seed data ...")->progressStart(count($tasks) + 2);
    if (!file_exists("{$kernel->documentRoot}/conf/mariadb/movlib.sql")) {
      throw new FileSystemException("Couldn't read schema!");
    }
    $this->progressAdvance();
    // We have to execute this in the shell directly, because our database object always tries to connect to the default
    // database, which might not exist yet!
    Shell::execute("mysql < {$kernel->documentRoot}/conf/mariadb/movlib.sql", $output);
    $this->progressAdvance();
    foreach ($tasks as $task) {
      $this->$task()->progressAdvance();
    }
    return $this->progressFinish()->write("Successfully imported seed data!", self::MESSAGE_TYPE_INFO);
  }

  /**
   * Import one ore more upload seed data.
   *
   * @global \MovLib\Tool\Kernel $kernel
   * @param array $directoryNames [optional]
   *   Numeric array containing the upload directory data names that should be imported, if left empty (<code>NULL</code>)
   *   all seed data will be imported.
   * @return this
   * @throws \ErrorException
   * @throws \RuntimeException
   */
  public function uploadImport(array $directoryNames = null) {
    global $kernel;
    foreach ([ "private", "public" ] as $visibility) {
      $directories     = null;
      $seedDirectory   = "{$this->seedPath}/" . self::OPTION_UPLOAD . "/{$visibility}";
      $uploadDirectory = "{$kernel->documentRoot}/{$visibility}/" . self::OPTION_UPLOAD;
      if (empty($directoryNames)) {
        $directories = $this->uploadDirectories;
        $this->uploadMoveImages($seedDirectory, $uploadDirectory);
      }
      else {
        $c = count($directoryNames);
        for ($i = 0; $i < $c; ++$i) {
          if (isset($this->uploadDirectories[$directoryNames[$i]])) {
            $directories[$directoryNames[$i]] = $this->uploadDirectories[$directoryNames[$i]];
            $this->uploadMoveImages("{$seedDirectory}/{$directoryNames[$i]}", "{$uploadDirectory}/{$directoryNames[$i]}");
          }
          else {
            throw new \InvalidArgumentException("No directory with name '{$directoryNames[$i]}' found!");
          }
        }
      }
    }

    // Fix permissions if executed as root.
    if ($this->checkPrivileges(false) === true) {
      (new FixPermissions())->run(new StringInput(""), new ConsoleOutput($this->output->getVerbosity()));
    }

    return $this->write("Successfully imported upload data for '" . implode("', '", array_keys($directories)) . "'.", self::MESSAGE_TYPE_INFO);
  }

  /**
   * Helper method to delete and move seed upload files.
   *
   * @param string $from
   *   Absolute path to the source directory.
   * @param string $to
   *   Absolute path to the target directory.
   * @return this
   */
  protected function uploadMoveImages($from, $to) {
    if (is_dir($from)) {
      Shell::execute("mkdir -p {$to}");
      Shell::execute("rm -rf {$to}/*");
      Shell::execute("cp -R {$from}/* {$to}");
    }
    return $this;
  }

}
