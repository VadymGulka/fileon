<!doctype html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>fileON - Register</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="icon" href="https://i.imgur.com/QiAVEd7.jpg" type="image/png">

</head>

<body class="bg-light">
<div class="container">
	<div class="row">
		<div class="text-center col-12" style="padding-top: 16%">

<?php
//Deshabilita errores php en la web
ini_set('display_errors', '0');
//Variables de POST
$codigo = $_POST["codigo"];
$nombre = $_POST["nombre"] . "-" . $_POST["apellido"];
$username = $_POST["username"];
$email = $_POST["email"];
$quota = $_POST["quota"];
$grupo = $_POST["grupo"];
$is_ftp = $_POST["ftp"];
$pass = $_POST["pass"];
//Variables para BD
$code_status = false;
$is_admin = 0;
$created = 0;

//Import de JSON
$string = file_get_contents("./fileon.json");
$json_a = json_decode($string, true);
$arr_index = array();
//Comprobacion del codigo en POST
foreach ($json_a as $key => $value) {
    if ($value['id'] == $codigo) {
        $arr_index[] = $key;
        $code_status = true;
    }
}
//Si es correcto se bora del archivo JSON y para a crear el usuario
if ($code_status) {
    global $created;
    foreach ($arr_index as $i) {
        if ($json_a[$i]['isAdmin'] == 1) {
            $is_admin = 1;
        }
        unset($json_a[$i]);
    };
    $json_a = array_values($json_a);
    file_put_contents('fileon.json', json_encode($json_a, JSON_PRETTY_PRINT));
    echo '<div class="alert alert-success" role="alert">Codigo correcto<br>';
    `sudo su -s /bin/sh www-data -c 'export OC_PASS="'$pass'" && php /var/www/nextcloud/occ user:add --password-from-env --group="'$grupo'" --display-name="'$nombre'"  "'$username'"'`;
    `sudo su -s /bin/sh www-data -c 'php /var/www/nextcloud/occ user:setting "'$username'" settings email "'$email'" '`;
    `sudo su -s /bin/sh www-data -c 'php /var/www/nextcloud/occ user:setting "'$username'" files quota "'$quota'"GB '`;
    if ($is_admin == 1) {
        `sudo su -s /bin/sh www-data -c 'php occ group:adduser admin "'$username'"'`;
    }
    if ($is_ftp) {
        //Usuario FTP
        `sudo useradd {$username} || true`;
        //Contraseña del usuario FTP
        `sudo echo -e {$pass}"\n"{$pass} | sudo passwd {$username}`;
        //Crear el directorio "files" si ya no estaba creado
        `sudo mkdir /var/datacloud/{$username}/files || true`;
        `sudo chown -R www-data:www-data /var/datacloud/{$username}/files`;
        `sudo chmod -R 775 /var/datacloud/{$username}/files`;
        //Restringir el usuarios FTP a la carpeta "files
        `sudo usermod -d /var/datacloud/{$username}/files {$username}`;
        //Añadir usuario el grupo www-data para tener acceso a los archivos en "files"
        `sudo usermod -a -G www-data {$username}`;
        
        echo '<br>Usuario creado correctamente, pulsa <a href="http://http://54.174.128.31/nextcloud/" class="alert-link"> aqui</a> para acceder al sitio "fileON" con su usuarios y contraseña</div>';
    }
    $created = 1;
} else {
    echo '<div class="alert alert-danger">Codigo incorrecto</div>';
}

//Logs en la BD "fileon"
$conn = new mysqli("localhost", "fileon", "fileon", "fileon");
if ($conn->connect_error) {
    echo "Error de conexion: " . $conn->connect_error;
};
$sql = "INSERT INTO `fileon`.`users` (`nombre`, `username`, `email`, `cuota`, `grupo`, `is_admin`, `is_ftp`, `creado`, `code`) VALUES ('$nombre', '$username', '$email', '$quota', '$grupo', '$is_admin', '$is_ftp' , '$created', '$codigo')";
if (!$conn->query($sql) === true) {
    echo "Error de conexion" . "<br>" . $conn->error;
}
$conn->close();







?>

















</div>
	</div>
	</div>
</body>

</html>











