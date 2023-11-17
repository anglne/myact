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


$app->get('/students/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $sql = "SELECT * FROM students WHERE id = $id";
 
    try {
     $db = new DB();
     $conn = $db->connect();
 
     $stmt = $conn->query($sql);
     $student = $stmt->fetch(PDO::FETCH_OBJ);
 
     $db=null;
     $response->getBody()->write(json_encode($student));
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

 $app->post('/students/add', function (Request $request, Response $response, array $args) {
    $firstname = $request->getParam('firstname');
    $lastname = $request->getParam('lastname');
    $course = $request->getParam('course');
    $age = $request->getParam('age');




    $sql = "INSERT INTO students (firstname, lastname, course, age) VALUE (:firstname, :lastname, :course, :age)";
 
    try {
     $db = new DB();
     $conn = $db->connect();
 
     $stmt = $conn->prepare($sql);
     $stmt-> bindParam(':firstname', $firstname);
     $stmt-> bindParam(':lastname', $lastname);
     $stmt-> bindParam(':course', $course);
     $stmt-> bindParam(':age', $age);

     $result = $stmt->execute();
    
 
     $db=null;
     $response->getBody()->write(json_encode($result));
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


 $app->put('/students/update/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $data = $request->getParsedBody(); // Retrieves PUT data
    
    // Assuming these keys exist in the PUT data
    $firstname = $data['firstname'] ?? null;
    $lastname = $data['lastname'] ?? null;
    $course = $data['course'] ?? null;
    $age = $data['age'] ?? null;

    $sql = "UPDATE students SET firstname = :firstname, lastname = :lastname, course = :course, age = :age WHERE id = :id";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':course', $course);
        $stmt->bindParam(':age', $age);
        $stmt->bindParam(':id', $id);

        $result = $stmt->execute();

        $db = null;
        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200); // Assuming success status code is 200 for an update

    } catch (PDOException $e) {
        $error = ["message" => $e->getMessage()];

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500); // Internal Server Error for database issues

    }
});

$app->delete('/students/delete/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];

    $sql = "DELETE FROM students WHERE id = :id";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        $result = $stmt->execute();

        $db = null;
        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200); // Assuming success status code is 200 for a deletion

    } catch (PDOException $e) {
        $error = ["message" => $e->getMessage()];

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500); // Internal Server Error for database issues

    }
});


