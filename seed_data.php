<?php
require __DIR__ . '/backend/vendor/autoload.php';

$app = require __DIR__ . '/backend/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$now = date('Y-m-d H:i:s');

DB::table('courses')->insert([
  [
    'title'=>'Angular Mastery Course',
    'summary'=>'Complete Angular course from basics to advanced topics. Build real-world applications with Angular 17.',
    'price'=>49900,
    'currency'=>'INR',
    'cover_url'=>'https://via.placeholder.com/400x300/6366F1/FFFFFF?text=Angular+Mastery',
    'status'=>'published',
    'created_at'=>$now,
    'updated_at'=>$now
  ],
  [
    'title'=>'PHP & Lumen API Development',
    'summary'=>'Build high-performance REST APIs with PHP and Lumen microframework. Includes authentication and payment integration.',
    'price'=>39900,
    'currency'=>'INR',
    'cover_url'=>'https://via.placeholder.com/400x300/8B5CF6/FFFFFF?text=PHP+Lumen+API',
    'status'=>'published',
    'created_at'=>$now,
    'updated_at'=>$now
  ],
  [
    'title'=>'Full Stack E-Learning Platform',
    'summary'=>'Build a complete e-learning platform with payment integration, user management, and course delivery.',
    'price'=>99900,
    'currency'=>'INR',
    'cover_url'=>'https://via.placeholder.com/400x300/7C3AED/FFFFFF?text=Full+Stack',
    'status'=>'published',
    'created_at'=>$now,
    'updated_at'=>$now
  ]
]);

echo "Demo courses seeded successfully!\n";
