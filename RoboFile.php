<?php


/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
  # Lando docroot
  const ENV_DOCROOT = "/app/docroot";
  # Drupal install profile
  const ENV_PROFILE = "startup";
  // Drush executable
  const DRUSH_EXEC = "/app/bin/drush";

  function __construct() {
    // Treat this command like bash -e and exit as soon as there's a failure.
    $this->stopOnFail();
  }

  protected function composerInstall($opts = ['no-dev' => FALSE]) {
    $composer_task = $this->taskComposerInstall()->optimizeAutoloader();
    if ($opts['no-dev']) {
      $composer_task->noDev();
    }

    return $composer_task;
  }

  /**
   * Install the drupal site with default credentials.
   */
  public function install() {
    $this->composerInstall()->run();

    // Copy local settings file.
    if (!file_exists(self::ENV_DOCROOT . '/sites/default/settings.local.php')) {
      $this->taskFilesystemStack()
        ->copy(
          self::ENV_DOCROOT . "/sites/default.settings.local.php",
          self::ENV_DOCROOT . "/sites/default/settings.local.php"
        )
        ->run();
    }

    // Install the site.
    $this->drush()
      ->args(['site-install', 'startup', 'install_configure_form.enable_update_status_module=0'])
      ->option('verbose')
      ->option('account-name', 'admin')
      ->option('account-pass', 'admin')
      ->run();
  }

  /**
   * Reset the files managed by composer.
   * @param array $opts
   * @return \Robo\Collection\Collection
   */
  public function reset($opts = ['delete-lock|l' => FALSE]) {
    $collection = $this->collectionBuilder();

    // Remove composer managed drupal directories
    $collection
      ->addTask($this->taskDeleteDir(self::ENV_DOCROOT . '/core'))
      ->addTask($this->taskDeleteDir(self::ENV_DOCROOT . '/libraries'))
      ->addTask($this->taskDeleteDir(self::ENV_DOCROOT . '/modules/contrib'))
      ->addTask($this->taskDeleteDir(self::ENV_DOCROOT . '/themes/contrib'));

    // Optionally remove composer.lock
    if ($opts['delete-lock']) {
      $collection->addTask($this->taskFilesystemStack()->remove('/app/composer.lock'));
    }

    // Remove the vendor directory.
    $collection->addTask($this->taskDeleteDir('/app/vendor'));

    return $collection;
  }

  /**
   * Run local deploy tasks.
   * Clear caches
   * Import config
   * Run db updates
   * Update entities
   * Import config
   * Clear caches
   */
  public function refresh() {
    $collection = $this->collectionBuilder();

    $collection
      ->addTask($this->composerInstall())
      ->addTask($this->drush()->arg('cr'))
      ->addTask($this->drush()->arg('cim'))
      ->addTask($this->drush()->arg('updb'))
      ->addTask($this->drush()->arg('entup'))
      ->addTask($this->drush()->arg('cim'))
      ->addTask($this->drush()->arg('cr'));

    return $collection;
  }

  /**
   * Return drush with default arguments.
   *
   * @return \Robo\Task\Base\Exec
   *   A drush exec command.
   */
  protected function drush() {
    return $this->taskExec(self::DRUSH_EXEC)
      ->option('root', self::ENV_DOCROOT, '=')
      ->option('yes'); // Assume yes
  }

  /**
   * $ robo run:tests
   *
   * Run all tests
   */
  public function runTests() {
    $this->taskExecStack()
      ->exec('/app/bin/phpcs')
      ->exec('/app/bin/unit')
      ->exec('/app/bin/behat --config=/app/test/behat/behat.yml -v')
      ->run();
  }
}
