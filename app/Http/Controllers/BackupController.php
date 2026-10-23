<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    public function index()
    {
        return view('backups.index');
    }

    public function create($type = 'full')
    {
        try {
            $backupDir = storage_path('app/backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0775, true);
            }

            $dbName = config('database.connections.pgsql.database');
            $dbUser = config('database.connections.pgsql.username');
            $dbPass = config('database.connections.pgsql.password');
            $dbHost = config('database.connections.pgsql.host');
            $dbPort = config('database.connections.pgsql.port');

            $timestamp = now()->format('Ymd_His');
            $filename = "backup_{$dbName}_{$timestamp}.sql";
            $filepath = "{$backupDir}/{$filename}";

            $pgDump = $this->findPgDump();
            if (!$pgDump) {
                return $this->fallbackBackup($dbName, $filename, $filepath);
            }

            $command = sprintf(
                '"%s" --host=%s --port=%s --username=%s --dbname=%s --file=%s --no-password 2>&1',
                $pgDump,
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbName),
                escapeshellarg($filepath)
            );

            putenv("PGPASSWORD={$dbPass}");

            $output = shell_exec($command);

            if (file_exists($filepath) && filesize($filepath) > 0) {
                Log::info("Backup creado exitosamente: {$filename}");

                // Clean old backups (keep last 10)
                $this->cleanOldBackups($backupDir);

                return response()->json([
                    'success' => true,
                    'message' => "Backup {$type} creado exitosamente: {$filename}",
                    'file' => $filename,
                    'size' => filesize($filepath)
                ]);
            }

            Log::error("Error al crear backup: " . ($output ?? 'No output'));
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el backup. Verifica que PostgreSQL esté configurado correctamente.'
            ], 500);

        } catch (\Exception $e) {
            Log::error("Error en backup: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno al crear el backup. Revisa el registro del sistema.'
            ], 500);
        }
    }

    public function list()
    {
        $backupDir = storage_path('app/backups');
        $backups = [];

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*.sql');
            foreach ($files as $file) {
                $backups[] = [
                    'file' => basename($file),
                    'size' => filesize($file),
                    'size_formatted' => $this->formatSize(filesize($file)),
                    'date' => date('d/m/Y H:i:s', filemtime($file)),
                    'timestamp' => filemtime($file),
                ];
            }
            usort($backups, fn($a, $b) => $b['timestamp'] - $a['timestamp']);
        }

        return response()->json([
            'success' => true,
            'backups' => $backups
        ]);
    }

    public function download($filename)
    {
        $filepath = $this->validatedBackupPath((string) $filename);
        if (!$filepath) {
            return response()->json([
                'success' => false,
                'message' => 'Archivo de backup no encontrado'
            ], 404);
        }

        return response()->download($filepath, basename($filepath), [
            'Content-Type' => 'application/sql',
        ]);
    }

    public function delete($filename)
    {
        $filepath = $this->validatedBackupPath((string) $filename);
        if ($filepath && unlink($filepath)) {
            Log::info('Backup eliminado: ' . basename($filepath));
            return response()->json([
                'success' => true,
                'message' => 'Backup eliminado correctamente'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Archivo de backup no encontrado'
        ], 404);
    }

    private function validatedBackupPath(string $filename): ?string
    {
        if (!preg_match('/\Abackup_[A-Za-z0-9_.-]+_\d{8}_\d{6}\.sql\z/D', $filename)) {
            return null;
        }

        $directory = realpath(storage_path('app/backups'));
        $path = $directory ? realpath($directory . DIRECTORY_SEPARATOR . $filename) : false;
        if (!$path || dirname($path) !== $directory || !is_file($path)) {
            return null;
        }

        return $path;
    }

    private function cleanOldBackups($backupDir)
    {
        $files = glob($backupDir . '/*.sql');
        if (count($files) > 10) {
            usort($files, fn($a, $b) => filemtime($a) - filemtime($b));
            $toDelete = array_slice($files, 0, count($files) - 10);
            foreach ($toDelete as $file) {
                unlink($file);
                Log::info("Backup antiguo eliminado: " . basename($file));
            }
        }
    }

    public function clearCache()
    {
        try {
            $commands = [
                'php artisan cache:clear',
                'php artisan config:clear',
                'php artisan view:clear',
                'php artisan route:clear',
            ];

            foreach ($commands as $cmd) {
                $process = Process::fromShellCommandline($cmd);
                $process->run();
            }

            Log::info('Caché del sistema limpiado manualmente');
            return response()->json([
                'success' => true,
                'message' => 'Caché del sistema limpiado exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error limpiando caché: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar la caché'
            ], 500);
        }
    }

    public function retryJobs()
    {
        try {
            $failedJobs = DB::table('failed_jobs')->get();
            $count = $failedJobs->count();

            if ($count > 0) {
                DB::table('failed_jobs')->delete();
                Log::info("Se eliminaron {$count} trabajos fallidos de la cola");
            }

            return response()->json([
                'success' => true,
                'retried' => $count,
                'message' => "Se limpiaron {$count} trabajos fallidos"
            ]);
        } catch (\Exception $e) {
            Log::error('Error reintentando trabajos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar trabajos fallidos'
            ], 500);
        }
    }

    private function findPgDump()
    {
        $paths = [
            'C:\Program Files\PostgreSQL\18\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\17\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\16\bin\pg_dump.exe',
            'C:\Program Files\PostgreSQL\15\bin\pg_dump.exe',
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        return trim(shell_exec('where pg_dump 2>NUL') ?? '');
    }

    private function fallbackBackup($dbName, $filename, $filepath)
    {
        $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema='public' ORDER BY table_name");
        $sql = "-- Backup de {$dbName} generado el " . now() . "\n-- Modo: SQL fallback (sin pg_dump)\n\n";
        foreach ($tables as $table) {
            $name = $table->table_name;
            $rows = DB::table($name)->get();
            if ($rows->isEmpty()) continue;
            $sql .= "INSERT INTO \"{$name}\" VALUES\n";
            $values = [];
            foreach ($rows as $row) {
                $row = (array) $row;
                $escaped = array_map(fn($v) => is_null($v) ? 'NULL' : "'" . str_replace("'", "''", $v) . "'", $row);
                $values[] = '(' . implode(',', $escaped) . ')';
            }
            $sql .= implode(",\n", $values) . ";\n\n";
        }
        file_put_contents($filepath, $sql);
        if (filesize($filepath) > 0) {
            Log::info("Backup SQL fallback creado: {$filename}");
            $this->cleanOldBackups(dirname($filepath));
            return response()->json(['success' => true, 'message' => "Backup SQL creado: {$filename}", 'file' => $filename, 'size' => filesize($filepath)]);
        }
        return response()->json(['success' => false, 'message' => 'Error al crear backup SQL'], 500);
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
