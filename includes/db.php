<?php
$db_file = __DIR__ . '/../database.sqlite';
$db = new PDO('sqlite:' . $db_file);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    avatar TEXT DEFAULT NULL
)");

$res = $db->query("PRAGMA table_info(users)");
$columns = $res->fetchAll(PDO::FETCH_ASSOC);
$avatarExists = false;
foreach ($columns as $column) {
    if ($column['name'] === 'avatar') {
        $avatarExists = true;
        break;
    }
}
if (!$avatarExists) {
    $db->exec("ALTER TABLE users ADD COLUMN avatar TEXT DEFAULT NULL");
}

// Migration for comments table to add parent_id
$res = $db->query("PRAGMA table_info(comments)");
$columns = $res->fetchAll(PDO::FETCH_ASSOC);
$parentIdExists = false;
foreach ($columns as $column) {
    if ($column['name'] === 'parent_id') {
        $parentIdExists = true;
        break;
    }
}
if (!$parentIdExists) {
    $db->exec("ALTER TABLE comments ADD COLUMN parent_id INTEGER DEFAULT NULL");
}

$db->exec("CREATE TABLE IF NOT EXISTS videos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    description TEXT,
    filename TEXT NOT NULL,
    poster TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    video_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    parent_id INTEGER DEFAULT NULL,
    text TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(video_id) REFERENCES videos(id),
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(parent_id) REFERENCES comments(id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS comment_likes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    comment_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    type TEXT CHECK(type IN ('like', 'dislike')),
    FOREIGN KEY(comment_id) REFERENCES comments(id),
    FOREIGN KEY(user_id) REFERENCES users(id),
    UNIQUE(comment_id, user_id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS likes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    video_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    FOREIGN KEY(video_id) REFERENCES videos(id),
    FOREIGN KEY(user_id) REFERENCES users(id),
    UNIQUE(video_id, user_id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS dislikes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    video_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    FOREIGN KEY(video_id) REFERENCES videos(id),
    FOREIGN KEY(user_id) REFERENCES users(id),
    UNIQUE(video_id, user_id)
)");

$db->exec("CREATE TABLE IF NOT EXISTS subscriptions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    subscriber_id INTEGER NOT NULL,
    creator_id INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(subscriber_id) REFERENCES users(id),
    FOREIGN KEY(creator_id) REFERENCES users(id),
    UNIQUE(subscriber_id, creator_id)
)");
?>