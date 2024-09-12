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

use OCP\Util;
use OCP\AppFramework\App;
use OCA\Scanner\Storage\ScannerStorage;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\Preview\BeforePreviewFetchedEvent;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCA\Scanner\Listeners\ListenerFilesLoadScripts;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App  implements IBootstrap
{
  const APPNAME = 'scanner';
  public function __construct(array $urlParams = array())
  {
    parent::__construct(self::APPNAME, $urlParams);
  }

  public function register(IRegistrationContext $context): void {
    /**
     * Storage Layer
     */
    $context->registerService('ScannerStorage', function ($c) {
      return new ScannerStorage($c->query('UserStorage'), $c->query('L10N'));
    });

    $context->registerService('UserStorage', function ($c) {
      return $c->query('ServerContainer')->getUserFolder();
    });

    $context->registerService('L10N', function ($c) {
      return $c->query('ServerContainer')->getL10N($c->query('AppName'));
    });

    $context->registerEventListener(LoadAdditionalScriptsEvent::class, ListenerFilesLoadScripts::class);

  }

  public function boot(IBootContext $context): void
  {
  }
}
