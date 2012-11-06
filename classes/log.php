<?php
namespace Loggly;

class Log extends \Fuel\Core\Log
{

  public static function _init()
  {
    \Config::load('loggly', true);

    parent::_init();
  }

  /**
   * Write Log File
   *
   * Generally this function will be called using the global log_message() function
   *
   * @access  public
   * @param int|string  the error level
   * @param string  the error message
   * @param string  information about the method
   * @return  bool
   */
  public static function write($level, $msg, $method = null)
  {
    // defined default error labels
    static $labels = array(
      1  => 'Error',
      2  => 'Warning',
      3  => 'Debug',
      4  => 'Info',
    );

    // get the levels defined to be logged
    $loglabels = \Config::get('log_threshold');

    // bail out if we don't need logging at all
    if ($loglabels == \Fuel::L_NONE)
    {
      return false;
    }

    // if it's not an array, assume it's an "up to" level
    if ( ! is_array($loglabels))
    {
      $loglabels = array_keys(array_slice($labels, 0, $loglabels, true));
    }

    // if $level is string, it is custom level.
    if (is_int($level))
    {
      // do we need to log the message with this level?
      if ( ! in_array($level, $loglabels))
      {
        return false;
      }

      // store the label for this level for future use
      $level = $labels[$level];
    }

    // if profiling is active log the message to the profile
    if (\Config::get('profiling'))
    {
      \Console::log($method.' - '.$msg);
    }

    // and write it to the logfile
    $call = '';
    if ( ! empty($method))
    {
      $call .= $method;
    }

    $message = array();
    $message['severity']       = $level;
    $message['server_date'] = date(\Config::get('log_date_format'));
    $message['message']     = (empty($call) ? '' : $call.' - ').$msg;

    $url = "https://logs.loggly.com/inputs/".\Config::get('loggly.input_key');

    $json = json_encode($message);

    $params = array(
    ); // header params

    $options = array(
      'HTTPHEADER' => array('Content-Type' => 'application/json'),
      'POSTFIELDS' => $json,
    ); // curl options

    $response = \Request::forge(
     $url,
     array(
     'driver' => 'curl',
     'params' => $params,
     'options' => $options),
     'POST' // the method, GET, POST, etc.
    );

    //die(var_dump($response));

    //die(var_dump($response->execute()));


    return true;
  }
}