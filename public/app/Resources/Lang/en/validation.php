<?php

return [
    'accepted' => 'The {:attribute} must be accepted.',
    'after' => 'The {:attribute} must be a date after {:date}.',
    'after_or_equal' => 'The {:attribute} must be a date after or equal to {:date}.',
    'boolean' => 'The {:attribute} field must be true or false.',
    'before' => 'The {:attribute} must be a date before {:date}.',
    'before_or_equal' => 'The {:attribute} must be a date before or equal to {:date}.',
    'required' => 'The {:attribute} field is required.',
    'confirmed' => 'The {:attribute} confirmation does not match.',
    'mimes' => 'The {:attribute} must be a file of type: {:values}.',
    'mimetypes' => 'The {:attribute} must be a file of type: {:values}.',
    'min' => [
        'numeric' => 'The {:attribute} must be at least {:min}.',
        'string' => 'The {:attribute} must be at least {:min} characters.',
        'file' => 'The {:attribute} must be at least {:min} kilobytes.',
        'array' => 'The {:attribute} must have at least {:min} items.'
     ],
    'max' => [
        'numeric' => 'The {:attribute} may not be greater than {:max}.',
        'string' => 'The {:attribute} may not be greater than {:max} characters.',
        'file' => 'The {:attribute} may not be greater than {:max} kilobytes.',
        'array' => 'The {:attribute} may not have more than {:max} items.'
    ],
    'dimensions' => 'The {:attribute} has invalid image dimensions.',
    'exists' => 'The selected {:attribute} is invalid.',
    'email' => 'The {:attribute} must be a valid email address.',
    'username' => 'The {:attribute} must be a valid username.',
    'url' => 'The {:attribute} format is invalid.',
    'unique' => 'The {:attribute} has already been taken.',
    'no_whitespace' => 'The {:attribute} can\'t have spaces.',
    'singleline' => 'The {:attribute} can\'t have line-breaks.',
    'one_whitespace' => 'The {:attribute} can only have one space between every word.',
    'length' => 'The {:attribute} length must be {:length}.',
    'length_between' => 'The {:attribute} length must be between {:min} and {:max}.',
    'date_format' => 'The {:attribute} does not match the format {:format}.',
    'between' => [
        'numeric' => 'The {:attribute} must be between {:min} and {:max}.',
        'file' => 'The {:attribute} must be between {:min} and {:max} kilobytes.',
        'string' => 'The {:attribute} must be between {:min} and {:max} characters.',
        'array' => 'The {:attribute} must have between {:min} and {:max} items.',
    ],
    'integer' => 'The {:attribute} must be an integer.',
    'float' => 'The {:attribute} must be a float.',
    'array' => 'The {:attribute} must be an array.',
    'string' => 'The {:attribute} must be a string.',
    'file' => 'The {:attribute} must be a file.',
    'file_array' => 'The {:attribute} must be a file.',
    'invalid_type' => 'The {:attribute} must be a valid input.',
    'image' => 'The {:attribute} must be an image.',
    'json' => 'The {:attribute} must be a valid JSON string.',


    /**
     * Custom validation attributes
     */
    'attributes' => [
        'email' => 'E-mail address',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'password' => 'Password'
    ],

    'nested' => [
        'attributes' => [
            'name' => [
                'first' => 'First Name',
                'last' => 'Last Name'
            ]
        ],
    ]
];
