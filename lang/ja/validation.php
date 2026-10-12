<?php

return [
    'required' => ':attributeを入力してください',
    'string' => ':attributeは文字列で入力してください',
    'email' => ':attributeは正しいメールアドレス形式で入力してください',
    'lowercase' => ':attributeは小文字で入力してください',
    'integer' => ':attributeは整数で入力してください',
    'exists' => '選択した:attributeは正しくありません',
    'unique' => 'この:attributeはすでに使われています',
    'enum' => '選択した:attributeは正しくありません',
    'confirmed' => ':attributeが確認用の入力と一致しません',
    'current_password' => ':attributeが正しくありません',
    'min' => [
        'numeric' => ':attributeは:min以上で入力してください',
        'string' => ':attributeは:min文字以上で入力してください',
    ],
    'max' => [
        'numeric' => ':attributeは:max以下で入力してください',
        'string' => ':attributeは:max文字以内で入力してください',
    ],

    'attributes' => [
        'price' => '月額料金',
        'streaming_service_id' => '配信サービス',
        'status' => 'ステータス',
        'q' => '検索キーワード',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード（確認）',
        'name' => '名前',
    ],
];
