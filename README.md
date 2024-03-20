## FarmaPremium - RestFul API

Desarrollar una RestFul API que permita a las farmacias y clientes gestionar los puntos de fidelización de manera eficiente. Las principales operaciones a implementar son:

- **Acumular:** Permite acumular puntos en la tarjeta de un cliente por compras realizadas en una farmacia específica.
- **Canjear:** Permite a los clientes canjear puntos acumulados por premios en una farmacia determinada.
- **Consultar**
  - Consultar puntos totales otorgados por una farmacia en un periodo de tiempo.
  - Consultar puntos totales otorgados a un cliente por una farmacia.
  - Consultar el saldo total de puntos de un cliente.

## Descarga de la API

Para descargar el proyecto tenemos que tener git y ejecutar los siguientes comandos:

```bash
git clone https://github.com/jaamito/farmaPremium.git
cd farmaPremium
```

## Instalación y ejecución DOCKER

Una vez descargado el fichero FarmaPremium.zip y extraido accedemos dentro de la carpeta y deberemos seguir los siguientes pasos:

1. Abrimos la terminal en el directorio FarmaPremium y ejecutamos el siguiente comando para construir la Imagen de nuestro archivo **docker-compose.yml**:

```bash
docker-compose build --no-cache --force-rm
```
2. En la misma terminal ejecutamos el siguiente comando para ejecuta el contenedor en docker:

```bash
docker-compose up -d
```
Una vez montada la Imagen y con los contenedores tendremos 3 imagenes:

- **Laravel:** [http://localhost:9000/](http://localhost:9000/)
- **MySql:** [http://localhost:3307/](http://localhost:3307/)
- **PhpMyAdmin:** [http://localhost:9001/](http://localhost:9001/)
  - Claves de acceso para hacer cualquier comprobación:
    - Usuario: root
    - Psw: root
    - bbdd: mysql_db

## Configuración Laravel

Empezamos por actualizar composer y sus librerías:

```bash
docker exec farmapremium-docker bash -c "composer update"
```

Vamos a lanzar 3 comandos para migrar la base de datos y cargar registros en las tablas Farmacia y Clientes para poder probar la API.

1. Migramos la bbdd de Laravel a MySQl:
   
```bash
docker exec farmapremium-docker bash -c "php artisan migrate"
```
2. Utilizamos un seeder para cargar datos en la tabla Farmacia y Clientes:

```bash
docker exec farmapremium-docker bash -c "php artisan db:seed --class=FarmaciaSeeder"
```
- Se generarán:
  - Farmacia A [ID:1]
  - Farmacia B [ID:2]
  - Farmacia C [ID:3]
    
```bash
docker exec farmapremium-docker bash -c "php artisan db:seed --class=ClienteSeeder"
```
- Se generarán:
  - Cliente 1 [ID:1]
  - Cliente 2 [ID:2]
  - Cliente 3 [ID:3]
 
  ## Estructura de bbdd

  Para esta API se han creado las siguientes tablas:
  - **clientes:** Contiene la información de los clientes.
    
    **Schema**
    
    ```php
    Schema::create('clientes', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->timestamps();
    });
    ```
    **Descripción del Esquema**
    - id: Campo autoincremental que sirve como clave primaria.
    - nombre: Campo para almacenar el nombre del cliente.
    - timestamps: Campos created_at y updated_at para mantener un registro de cuándo se creó y actualizó cada registro.
    
  - **farmacias:** Contiene la información de las farmacias.
    
    **Schema**
    
    ```php
    Schema::create('farmacias', function (Blueprint $table) {
        $table->id();
        $table->string('nombre')->unique();
        $table->timestamps();
    });
    ```
    **Descripción del Esquema**
    - id: Campo autoincremental que sirve como clave primaria.
    - nombre: Campo para almacenar el nombre de la farmacia. Se establece como único para evitar nombres duplicados.
    - timestamps: Campos created_at y updated_at para mantener un registro de cuándo se creó y actualizó cada registro.
  
  - **historicoFarmacia:** Historial de transacciones entre clientes y farmacias 
    
    **Schema**
    
    ```php
    Schema::create('historicoFarmacia', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('farmacia_id');
        $table->foreign('farmacia_id')->references('id')->on('farmacias')->onDelete('cascade');
        $table->unsignedBigInteger('cliente_id');
        $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
        $table->integer('puntos_otorgados');
        $table->timestamps();
    });
    ```
 **Descripción del Esquema**
  - id: Campo autoincremental que sirve como clave primaria.
  - farmacia_id: Campo que representa la relación con la tabla 'farmacias', utilizando una clave foránea.
  - cliente_id: Campo que representa la relación con la tabla 'clientes', utilizando una clave foránea.
  - puntos_otorgados: Campo para almacenar la cantidad de puntos otorgados en la transacción.
  - timestamps: Campos created_at y updated_at para mantener un registro de cuándo se creó y actualizó cada registro.
    
  - **movimientos:** Realiza seguimiento de los movimientos de puntos entre clientes y farmacias

    **Schema**
    
    ```php
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('farmacia_id');
            $table->integer('puntos_acumulados');
            $table->integer('puntos_canjeados');
            $table->timestamps();
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('farmacia_id')->references('id')->on('farmacias')->onDelete('cascade');
        });
    ```
     **Descripción del Esquema**
      - id: Campo autoincremental que sirve como clave primaria.
      - cliente_id: Campo que representa la relación con la tabla 'clientes', utilizando una clave foránea.
      - farmacia_id: Campo que representa la relación con la tabla 'farmacias', utilizando una clave foránea.
      - puntos_acumulados: Campo para almacenar la cantidad de puntos acumulados en la transacción.
      - puntos_canjeados: Campo para almacenar la cantidad de puntos canjeados en la transacción.
      - timestamps: Campos created_at y updated_at para mantener un registro de cuándo se creó y actualizó cada registro.
        
 - **tarjetas:**

   **Schema**
   ```php
        Schema::create('tarjetas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->integer('saldo_actual');
            $table->timestamps();
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
        });
    ```
   **Descripción del Esquema**
     - id: Campo autoincremental que sirve como clave primaria.
     - cliente_id: Campo que representa la relación con la tabla 'clientes', utilizando una clave foránea.
     - saldo_actual: Campo para almacenar el saldo actual de la tarjeta.
     - timestamps: Campos created_at y updated_at para mantener un registro de cuándo se creó y actualizó cada registro.

## Swagger
Para consultar la documentación de la API puedes acceder a [http://localhost:9000/api/documentation](http://localhost:9000/api/documentation)

## Test con PHPUnit
Se han creado 2 tipos de test para esta aplicación:
 1. Test simple que recorre todas las rutas de la API probando los diferentes tipos de código devueltos por la API (200,401,500...)
 2. Test mas complejo añadiendo mocks para simular objetos o clases específicas durante la ejecución de pruebas unitarias y hay 3 tests, uno por cada ruta y se encuentran en la ruta **FarmaPremium/laravel-app/test/Feature**

Para ejecutar los test debes utilizar el comando:
```bash
    docker exec farmapremium-docker bash -c "php artisan test"
```
