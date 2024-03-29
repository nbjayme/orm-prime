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
      'groupfilter' => '', // use having
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

    public function Clear()
    {
        $this->props = [
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
          'groupfilter' => '', // use having
        ];
        return $this;
    }

    public function toArray()
    {
      return $this->props;
    }

    public function fromArray($options)
    {
      $this->Clear();
      $this->props = array_replace($this->props,
        array_intersect_key($options, $this->props));
    }

    public function __destruct()
    {
    }
};
