<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use ZipArchive;
use Illuminate\Support\Facades\Log;

class ViewerController extends Controller
{
    /**
     * Sanitiza una cadena para ser usada como nombre de DIRECTORIO.
     * Reemplaza espacios y caracteres especiales con guiones bajos.
     */
    private function sanitizePath($string)
    {
        // Reemplaza espacios por guiones bajos
        $string = str_replace(' ', '_', $string);
        // Reemplaza cualquier carácter que NO sea alfanumérico, guion bajo, guion medio o punto por guion bajo
        return preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $string);
    }

    /**
     * Normaliza un nombre de archivo SIN cambiar caracteres legibles.
     */
    private function cleanFileName(string $fileName): string
    {
        $fileName = str_replace("\0", '', $fileName);
        $fileName = str_replace('\\', '/', $fileName);
        $fileName = basename($fileName);
        return trim($fileName);
    }

    /**
     * Lista el contenido de un archivo comprimido (ZIP o RAR).
     */
    public function getArchiveFiles(Request $request)
    {
        $inputs = $request->input();

        // Validación básica
        if (!isset($inputs['idProject']) || !isset($inputs['fileName'])) {
            return response()->json(['error' => 'Missing parameters'], 400);
        }

        // Obtener el proyecto
        $project = Project::find($inputs['idProject']);
        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $projectName = $this->sanitizePath($project->name);
        $milestoneName = isset($inputs['milestoneTitle']) ? $this->sanitizePath($inputs['milestoneTitle']) : null;
        $requestedFileName = $this->cleanFileName($inputs['fileName']);

        // Construir la ruta (misma lógica que ProjectController::downloadFile)
        $filePath = '';
        if ($milestoneName !== null) {
            // Buscar primero por nombre limpio
            $filePath = 'project_files/' . $projectName . '/' . $milestoneName . '/' . $requestedFileName;
            // Fallback
            if (!Storage::disk('local')->exists($filePath)) {
                $legacySanitized = $this->sanitizePath($requestedFileName);
                $filePath = 'project_files/' . $projectName . '/' . $milestoneName . '/' . $legacySanitized;
            }
        } else {
            // Buscar primero por nombre limpio
            $filePath = 'project_files/' . $projectName . '/' . $requestedFileName;
            // Fallback
            if (!Storage::disk('local')->exists($filePath)) {
                $legacySanitized = $this->sanitizePath($requestedFileName);
                $filePath = 'project_files/' . $projectName . '/' . $legacySanitized;
            }
        }

        if (!Storage::disk('local')->exists($filePath)) {
            Log::warning("ViewerController: File not found. Inputs: " . json_encode($inputs) . " | Checked Path: " . $filePath);
            return response()->json(['error' => 'File not found on server'], 404);
        }

        // Obtener ruta absoluta para las librerías de PHP
        $absolutePath = Storage::disk('local')->path($filePath);
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        $structure = [];
        $stats = [
            'total_files' => 0,
            'total_size' => 0, // bytes
            'types' => [] // conteo por extensión
        ];

        try {
            if ($extension === 'zip') {
                $zip = new ZipArchive;
                if ($zip->open($absolutePath) === TRUE) {
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $stat = $zip->statIndex($i);
                        // $stat['name'], $stat['size'], $stat['mtime']

                        // Ignorar directorios si terminan en /
                        $isDir = substr($stat['name'], -1) === '/';

                        $fileInfo = [
                            'name' => $stat['name'],
                            'size' => $stat['size'],
                            'is_dir' => $isDir,
                            'path' => $stat['name'] // ruta interna
                        ];

                        if (!$isDir) {
                            $stats['total_files']++;
                            $stats['total_size'] += $stat['size'];
                            $ext = strtolower(pathinfo($stat['name'], PATHINFO_EXTENSION));
                            if (!isset($stats['types'][$ext])) $stats['types'][$ext] = 0;
                            $stats['types'][$ext]++;
                        }

                        $structure[] = $fileInfo;
                    }
                    $zip->close();
                } else {
                    return response()->json(['error' => 'Could not open ZIP file'], 500);
                }
            } elseif ($extension === 'rar') {
                if (function_exists('rar_open')) {
                    $rar = \RarArchive::open($absolutePath);
                    if ($rar === FALSE) {
                        return response()->json(['error' => 'Could not open RAR file'], 500);
                    }
                    $entries = $rar->getEntries();
                    if ($entries === FALSE) {
                        return response()->json(['error' => 'Could not read RAR entries'], 500);
                    }
                    foreach ($entries as $entry) {
                        $isDir = $entry->isDirectory();
                        $fileInfo = [
                            'name' => $entry->getName(),
                            'size' => $entry->getUnpackedSize(),
                            'is_dir' => $isDir,
                            'path' => $entry->getName()
                        ];

                        if (!$isDir) {
                            $stats['total_files']++;
                            $stats['total_size'] += $entry->getUnpackedSize();
                            $ext = strtolower(pathinfo($entry->getName(), PATHINFO_EXTENSION));
                            if (!isset($stats['types'][$ext])) $stats['types'][$ext] = 0;
                            $stats['types'][$ext]++;
                        }

                        $structure[] = $fileInfo;
                    }
                    $rar->close();
                } else {
                    // Fallback para RAR sin soporte: devolvemos éxito pero indicamos que no hay contenido listable
                    return response()->json([
                        'files' => [],
                        'stats' => ['total_files' => 0, 'total_size' => 0, 'types' => []],
                        'supported' => false,
                        'message' => 'RAR preview not supported on this server'
                    ]);
                }
            } else {
                return response()->json(['error' => 'Unsupported archive format'], 400);
            }
        } catch (\Exception $e) {
            Log::error("Error reading archive: " . $e->getMessage());
            return response()->json(['error' => 'Exception reading archive: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'files' => $structure,
            'stats' => $stats,
            'supported' => true
        ]);
    }
}
