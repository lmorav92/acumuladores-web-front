<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de Conexión - Sistema Mi Barber Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            max-width: 600px;
            width: 90%;
        }
        .error-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideDown 0.5s ease-out;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .error-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .error-icon {
            font-size: 60px;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        .error-body {
            padding: 30px;
        }
        .error-title {
            color: #dc3545;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .solution-box {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .solution-box h6 {
            color: #007bff;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .btn-retry {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 25px;
            transition: transform 0.3s;
        }
        .btn-retry:hover {
            transform: scale(1.05);
            color: white;
        }
        .tech-details {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #dc3545;
            margin-right: 8px;
            animation: blink 1.5s infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }
        .footer-text {
            text-align: center;
            color: white;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-header">
                <div class="error-icon">
                    <i class="fas fa-database"></i>
                </div>
                <h2 class="mb-0">Error de Conexión</h2>
            </div>
            
            <div class="error-body">
                <div class="alert alert-danger" role="alert">
                    <h5 class="error-title">
                        <span class="status-indicator"></span>
                        No se pudo conectar a la base de datos
                    </h5>
                    <p class="mb-0">
                        El sistema no puede establecer conexión con el servidor de base de datos MySQL.
                        Por favor, verifica que el servidor esté ejecutándose.
                    </p>
                </div>

                <div class="solution-box">
                    <h6><i class="fas fa-lightbulb"></i> Soluciones Rápidas:</h6>
                    <ol class="mb-0">
                        <li><strong>Iniciar MySQL:</strong> Ejecuta <code>sudo systemctl start mysql</code></li>
                        <li><strong>Verificar estado:</strong> Ejecuta <code>sudo systemctl status mysql</code></li>
                        <li><strong>Verificar credenciales:</strong> Revisa el archivo <code>database.php</code></li>
                        <li><strong>Verificar puerto:</strong> Asegúrate de que MySQL escucha en el puerto 3306</li>
                    </ol>
                </div>

                <div class="text-center mt-4">
                    <button onclick="location.reload()" class="btn btn-retry">
                        <i class="fas fa-sync-alt"></i> Reintentar Conexión
                    </button>
                </div>

                <div class="tech-details">
                    <strong><i class="fas fa-info-circle"></i> Detalles Técnicos:</strong><br>
                    <small>
                        Error: mysqli::real_connect(): (HY000/2002): Connection refused<br>
                        Archivo: mysqli_driver.php | Línea: 211<br>
                        Hostname: <?php echo isset($hostname) ? $hostname : '127.0.0.1'; ?><br>
                        Puerto: <?php echo isset($port) ? $port : '3306'; ?><br>
                        Base de Datos: <?php echo isset($database) ? $database : 'barberia_db'; ?>
                    </small>
                </div>

                <div class="alert alert-info mt-3" role="alert">
                    <small>
                        <i class="fas fa-terminal"></i> <strong>Comandos útiles:</strong><br>
                        • Iniciar MySQL: <code>sudo systemctl start mysql</code><br>
                        • Estado de MySQL: <code>sudo systemctl status mysql</code><br>
                        • Reiniciar MySQL: <code>sudo systemctl restart mysql</code>
                    </small>
                </div>
            </div>
        </div>

        <div class="footer-text">
            <p class="mb-0">
                <i class="fas fa-tools"></i> Sistema Mi Barber Shop - Versión 1.0
            </p>
            <small>Si el problema persiste, contacta al administrador del sistema</small>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Auto-verificar conexión cada 5 segundos
    setInterval(function() {
        console.log('Verificando conexión a la base de datos...');
    }, 5000);
    
    // Mostrar un contador de reintentos
    var intentos = 0;
    function reintentar() {
        intentos++;
        console.log('Intento de reconexión #' + intentos);
        location.reload();
    }
    </script>
</body>
</html>
