[11:20 AM] Alejandro Montero Arteaga

<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
class ClocksAPI
{
    private $app;
    private $db;
    public function __construct()
    {
        $this->app = AppFactory::create();
        $this->db = new mysqli('localhost', 'your_username', 'your_password', 'your_database_name');
        $this->routes();

    }
            private function routes()
    {
        $this->app->get('/clocks', function (Request $request, Response $response) {
            $sql = "SELECT * FROM clocks";
            $result = $this->db->query($sql);
            if ($result->num_rows > 0) {
                $clocks = [];
                while ($row = $result->fetch_assoc()) {
                    $clocks[] = $row;
                }
                $response->getBody()->write(json_encode($clocks));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } else {
                $response->getBody()->write(json_encode([]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }
        });
        $this->app->get('/clocks/{id}', function (Request $request, Response $response, $args) {
            $clockId = $args['id'];
            $sql = "SELECT * FROM clocks WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $clockId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $clock = $result->fetch_assoc();
                $response->getBody()->write(json_encode($clock));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['error' => 'Clock not found']));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }
        });
        $this->app->post('/clocks', function (Request $request, Response $response) {
            $data = $request->getParsedBody();
            $nombre = $data['nombre'] ?? '';
            $marca = $data['marca'] ?? '';
            $precio = $data['precio'] ?? '';
            $material = $data['material'] ?? '';
            $nombre = $this->db->real_escape_string($nombre);
            $marca = $this->db->real_escape_string($marca);
            $precio = $this->db->real_escape_string($precio);
            $material = $this->db->real_escape_string($material);
            $sql = "INSERT INTO clocks (nombre, marca, precio, material) VALUES ('$nombre', '$marca', '$precio', '$material')";
            if ($this->db->query($sql) === TRUE) {
                $clockId = $this->db->insert_id;
                $clock = ['id' => $clockId, 'nombre' => $nombre, 'marca' => $marca, 'precio' => $precio, 'material' => $material];
                $response->getBody()->write(json_encode($clock));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(201);
            } else {
                $response->getBody()->write(json_encode(['error' => 'Failed to create clock']));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500);
            }
        });
        $this->app->put('/clocks/{id}', function (Request $request, Response $response, $args) {
            $clockId = $args['id'];
            $data = $request->getParsedBody();
            $nombre = $data['nombre'] ?? '';
            $marca = $data['marca'] ?? '';
            $precio = $data['precio'] ?? '';
            $material = $data['material'] ?? '';
            $clockId = $this->db->real_escape_string($clockId);
            $nombre = $this->db->real_escape_string($nombre);
            $marca = $this->db->real_escape_string($marca);
            $precio = $this->db->real_escape_string($precio);
            $material = $this->db->real_escape_string($material);
            $sql = "UPDATE clocks SET nombre = '$nombre', marca = '$marca', precio = '$precio', material = '$material' WHERE id = '$clockId'";
            if ($this->db->query($sql) === TRUE) {
                $clock = ['id' => $clockId, 'nombre' => $nombre, 'marca' => $marca, 'precio' => $precio, 'material' => $material];
                $response->getBody()->write(json_encode($clock));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['error' => 'Failed to update clock']));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500);
            }
        });
        $this->app->delete('/clocks/{id}', function (Request $request, Response $response, $args) {
            $clockId = $args['id'];
            $clockId = $this->db->real_escape_string($clockId);
            $sql = "DELETE FROM clocks WHERE id = '$clockId'";
            if ($this->db->query($sql) === TRUE) {
                $response->getBody()->write(json_encode(['message' => 'Clock deleted']));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            } else {
                $response->getBody()->write(json_encode(['error' => 'Failed to delete clock']));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(500);
            }
        });
        $this->app->run();
    }
}
// Usage example:
$api = new ClocksAPI();