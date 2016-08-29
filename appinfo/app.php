<?php
/**
 * Nextcloud - scanner
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Greg Sutcliffe <nextcloud@emeraldreverie.org>
 * @copyright Greg Sutcliffe 2016
 */

namespace OCA\Scanner\AppInfo;

use OCP\AppFramework\App;
use \OCA\Scanner\Storage\ScannerStorage;

require_once __DIR__ . '/autoload.php';

class Application extends App {

  public function __construct(array $urlParams=array()){
    parent::__construct('scanner', $urlParams);

    $container = $this->getContainer();

    /**
     * Storage Layer
     */
    $container->registerService('ScannerStorage', function($c) {
      return new ScannerStorage($c->query('UserStorage'));
    });

    $container->registerService('UserStorage', function($c) {
      return $c->query('ServerContainer')->getUserFolder();
    });

  }
}

$eventDispatcher = \OC::$server->getEventDispatcher();
$eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function() {
  script('scanner', 'menu');  // adds js/script.js
  style('scanner', 'style');  // adds js/script.js
});
