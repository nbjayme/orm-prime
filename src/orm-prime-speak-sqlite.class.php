<?php

class OrmPrimeSpeakSqlite implements iOrmPrimeSpeak {
  private $dialect = 'sqlite';

  public function __construct()
  {
  }

  public function Connect($opts=[])
  {
      $optDefaults = [
        'database' => '',
      ];
      $opts = array_replace($optDefaults, array_intersect_key($opts, $optDefaults));

      return "sqlite:" . $opts['database'];
  }

  public function Insert($opts = [])
  {
    $optDefaults = [
      'table' => '',
      'fields' => [],
    ];
    $opts = array_replace($optDefaults, array_intersect_key($opts, $optDefaults));

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
    $opts = array_replace($optDefaults, array_intersect_key($opts, $optDefaults));

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
    $opts = array_replace($optDefaults, array_intersect_key($opts, $optDefaults));

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
    $opts = array_replace($optDefaults, array_intersect_key($opts, $optDefaults));

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
    $opts = array_replace($optDefaults, array_intersect_key($opts, $optDefaults));

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
    $opts = array_replace($optDefaults, array_intersect_key($opts, $optDefaults));

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
