<?php

namespace NomadTaxCalc\Theme\Traits;

trait Singleton
{
  private static $instance = [];

  public static function getInstance()
  {
    $called_class = get_called_class();
    if (!isset(self::$instance[$called_class])) {
      self::$instance[$called_class] = new $called_class();
    }
    return self::$instance[$called_class];
  }

  /**
   * Disabled constructor.
   *
   * @codeCoverageIgnore
   */
  private function __construct() {}
  /**
   * Disabled clone.
   *
   * @codeCoverageIgnore
   */
  protected function __clone() {}
}
