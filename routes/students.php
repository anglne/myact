<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;


$app = AppFactory::create();

$app->get('/students/all', function (Request $request, Response $response) {
   $sql = "SELECT * FROM students";

   try {
    $db = new DB();
    $conn = $db->connect();

    $stmt = $conn->query($sql);
    $students = $stmt->fetchAll(PDO::FETCH_OBJ);

    $db=null;
    $response->getBody()->write(json_encode($students));
    return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(202);

   } catch (PDOException $e) {
    $error = array(
        "message" => $e->getMessage()
    );

    $response->getBody()->write(json_encode($error));
    return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(500);

   }
});

$app->run();