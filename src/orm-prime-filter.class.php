<?php
class OrmPrimeFilter{

    private  $props = [
      'fields' => ['*'],
      'tokens' => [],
      'where' => '',
      'group' => [],
      'having' => '',
      'order' => [],
      'limit' => '*',
      'page' => '*',
      'rows' => 1,
      //deprecated
      'limit' => '*',
      'groupfilter' => ''
    ];

    public function __construct()
    {
    }

    public function __call($name, $arguments)
    {
        $name = strtolower($name);
        if (!isset($this->props[$name]))
        {
          return $this;
        }
        $this->props[$name] = $arguments[0];
        return $this;
    }

    public function __set($name, $value)
    {
        if (is_null($value))
        {
            return;
        }
        $name = strtolower($name);
        if (!isset($this->props[$name]))
        {
          return;
        }
        $this->props[$name] = $value;
        if ($name == 'having')
        {
          $this->props['groupfilter'] = $value;
        }
    }

    public function __get($name)
    {
        $name = strtolower($name);
        if(!isset($this->props[$name]))
        {
          return NULL;
        }
        return $this->props[$name];
    }

    public function toArray()
    {
      return $this->props;
    }

    public function fromArray($options)
    {
      foreach($options as $k => $v)
      {
        $this->{$k} = $v;
      }
    }

    public function __destruct()
    {
    }
};
