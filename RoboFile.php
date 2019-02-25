<?php


/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
  use \Boedah\Robo\Task\Drush\loadTasks;

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

  protected function composerInstall() {
    $this->_exec('`which composer` install');
  }

  /**
   * Install the drupal site with default credentials.
   * @param null $site_name
   */
  public function install($site_name = NULL) {
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
//      ->option('site-name', $site_name)
//      ->option('site-email', 'admin@example.com')
//      ->option('account-mail', 'admin@example.com')
      ->option('account-name', 'admin')
      ->option('account-pass', 'admin')
      ->run();
  }

  /**
   * Reset the files managed by composer.
   * @param array $opts
   */
  public function reset($opts = ['delete-lock|l' => FALSE]) {
    // Remove composer managed drupal directories
    $this->taskDeleteDir(self::ENV_DOCROOT . '/core')->run();
    $this->taskDeleteDir(self::ENV_DOCROOT . '/libraries')->run();
    $this->taskDeleteDir(self::ENV_DOCROOT . '/modules/contrib')->run();
    $this->taskDeleteDir(self::ENV_DOCROOT . '/themes/contrib')->run();

    // Optionally remove composer.lock
    if ($opts['delete-lock']) {
      $this->taskFilesystemStack()
        ->remove('/app/composer.lock')
        ->run();
    }

    // Remove the vendor directory.
    // How will this affect Robo??
    $this->taskDeleteDir('/app/vendor')->run();
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

  public function runTests() {
    $this->taskExecStack()
      ->exec('/app/bin/phpcs')
      ->exec('/app/bin/unit')
      ->exec('/app/bin/behat --config=/app/test/behat/behat.yml -v')
      ->run();
  }
}
