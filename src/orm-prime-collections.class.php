<?php

class OrmPrimeCollections {

  private $collections = [];

  public function __construct($collections)
  {
      $this->collections = $collections;
  }

  public function toJSON()
  {
    $result = $this->toArray();
    return json_encode($result);
  }

  public function toArray()
  {
    $result = $this->EachRow(function($rs)
    {
      return $rs->toArray();
    });
    return $result;
  }

  public function Length()
  {
    return count($this->collections);
  }

  public function getIndex($nIndex)
  {
    $numRows = $this->Length();
    if ($nIndex >= $numRows)
    {
      return NULL;
    }
    return $this->collections[$nIndex];
  }

  public function Delete()
  {
    if ($this->Length() == 0)
    {
      return;
    }
    $collections = $this->collections;
    foreach($collections as $rs)
    {
      $rs->Delete();
    }
    $this->collections = NULL;
  }

  public function EachRow($handler)
  {
    if ($this->Length() == 0)
    {
      return [];
    }

    $collections = $this->collections;
    $items = [];
    foreach($collections as $rs)
    {
      $val = $handler($rs);
      if (is_null($val))
      {
        continue;
      }
      $items[] = $val;
    }
    return $items;
  }

}
