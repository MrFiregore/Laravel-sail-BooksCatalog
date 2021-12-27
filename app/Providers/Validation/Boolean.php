<?php


    namespace App\Providers\Validation;


    class Boolean
    {
        /**
         * Validates a phone number.
         *
         * @param   string   $attribute
         * @param   mixed    $value
         * @param   array    $parameters
         * @param   object   $validator
         *
         * @return bool
         */
        public function validate ($attribute, $value, array $parameters, $validator)
        {

            $bool = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            return $bool !== null && is_bool($bool);
        }
    }
