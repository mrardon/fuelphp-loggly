<?php

Autoloader::add_core_namespace('Loggly');

Autoloader::add_classes(array(
  /**
   * Loggly classes.
   */
  'Loggly\\Log'              => __DIR__.'/classes/log.php',
));