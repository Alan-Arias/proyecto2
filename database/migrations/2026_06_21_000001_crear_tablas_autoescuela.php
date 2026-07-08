<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'role_id']);
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menus')->nullOnDelete();
            $table->string('nombre');
            $table->string('ruta')->nullable();
            $table->string('icono')->nullable();
            $table->unsignedSmallInteger('orden')->default(1);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['menu_id', 'role_id']);
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('cedula')->unique();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();
        });

        Schema::create('instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('cedula')->unique();
            $table->string('telefono')->nullable();
            $table->string('especialidad')->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->unsignedSmallInteger('horas')->default(1);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2)->default(0);
            $table->unsignedSmallInteger('duracion_horas')->default(1);
            $table->string('tipo_licencia');
            $table->string('tema_visual')->default('adultos');
            $table->string('estado')->default('disponible');
            $table->timestamps();
        });

        Schema::create('course_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['course_id', 'subject_id']);
        });

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('instructors')->nullOnDelete();
            $table->string('codigo')->unique();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->unsignedSmallInteger('cupos')->default(10);
            $table->string('estado')->default('activo');
            $table->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->date('fecha_inscripcion');
            $table->string('estado_academico')->default('inscrito');
            $table->string('estado_pago')->default('pendiente');
            $table->decimal('precio_total', 10, 2)->default(0);
            $table->decimal('saldo_pendiente', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('estado')->default('presente');
            $table->string('observacion')->nullable();
            $table->timestamps();
            $table->unique(['enrollment_id', 'fecha']);
        });

        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->decimal('nota', 5, 2);
            $table->string('estado')->default('pendiente');
            $table->string('observacion')->nullable();
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->unique()->constrained('enrollments')->cascadeOnDelete();
            $table->string('codigo')->unique();
            $table->date('fecha_emision');
            $table->string('estado')->default('emitido');
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->foreignId('payment_method_id')->constrained('payment_methods')->restrictOnDelete();
            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->string('referencia')->nullable();
            $table->string('comprobante')->nullable();
            $table->string('estado')->default('registrado');
            $table->string('observacion')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->unique()->constrained('enrollments')->cascadeOnDelete();
            $table->decimal('monto_total', 10, 2);
            $table->unsignedSmallInteger('numero_cuotas');
            $table->string('estado')->default('activo');
            $table->timestamps();
        });

        Schema::create('payment_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_plan_id')->constrained('payment_plans')->cascadeOnDelete();
            $table->unsignedSmallInteger('numero_cuota');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_vencimiento');
            $table->date('fecha_pago')->nullable();
            $table->string('estado')->default('pendiente');
            $table->timestamps();
        });

        Schema::create('page_visits', function (Blueprint $table) {
            $table->id();
            $table->string('pagina')->unique();
            $table->unsignedBigInteger('visitas')->default(0);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique();
            $table->text('valor')->nullable();
            $table->string('tipo')->default('texto');
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('page_visits');
        Schema::dropIfExists('payment_installments');
        Schema::dropIfExists('payment_plans');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('offers');
        Schema::dropIfExists('course_subject');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('instructors');
        Schema::dropIfExists('students');
        Schema::dropIfExists('menu_role');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};
