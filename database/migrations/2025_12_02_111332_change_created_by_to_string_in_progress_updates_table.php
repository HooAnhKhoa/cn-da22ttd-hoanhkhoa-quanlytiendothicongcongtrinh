<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('progress_updates', function (Blueprint $table) {
            // Xóa foreign key nếu tồn tại
            $table->dropForeign(['created_by']);
            
            // Đổi kiểu cột thành string
            $table->string('created_by')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('progress_updates', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->change();
            $table->foreign('created_by')->references('id')->on('users');
        });
    }
};