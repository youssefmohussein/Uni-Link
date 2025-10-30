<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once __DIR__ . '/../exports/export.php';

class ExportController {
	public function handleRequest(): void {
		$result = exportDatabase();
		echo json_encode($result);
	}
}
