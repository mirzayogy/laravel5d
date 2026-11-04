<?php

// Indonesian messages for the validation rules this app uses.
return [
    'accepted' => ':attribute harus diterima.',
    'array' => ':attribute harus berupa array.',
    'before_or_equal' => ':attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between' => [
        'numeric' => ':attribute harus di antara :min dan :max.',
        'string' => ':attribute harus di antara :min dan :max karakter.',
    ],
    'boolean' => ':attribute harus bernilai benar atau salah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_format' => ':attribute tidak sesuai format :format.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'in' => ':attribute yang dipilih tidak valid.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'max' => [
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
        'array' => ':attribute tidak boleh lebih dari :max item.',
    ],
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
        'array' => ':attribute minimal :min item.',
    ],
    'numeric' => ':attribute harus berupa angka.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'unique' => ':attribute sudah dipakai.',
    'password' => [
        'letters' => ':attribute harus mengandung setidaknya satu huruf.',
        'mixed' => ':attribute harus mengandung huruf besar dan kecil.',
        'numbers' => ':attribute harus mengandung setidaknya satu angka.',
        'symbols' => ':attribute harus mengandung setidaknya satu simbol.',
        'uncompromised' => ':attribute muncul dalam kebocoran data. Pilih :attribute yang berbeda.',
    ],

    'attributes' => [
        'name' => 'nama', 'email' => 'email', 'password' => 'kata sandi', 'password_confirmation' => 'konfirmasi kata sandi',
        'category_id' => 'kategori', 'target_count' => 'target harian', 'unit' => 'satuan', 'description' => 'deskripsi', 'tags' => 'tag',
        'timezone' => 'zona waktu', 'daily_goal' => 'target harian', 'bio' => 'bio', 'remind_at' => 'waktu', 'days_of_week' => 'hari',
        'entry_date' => 'tanggal', 'title' => 'judul', 'content' => 'isi catatan', 'mood_level' => 'suasana hati', 'note' => 'catatan',
        'role' => 'peran', 'color' => 'warna', 'icon' => 'ikon', 'criteria_type' => 'jenis target', 'criteria_threshold' => 'jumlah',
        'reminder_time' => 'waktu pengingat', 'locale' => 'bahasa',
    ],
];
