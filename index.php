<?php

/**
* PLACEGOAT. For all your goat placeholder needs.
* @author Ross Hettel
* @version 0.1 beta
*/


/**
 * Step 1: Require the Slim Framework
 */
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

/**
 * Step 2: Instantiate a Slim application
 */
$app = new \Slim\Slim(array(
    'debug'=>true,
    'templates.path'=>'./templates'
));

/**
 * Step 3: Define the Slim application routes
 */

// Homepage
$app->get('/', function() use($app) {
    $app->render('homeTemplate.php', array('test'=>'this is a test'));
});

// Width & Height
$app->get('/:width/:height', function($width, $height) use($app) {
    if($width > 1500 || $height > 1500) {
        echo "Slow down, buddy. We don't have that many goats."; 
        die();
    }

    $response = $app->response();
    $response['Content-Type'] = 'image/jpeg';

    $goat = grabAGoat();
    resizeAndServe($goat, $width, $height);
    
});

// Width only
$app->get('/:width', function($width) use($app) {
    //just redirect them to the width & height route 
    $app->response()->redirect("/$width/$width", 303);
});

// Yakkity Yak
$app->get('/yak/:width/:height', function($width, $height) use($app) {
    echo "Yakkity yak. Come back later, Jack.";
});

$app->get('/yak/:width', function($width) use($app) {
    //redirect to the yak width & height route
    $app->response()->redirect("/yak/$width/$width", 303);
});

function resizeAndServe($imagePath, $newWidth, $newHeight) {
    //read original image, get it's dimensions
    $sourceImage = imagecreatefromjpeg($imagePath);
    $sourceX = imagesx($sourceImage);
    $sourceY = imagesy($sourceImage);

    $destImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceX, $sourceY);

    header('Content-Type: image/jpeg');
    imagejpeg($destImage);
}

function grabAGoat($dir = 'goats') {
    $goats = glob($dir.'/*.*');
    $goat = array_rand($goats);
    return $goats[$goat];
};


/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
