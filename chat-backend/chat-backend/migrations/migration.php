<?php

// Check if the 'users' table exists before creating it
if (!$capsule->schema()->hasTable('users')) {
    $capsule->schema()->create('users', function ($table) {
        $table->increments('id'); // Auto-incrementing primary key
        $table->string('username'); // Username for the user
        $table->string('token')->unique(); // Unique token for user identification
        $table->timestamps(); // Created_at and updated_at timestamps
    });
    echo "Users table created.\n";
}

// Check if the 'groups' table exists before creating it
if (!$capsule->schema()->hasTable('groups')) {
    $capsule->schema()->create('groups', function ($table) {
        $table->increments('id'); // Auto-incrementing primary key
        $table->string('name'); // Name of the group
        $table->timestamps(); // Created_at and updated_at timestamps
    });
    echo "Groups table created.\n";
}

// Check if the 'messages' table exists before creating it
if (!$capsule->schema()->hasTable('messages')) {
    $capsule->schema()->create('messages', function ($table) {
        $table->increments('id'); // Auto-incrementing primary key
        $table->integer('group_id')->unsigned(); // Reference to the group
        $table->integer('user_id')->unsigned(); // Reference to the user (sender)
        $table->text('content'); // Message content
        $table->timestamps(); // Created_at and updated_at timestamps

        // Foreign key constraints (optional but recommended for referential integrity)
        $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
    echo "Messages table created.\n";
}
