<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'id_about_to_expire' => 'الموظفين التالية اسماؤهم: :employee، ستنتهي هوياتهم خلال شهر',
    'license' => 'استمارات السيارات التالية: :cars، ستنتهي خلال شهر',
    'periodic_examination' => 'الفحص الدوري للسيارات التالية: :cars سينتهي خلال شهر',
    'accepted' => 'يجب قبول :attribute',
    'accepted_if' => 'يجب قبول :attribute عندما الآخر هو :value.',
    'active_url' => ':attribute لا يُمثّل رابطًا صحيحًا',
    'after' => 'يجب على :attribute أن يكون تاريخًا لاحقًا للتاريخ :date.',
    'after_or_equal' => ':attribute يجب أن يكون تاريخاً لاحقاً أو مطابقاً للتاريخ :date.',
    'alpha' => 'يجب أن لا يحتوي :attribute سوى على حروف',
    'alpha_dash' => 'يجب أن لا يحتوي :attribute على حروف، أرقام ومطّات.',
    'alpha_num' => 'يجب أن يحتوي :attribute على حروفٍ وأرقامٍ فقط',
    'array' => 'يجب أن يكون :attribute ًمصفوفة',
    'before' => 'يجب على :attribute أن يكون تاريخًا سابقًا للتاريخ :date.',
    'before_or_equal' => ':attribute يجب أن يكون تاريخا سابقا أو مطابقا للتاريخ :date',
    'between' => [
        'array' => 'يجب أن يحتوي :attribute على عدد من العناصر بين :min و :max',
        'file' => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'string' => 'يجب أن يكون عدد حروف النّص :attribute بين :min و :max',
    ],
    'boolean' => 'يجب أن تكون قيمة :attribute إما true أو false ',
    'confirmed' => 'حقل التأكيد غير مُطابق للحقل :attribute',
    'current_password' => 'The password is incorrect.',
    'date' => ':attribute ليس تاريخًا صحيحًا',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => 'لا يتوافق :attribute مع الشكل :format.',
    'declined' => 'The :attribute must be declined.',
    'declined_if' => 'The :attribute must be declined when :other is :value.',
    'different' => 'يجب أن يكون الحقلان :attribute و :other مُختلفان',
    'digits' => 'يجب أن يحتوي :attribute على :digits رقمًا/أرقام',
    'digits_between' => 'يجب أن يحتوي :attribute بين :min و :max رقمًا/أرقام ',
    'dimensions' => 'الـ :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'للحقل :attribute قيمة مُكرّرة.',
    'doesnt_end_with' => 'قد لا تنتهي :attribute مع أحد ما يلي: :values.',
    'doesnt_start_with' => 'قد لا تبدأ :attribute بأحد ما يلي: :values.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح البُنية',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => 'القيمة المحددة :attribute غير موجودة',
    'file' => 'الـ :attribute يجب أن يكون ملفا.',
    'filled' => ':attribute إجباري',
    'gt' => [
        'array' => 'يجب أن تحتوي :attribute على أكثر من عناصر :value.',
        'file' => 'يجب أن تكون :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون :attribute أكبر من :value.',
        'string' => 'يجب أن تكون :attribute أكبر من أحرف :value.',
    ],
    'gte' => [
        'array' => 'يجب أن يكون لدى :attribute عناصر :value أو أكثر.',
        'file' => 'يجب أن تكون :attribute أكبر من أو تساوي :value كيلوبايت.',
        'numeric' => 'يجب أن تكون :attribute أكبر من أو تساوي :value.',
        'string' => 'يجب أن تكون :attribute أكبر من أو تساوي أحرف :value.',
    ],
    'image' => 'يجب أن يكون :attribute صورةً',
    'in' => ':attribute يحتوي على قيمة غير صالحة',
    'in_array' => ':attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون :attribute عددًا صحيحًا',
    'ip' => 'يجب أن يكون :attribute عنوان IP صحيحًا',
    'ipv4' => 'يجب أن يكون :attribute عنوان IPv4 صحيحًا.',
    'ipv6' => 'يجب أن يكون :attribute عنوان IPv6 صحيحًا.',
    'json' => 'يجب أن يكون :attribute نصآ من نوع JSON.',

    'lt' => [
         'array' => 'يجب أن تحتوي :attribute على أقل من عناصر :value.',
         'file' => 'يجب أن تكون :attribute أقل من :value كيلوبايت.',
         'numeric' => 'يجب أن تكون:attribute أقل من :value.',
         'string' => 'يجب أن تكون :attribute أقل من أحرف :value.',
    ],
    'lte' => [
        'array' => 'يجب ألا تحتوي :attribute على أكثر من: عناصر :value.',
        'file' => 'يجب أن تكون :attribute أقل من أو تساوي :value كيلوبايت.',
        'numeric' => 'يجب أن تكون :attribute أقل من أو تساوي :value.',
        'string' => 'يجب أن تكون :attribute أقل من أو تساوي أحرف :value.',

    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max' => [
        'array' => 'يجب أن لا يحتوي :attribute على أكثر من :max عناصر/عنصر.',
        'file' => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت',
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أصغر لـ :max.',
        'string' => 'يجب أن لا يتجاوز طول النّص :attribute :max حروفٍ/حرفًا',
    ],
    'max_digits' => 'The :attribute must not have more than :max digits.',
    'mimes' => 'يجب أن يكون ملفًا من نوع : :values.',
    'mimetypes' => 'يجب أن يكون ملفًا من نوع : :values.',
    'min' => [
        'array' => 'يجب أن يحتوي :attribute على الأقل على :min عُنصرًا/عناصر',
        'file' => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت',
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر لـ :min.',
        'string' => 'يجب أن يكون طول النص :attribute على الأقل :min حروفٍ/حرفًا',
    ],

    'min_digits' => 'The :attribute must have at least :min digits.',
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => ':attribute لاغٍ',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'يجب على :attribute أن يكون رقمًا',
    'password' => [
        'letters' => 'يجب أن تحتوي :attribute على حرف واحد على الأقل.',
        'mixed' => 'يجب أن تحتوي :attribute على أحرف كبيرة واحدة على الأقل وحرف صغيرة واحدة.',
        'numbers' => 'يجب أن تحتوي :attribute على رقم واحد على الأقل.',
        'symbols' => 'يجب أن تحتوي :attribute على رمز واحد على الأقل.',
        'uncompromised' => 'المعطى ظهرت :attribute في تسرب البيانات.الرجاء اختيار مختلف :attribute.',
        ],
    'present' => 'يجب تقديم :attribute',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => 'صيغة :attribute .غير صحيحة',
    'required' => ':attribute مطلوب.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => ':attribute مطلوب في حال ما إذا كان :other يساوي :value.',
    'required_unless' => ':attribute مطلوب في حال ما لم يكن :other يساوي :values.',
    'required_with' => ':attribute مطلوب إذا توفّر :values.',
    'required_with_all' => ':attribute مطلوب إذا توفّر :values.',
    'required_without' => ':attribute مطلوب إذا لم يتوفّر :values.',
    'required_without_all' => ':attribute مطلوب إذا لم يتوفّر :values.',
    'same' => 'يجب أن يتطابق :attribute مع :other',
    'size' => [
        'array' => 'يجب أن يحتوي :attribute على :size عنصرٍ/عناصر بالظبط',
        'file' => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت',
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية لـ :size',
        'string' => 'يجب أن يحتوي النص :attribute على :size حروفٍ/حرفًا بالظبط',
    ],
    'starts_with' => ':attribute يجب أن يبدأ بـ: :values',
    'string' => 'يجب أن يكون :attribute نصآ.',
    'timezone' => 'يجب أن يكون :attribute نطاقًا زمنيًا صحيحًا',
    'unique' => 'قيمة :attribute مُستخدمة من قبل',
    'uploaded' => 'فشل في تحميل الـ :attribute',
    'url' => 'صيغة الرابط :attribute غير صحيحة',
    'uuid' => 'يجب أن تكون :attribute uuid صالحة.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [

    ],
    'phone' => 'يحتوي حقل :attribute على رقم غير صالح.',

];
