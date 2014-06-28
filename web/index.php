<?php

// web/index.php

$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

//$app = require __DIR__ . '/../src/app.php';
$blogPosts = array(
    1 => array(
        'date' => '2011-03-29',
        'author' => 'igorw',
        'title' => 'Using Silex',
        'body' => '...',
    ) ,
);

$app->get('/blog', function () use ($blogPosts) {
    $output = '';
    foreach ($blogPosts as $post) {
        $output.= $post['title'];
        $output.= '<br />';
    }
    
    return $output;
});

$app->get('/blog/{id}', function (Silex\Application $app, $id) use ($blogPosts) {
    if (!isset($blogPosts[$id])) {
        $app->abort(404, "Post $id does not exist.");
    }
    
    $post = $blogPosts[$id];
    
    return "<h1>{$post['title']}</h1>" . "<p>{$post['body']}</p>";
});

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->post('/feedback', function (Request $request) {
    $message = $request->get('message');
    mail('feedback@yoursite.com', '[YourSite] Feedback', $message);

    return new Response('Thank you for your feedback!', 201);
});

$app->run();
