<?php

class OrmPrimeRowset {

  private $table = "";
  private $fields = [];
  private $connProfile = null;
  private $ormSpeak = null;
  private $connection = null;

  public function __construct($arrOptions, $connProfile)
  {
      $this->connProfile = $connProfile;
      $this->connection = & $connProfile['connection'];
      $this->ormSpeak = & $connProfile['ormSpeak'];
      $this->table = & $arrOptions['table'];
      $this->fields = & $arrOptions['fields']; // with values
  }

  public function __set($name, $value)
  {
    if (!isset($this->fields[$name]))
    {
      return;
    }
    if (is_callable($this->fields[$name]))
    {
      return;
    }
    $this->fields[$name]['value'] = $value;
  }

  public function __get($name)
  {
    if (!isset($this->fields[$name]))
    {
      return NULL;
    }
    if (is_callable($this->fields[$name]))
    {
      return $this->fields[$name]($this->fields);
    }
    return $this->fields[$name]['value'];
  }

  public function toArray()
  {
    $arr = [];
    foreach($this->fields as $k => $v)
    {
      if (is_callable($v))
      {
        $arr[$k] = $v($this->fields);
      }
      $arr[$k] = $v['value'];
    }
    return $arr;
  }

  public function fromArray($arrValues)
  {
    foreach($arrValues as $k => $v)
    {
      if (!isset($this->fields[$k]))
      {
        continue;
      }
      if (is_callable($this->fields[$k]))
      {
        continue;
      }
      $this->fields[$k]['value'] = $v;
    }
  }

  public function toJSON()
  {
    return json_encode($this->toArray());
  }

  private function OrmSpeak()
  {
    return $this->ormSpeak;
  }

  public function Save()
  {
    $tokens = [];
    $tokensWhere = [];
    $fields = [];

    foreach($this->fields as $k => $v)
    {
      if (is_callable($v))
      {
        continue;
      }
      if (empty($v['is_key']))
      {
        $fields[$k] = $v['value'];
        continue;
      }
      $tokens[$k] = $v['value'];
      $tokensWhere[] = $k . ' = :' . $k;
    }

    $ormFilter = (new OrmPrimeFilter());
    $ormFilter->Where(implode(' AND ', $tokensWhere));
    $opts = $ormFilter->toArray();
    $opts['fields'] =  $fields;
    $opts['table'] = $this->table;
    $cmd = $this->ormSpeak->Update($opts);
    $stmt = $this->connection->prepare($cmd);
    $stmt->execute($tokens + $fields);
  }

  public function Delete()
  {
    $tokens = [];
    $tokensWhere = [];
    foreach($this->fields as $k => $v)
    {
      if (is_callable($v))
      {
        continue;
      }
      if (empty($v['is_key']))
      {
        continue;
      }
      $tokens[$k] = $v['value'];
      $tokensWhere[] = $k . ' = :' . $k;
    }

    $ormFilter = (new OrmPrimeFilter());
    $ormFilter->Where(implode(' AND ', $tokensWhere));
    $opts = $ormFilter->toArray();
    $opts['table'] = $this->table;
    $cmd = $this->ormSpeak->Delete($opts);
    $stmt = $this->connection->prepare($cmd);
    $stmt->execute($tokens);
  }

  public function __destruct()
  {
    $this->arrOptions = null;
  }

}