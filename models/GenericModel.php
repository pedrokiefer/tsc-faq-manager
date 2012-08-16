<?php
/**
 * User: Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/7/12
 * Time: 1:47 PM
 */
class GenericModel
{
    protected $validationMessages;

    public function getValidationMessages()
    {
        return $this->validationMessages;
    }

    public function __get($name)
    {

        if (method_exists($this, 'get' . $name))
            return $this->{'get' . $name}();

        if (property_exists($this, '_' . $name))
            return $this->{'_' . $name};
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->readonly())) {
            $backtrace = debug_backtrace();
            wp_die('<strong>Error:</strong>' . get_class($backtrace[0]['object']) . '::' . $name
                . ' is read-only. <br /><strong>' . $backtrace[0]['file']
                . '</strong> on line <strong>' . $backtrace[0]['line'] . '</strong>');
            exit();
        }

        if (method_exists($this, 'set' . $name)) {
            return $this->{'set' . $name}($value);
        }

        if (property_exists($this, '_' . $name)) {
            $this->{'_' . $name} = $value;
        }
    }

    protected function readonly() {
        return array();
    }
}
