<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('users', function (Blueprint $t) {
      $t->id();
      $t->string('name');
      $t->string('email')->unique();
      $t->string('phone')->nullable();
      $t->string('whatsapp')->nullable();
      $t->string('password_hash');
      $t->string('role')->default('student');
      $t->timestamps();
      $t->softDeletes();
    });
    
    Schema::create('courses', function (Blueprint $t) {
      $t->id();
      $t->string('title');
      $t->text('summary')->nullable();
      $t->integer('price')->default(0);
      $t->string('currency',8)->default('INR');
      $t->string('cover_url')->nullable();
      $t->string('status')->default('published');
      $t->timestamps();
      $t->softDeletes();
    });
    
    Schema::create('preorders', function (Blueprint $t) {
      $t->id();
      $t->enum('product_type',['course','ebook']);
      $t->unsignedBigInteger('product_id');
      $t->string('name');
      $t->string('email')->index();
      $t->string('mobile')->nullable();
      $t->string('whatsapp')->nullable();
      $t->string('razorpay_order_id')->unique()->nullable();
      $t->integer('amount')->default(0);
      $t->string('currency',8)->default('INR');
      $t->string('status')->default('created');
      $t->json('consents_json')->nullable();
      $t->json('utm_json')->nullable();
      $t->string('ip')->nullable();
      $t->string('ua')->nullable();
      $t->timestamps();
    });
    
    Schema::create('payments', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('preorder_id')->nullable();
      $t->string('provider')->default('razorpay');
      $t->string('provider_order_id')->unique();
      $t->string('provider_payment_id')->nullable();
      $t->integer('amount')->default(0);
      $t->string('currency',8)->default('INR');
      $t->string('status')->default('captured');
      $t->json('webhook_payload')->nullable();
      $t->timestamps();
    });
    
    Schema::create('enrollments', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('user_id');
      $t->unsignedBigInteger('course_id');
      $t->string('status')->default('active');
      $t->timestamp('started_at')->nullable();
      $t->timestamp('completed_at')->nullable();
      $t->timestamps();
      $t->unique(['user_id','course_id']);
    });
    
    Schema::create('entitlements', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('user_id');
      $t->unsignedBigInteger('ebook_id');
      $t->string('status')->default('active');
      $t->timestamp('granted_at')->nullable();
      $t->timestamp('revoked_at')->nullable();
      $t->timestamps();
      $t->unique(['user_id','ebook_id']);
    });
    
    Schema::create('certificates', function (Blueprint $t) {
      $t->id();
      $t->unsignedBigInteger('user_id');
      $t->unsignedBigInteger('course_id');
      $t->string('cert_number')->unique();
      $t->string('pdf_url')->nullable();
      $t->timestamp('issued_at')->nullable();
      $t->timestamp('revoked_at')->nullable();
      $t->string('revoke_reason')->nullable();
      $t->timestamps();
    });
  }
  
  public function down(): void {
    Schema::dropIfExists('certificates');
    Schema::dropIfExists('entitlements');
    Schema::dropIfExists('enrollments');
    Schema::dropIfExists('payments');
    Schema::dropIfExists('preorders');
    Schema::dropIfExists('courses');
    Schema::dropIfExists('users');
  }
};