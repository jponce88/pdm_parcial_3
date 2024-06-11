<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->addBodyParsingMiddleware();

$app->post('/api/v1/equipos', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    
    //var_dump($data);

    if (empty($data)) {
        throw new Exception("No se recibieron datos en el cuerpo de la solicitud.");
    }

    $requiredFields = ['nombreEquipo', 'institucion', 'departamento', 'municipio', 'direccion', 'telefono'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new Exception("Falta el campo '$field' en los datos recibidos.");
            }
        }

    $sql = "INSERT INTO equipos (nombreEquipo, 
                                 institucion, 
                                 departamento, 
                                 municipio, 
                                 direccion, 
                                 telefono) 
                 VALUES         (:nombreEquipo, 
                                 :institucion, 
                                 :departamento, 
                                 :municipio, 
                                 :direccion, 
                                 :telefono)";
    try {
        $db = DB::connect();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':nombreEquipo', $data['nombreEquipo']);
        $stmt->bindParam(':institucion', $data['institucion']);
        $stmt->bindParam(':departamento', $data['departamento']);
        $stmt->bindParam(':municipio', $data['municipio']);
        $stmt->bindParam(':direccion', $data['direccion']);
        $stmt->bindParam(':telefono', $data['telefono']);
        $stmt->execute();
        $data['idEquipo'] = $db->lastInsertId();
        $response->getBody()->write(json_encode($data));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(500);
    }
    return $response->withHeader('Content-Type', 'application/json');
});


$app->get('/api/v1/equipos', function (Request $request, Response $response, $args) {
    $sql = "SELECT * FROM equipos";
    try {
        $db = DB::connect();
        $stmt = $db->query($sql);
        $equipos = $stmt->fetchAll();
        $response->getBody()->write(json_encode($equipos));
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withStatus(500);
    }
    return $response->withHeader('Content-Type', 'application/json');
});
