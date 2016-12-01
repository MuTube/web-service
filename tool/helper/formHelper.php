<?php

class FormHelper {
    //init
    function __construct($values = []) {
        $this->defineFields();
        $this->loadValues($values);
    }

    //properties
    protected $fields;
    protected $defaultValues;
    protected $values = [];

    //actions
    public function loadValues($values = []) {
        $values = $this->prepareValues($values);

        foreach($this->fields as $field) {
            if(array_key_exists($field, $values)) {
                $this->values[$field] = $values[$field];
            }
        }
    }

    public function getValues() {
        return $this->values;
    }

    protected function defineFields() {
        $this->fields = [];
    }

    protected function prepareValues($values) {
        //prepare form values

        return $values;
    }
}