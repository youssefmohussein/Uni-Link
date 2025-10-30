<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($request) {
	case '/unilink-backend/api/install': // 👈 Adjust folder name if different
	case '/api/install':
	case '/unilink_backend/api/install':
	case '/unilink_backend/index.php/api/install':
		require __DIR__ . '/controllers/InstallController.php';
		$controller = new InstallController();
		$controller->handleRequest();
		break;

	case '/unilink-backend/api/test':
	case '/api/test':
	case '/unilink_backend/api/test':
	case '/unilink_backend/index.php/api/test':
		require __DIR__ . '/test.php';
		break;

	default:
		echo json_encode(["status" => "error", "message" => "Invalid route"]);
		break;
}
?>
