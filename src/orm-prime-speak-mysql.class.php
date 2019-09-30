<?php

class OrmPrimeSpeakMysql implements iOrmPrimeSpeak {
  private $dialect = 'mysql';

  public function __construct()
  {
  }

  public function Connect($opts=[])
  {
      $optDefaults = [
        'host' => '',
        'database' => '',
      ];
      foreach($opts as $k => $v)
      {
        if (!isset($optDefaults[$k]))
        {
          continue;
        }
        $optDefaults[$k] = $v;
      }
      $opts = $optDefaults;

      return "mysql:host=" . $opts['host'] . ';'
        . "dbname=" . $opts['database'];
  }

  public function Insert($opts = [])
  {
    $optDefaults = [
      'table' => '',
      'fields' => [],
    ];
    foreach($opts as $k => $v)
    {
      if (!isset($optDefaults[$k]))
      {
        continue;
      }
      $optDefaults[$k] = $v;
    }
    $opts = $optDefaults;

    $keys = [];
    $tKeys = [];
    foreach($opts['fields'] as $k => $v)
    {
      $keys[] = $k;
      $tKeys[] = ':' . $k;
    }
    $sql =  'INSERT INTO ' . $opts['table']
       . '(' . implode(', ', $keys) . ')'
       . ' VALUES '
       . '(' . implode(', ', $tKeys) . ');';
     return $sql;
  }

  public function Update($opts = [])
  {
    $optDefaults = [
      'table' => '',
      'fields' => [],
      'where' => ''
    ];
    foreach($opts as $k => $v)
    {
      if (!isset($optDefaults[$k]))
      {
        continue;
      }
      $optDefaults[$k] = $v;
    }
    $opts = $optDefaults;

    $keys = [];
    foreach($opts['fields'] as $k => $v)
    {
      $keys[] = $k . ' = :' . $k;
    }

    $sql =  'UPDATE ' . $opts['table'] . ' '
      . 'SET '
      . implode(', ', $keys) . ' ';
    if ($opts['where'] !== '')
    {
      $sql .= 'WHERE ' . $opts['where'] . ' ';
    }
    $sql .= ';';

    return $sql;
  }

  public function Select($opts = [])
  {
    $optDefaults = [
      'table' => '',
      'fields' => ['*'],
      'where' => '',
      'group' => [],
      'having' => '',
      'order' => [],
      'page' => '*',
      'rows' => 50,
      'limit' => '*',
    ];
    foreach($opts as $k => $v)
    {
      if (!isset($optDefaults[$k]))
      {
        continue;
      }
      $optDefaults[$k] = $v;
    }
    $opts = $optDefaults;

    $sql = 'SELECT '  . implode(', ', $opts['fields']) . ' '
      . 'FROM ' . $opts['table'] . ' ';
    if ($opts['where'] !== '')
    {
      $sql .= 'WHERE ' .  $opts['where'] . ' ';
    }
    if (!empty($opts['group']))
    {
      $sql .= 'GROUP BY ' . implode(', ', $opts['group']) . ' ';
    }
    if ($opts['having'] !== '')
    {
      $sql .= 'HAVING ' . $opts['having'] . ' ';
    }
    if(!empty($opts['order']))
    {
      $sql .= 'ORDER BY ' . implode(', ', $opts['order']) . ' ';
    }
    if($opts['page'] !== '*')
    {
      $pg = $opts['page'];
      $pg --;
      if ($pg < 0)
      {
        $pg = 0;
      }
      $startRow =$pg * $opts['rows'];
      $sql .= 'LIMIT ' . $startRow . ', ' . $opts['rows'];
    }else if ($opts['limit'] !== '*')
    {
      $sql .= 'LIMIT ' . $opts['limit'];
    }
    return $sql . ';';
  }

  public function Delete($opts= [])
  {
    $optDefaults = [
      'table' => '',
      'where' => '',
      'limit' => '*',
    ];
    foreach($opts as $k => $v)
    {
      if (!isset($optDefaults[$k]))
      {
        continue;
      }
      $optDefaults[$k] = $v;
    }
    $opts = $optDefaults;

    $sql =  'DELETE FROM ' . $opts['table'] . ' ';
    if ($opts['where'] !== '')
    {
      $sql .= 'WHERE ' . $opts['where'] . ' ';
    }
    if ($opts['limit'] !== '*')
    {
      $sql .= 'LIMIT ' . $opts['limit'] . ' ';
    }
    $sql .= ';';
    return $sql;
  }

  public function Truncate($opts = [])
  {
    $optDefaults = [
      'table' => ''
    ];
    foreach($opts as $k => $v)
    {
      if (!isset($optDefaults[$k]))
      {
        continue;
      }
      $optDefaults[$k] = $v;
    }
    $opts = $optDefaults;
    $sql =  'TRUNCATE ' . $opts['table'] . ';';
    return $sql;
  }

  public function Count($opts = [])
  {
    $optDefaults = [
      'table' => '',
      'fields' => ['COUNT(*) as cnt'],
      'where' => '',
    ];
    foreach($opts as $k => $v)
    {
      if (!isset($optDefaults[$k]))
      {
        continue;
      }
      $optDefaults[$k] = $v;
    }
    $opts = $optDefaults;
    $sql = "SELECT " . implode(', ', $opts['fields']) . " "
      . "FROM " . $opts['table'] . " ";
    if($opts['where'] !== '')
    {
      $sql .= "WHERE " . $opts['where'];
    }
    $sql .= ";";
    return $sql;
  }

};
