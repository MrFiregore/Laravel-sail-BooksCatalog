<?php

    namespace App\Http\Requests;

    use Illuminate\Foundation\Http\FormRequest;

    class DooRequest extends FormRequest
    {
        /**
         * Determine if the user is authorized to make this request.
         *
         * @return bool
         */
        public function authorize()
        {
            return true;
        }

        /**
         * Get the validation rules that apply to the request.
         *
         * @return array
         */
        public function rules()
        {
            return [
                'all'    => 'nullable|trueboolean',
                'genres' => 'nullable|exists:App\Models\Genre,id',
            ];
        }

        protected function passedValidation()
        {
            $this->replace(
                [
                    'all'    => filter_var($this->all ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                    'genres' => is_string($this->genres) ? explode(",", $this->genres) : $this->genres,
                ]
            );
        }
    }
