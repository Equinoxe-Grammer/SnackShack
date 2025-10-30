<?php
// testing-example.php - ejemplo simple de test usando PHPUnit
// Colócalo en tests/Integration o tests/Unit según corresponda.

use PHPUnit\Framework\TestCase;

class ExampleApiTest extends TestCase
{
    public function testProductsEndpoint()
    {
        $response = file_get_contents('http://localhost:8000/api/v1/products');
        $this->assertNotFalse($response, 'No se obtuvo respuesta del endpoint');

        $data = json_decode($response, true);
        $this->assertIsArray($data, 'La respuesta debe ser un array JSON');
    }
}

// Para ejecutar: ./vendor/bin/phpunit --filter ExampleApiTest
?>
