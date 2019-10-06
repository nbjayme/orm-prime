<?php
class OrmPrimeInstance {
  private static $instance = FALSE;
  private $connections = [];

  private function __construct()
  {
  }
  private function __clone()
  {
  }
  private function __wakeup()
  {
  }

  public static function getInstance()
  {
    if (!empty(self::$instance))
    {
      return self::$instance;
    }
    self::$instance = (new OrmPrimeInstance());
    return self::$instance;
  }

  public function AddProfile($key, $profile)
  {
    $profileDefaults = [
      'dialect' => 'mysql',
      'host' => '',
      'user' => '',
      'password' => '',
      'database' => '',
    ];
    $profile = array_replace($profileDefaults,
      array_intersect_key($profile, $profileDefaults));

    $instance = OrmPrimeInstance::getInstance();
    if($profile['dialect'] == 'mysql')
    {
      $ormSpeak = new OrmPrimeSpeakMysql();
    }

    $connString = $ormSpeak->Connect($profile);
    try {
      $conn = new PDO($connString, $profile['user'], $profile['password']);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $conn->prepare('SET NAMES utf8');
      $stmt->execute();
      $instance->connections[$key] = new OrmPrimeConnection($conn, $ormSpeak);
    }
    catch (PDOException $e)
    {
      return FALSE;
    }

    return $conn;
  }

  public function getProfile($key)
  {
    $instance = OrmPrimeInstance::getInstance();
    if (!isset($instance->connections[$key]))
    {
      return NULL;
    }
    return $instance->connections[$key];
  }

  public function __destruct()
  {
    $instance = OrmPrimeInstance::getInstance();
    foreach($instance->connections as $p)
    {
      $p = null;
    }
    $instance->connections = [];
  }

}
