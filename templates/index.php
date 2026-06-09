<?php

declare(strict_types=1);

use OCP\Util;

Util::addScript(OCA\DpaTracker\AppInfo\Application::APP_ID, OCA\DpaTracker\AppInfo\Application::APP_ID . '-main');
Util::addStyle(OCA\DpaTracker\AppInfo\Application::APP_ID, OCA\DpaTracker\AppInfo\Application::APP_ID . '-main');

?>

<div id="dpatracker"></div>
