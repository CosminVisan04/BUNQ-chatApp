<?php

// Use the Capsule instance passed from index.php
echo "Running migration...\n";

// Check if the 'users' table exists before creating it
if (!$capsule->schema()->hasTable('users')) {
    $capsule->schema()->create('users', function ($table) {
        $table->increments('id');
        $table->string('username');
        $table->string('token')->unique();
        $table->timestamps();
    });
    echo "Users table created.\n";
} else {
    echo "Users table already exists.\n";
}

// Check if the 'groups' table exists before creating it
if (!$capsule->schema()->hasTable('groups')) {
    $capsule->schema()->create('groups', function ($table) {
        $table->increments('id');
        $table->string('name');
        $table->timestamps();
    });
    echo "Groups table created.\n";
} else {
    echo "Groups table already exists.\n";
}

// Check if the 'messages' table exists before creating it
if (!$capsule->schema()->hasTable('messages')) {
    $capsule->schema()->create('messages', function ($table) {
        $table->increments('id');
        $table->integer('group_id')->unsigned();
        $table->integer('user_id')->unsigned();
        $table->text('content');
        $table->timestamps();
    });
    echo "Messages table created.\n";
} else {
    echo "Messages table already exists.\n";
}

