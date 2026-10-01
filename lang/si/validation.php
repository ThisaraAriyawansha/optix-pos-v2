<?php

// Sinhala validation messages. Rules not listed here fall back to English.

return [
    'boolean'   => ':attribute ක්ෂේත්‍රය සත්‍ය හෝ අසත්‍ය විය යුතුය.',
    'confirmed' => ':attribute තහවුරු කිරීම නොගැළපේ.',
    'email'     => ':attribute වලංගු ඊමේල් ලිපිනයක් විය යුතුය.',
    'exists'    => 'තෝරාගත් :attribute වලංගු නොවේ.',
    'in'        => 'තෝරාගත් :attribute වලංගු නොවේ.',
    'max'       => [
        'numeric' => ':attribute :max ට වඩා වැඩි නොවිය යුතුය.',
        'string'  => ':attribute අක්ෂර :max ට වඩා වැඩි නොවිය යුතුය.',
        'file'    => ':attribute කිලෝබයිට් :max ට වඩා වැඩි නොවිය යුතුය.',
        'array'   => ':attribute අයිතම :max ට වඩා වැඩි නොවිය යුතුය.',
    ],
    'min'       => [
        'numeric' => ':attribute අවම වශයෙන් :min විය යුතුය.',
        'string'  => ':attribute අවම වශයෙන් අක්ෂර :min ක් විය යුතුය.',
        'file'    => ':attribute අවම වශයෙන් කිලෝබයිට් :min ක් විය යුතුය.',
        'array'   => ':attribute අවම වශයෙන් අයිතම :min ක් තිබිය යුතුය.',
    ],
    'required'  => ':attribute ක්ෂේත්‍රය අවශ්‍යයි.',
    'string'    => ':attribute පෙළක් විය යුතුය.',
    'unique'    => ':attribute දැනටමත් භාවිතා කර ඇත.',

    'attributes' => [
        'name'              => 'නම',
        'email'             => 'ඊමේල්',
        'password'          => 'මුරපදය',
        'phone'             => 'දුරකථනය',
        'phone_number'      => 'දුරකථන අංකය',
        'address'           => 'ලිපිනය',
        'description'       => 'විස්තරය',
        'nic'               => 'ජා.හැ.අ.',
        'branch_id'         => 'ශාඛාව',
        'role_id'           => 'භූමිකාව',
        'main_contact'      => 'ප්‍රධාන දුරකථන අංකය',
        'secondary_contact' => 'ද්විතීයික දුරකථන අංකය',
        'status'            => 'තත්ත්වය',
        'is_active'         => 'තත්ත්වය',
    ],
];
