
<?php
require_once __DIR__ . '/../utils/EnvLoader.php';

function exportDatabase(): array {
	$env = loadEnv(__DIR__ . '/../.env');
	$host = $env['DB_HOST'] ?? 'localhost';
	$user = $env['DB_USER'] ?? '';
	$pass = $env['DB_PASS'] ?? '';
	$dbName = $env['DB_NAME'] ?? '';

	if ($dbName === '') {
		return [
			'status' => 'error',
			'message' => 'DB_NAME is missing in .env'
		];
	}

	$exportsDir = __DIR__;
	if (!is_dir($exportsDir)) {
		if (!mkdir($exportsDir, 0777, true) && !is_dir($exportsDir)) {
			return [
				'status' => 'error',
				'message' => 'Failed to create exports directory'
			];
		}
	}

	$timestamp = date('Ymd_His');
	$exportFile = $exportsDir . DIRECTORY_SEPARATOR . "{$dbName}_backup_{$timestamp}.sql";

	$mysqldumpPath = 'C:\develope tools\xamp\mysql\bin\mysqldump.exe';
	if (!file_exists($mysqldumpPath)) {
		return [
			'status' => 'error',
			'message' => "mysqldump not found at $mysqldumpPath"
		];
	}

	$cmd = '"' . $mysqldumpPath . '"'
		. ' -h ' . escapeshellarg($host)
		. ' -u ' . escapeshellarg($user)
		. (!empty($pass) ? ' -p' . escapeshellarg($pass) : '')
		. ' --routines --events --triggers'
		. ' --databases ' . escapeshellarg($dbName)
		. ' --result-file=' . '"' . $exportFile . '"';

	$output = [];
	$resultCode = 0;
	exec($cmd . ' 2>&1', $output, $resultCode);

	if ($resultCode === 0 && file_exists($exportFile)) {
		return [
			'status' => 'success',
			'message' => 'Database exported successfully',
			'file' => $exportFile
		];
	}

	return [
		'status' => 'error',
		'message' => 'Export failed',
		'command' => $cmd,
		'output' => $output
	];
}
 