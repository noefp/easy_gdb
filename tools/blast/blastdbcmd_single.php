<?php
// Linux; PHP >= 7.4. Keep this implementation identical in both endpoints.
ini_set('display_errors', '0');
ob_start();
function egdb_fail($status, $message) {
    if (ob_get_level()) ob_clean();
    http_response_code($status);
    header('Content-Type: text/plain; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    echo $message;
    exit;
}
function egdb_string($value) {
    if (!is_string($value) || strpos($value, "\0") !== false) {
        throw new InvalidArgumentException('Invalid input.');
    }
    return $value;
}
function egdb_ids($raw, $maximum) {
    $raw = egdb_string($raw);
    if (strlen($raw) > 300000) throw new InvalidArgumentException('Input too large.');
    $ids = array_values(array_filter(array_map('trim', preg_split('/\R/', $raw)),
        static function ($id) { return $id !== ''; }));
    if (!$ids || count($ids) > $maximum) throw new InvalidArgumentException('Invalid number of gene IDs.');
    foreach ($ids as $id) {
        if (!preg_match('/\A[A-Za-z0-9_][A-Za-z0-9_.:|+\-]{0,254}\z/', $id)) {
            throw new InvalidArgumentException('Invalid gene ID format.');
        }
    }
    return array_values(array_unique($ids));
}
function egdb_catalog($directory) {
    $root = realpath($directory);
    if ($root === false || !is_dir($root)) throw new RuntimeException('Database directory unavailable.');
    $catalog = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root,
        FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (!$file->isFile()) continue;
        // Include ordinary, volume and alias BLAST database prefixes.
        if (!preg_match('/\.(nhr|nin|nsq|nog|nsd|nsi|phr|pin|pog|psd|psi|psq|nal|pal)$/i',
            $file->getFilename())) continue;
        $real = $file->getRealPath();
        if ($real === false || strpos($real, $root . DIRECTORY_SEPARATOR) !== 0) continue;
        $prefix = preg_replace('/\.[^.]+$/', '', $real);
        $relative = substr($real, strlen($root) + 1);
        $keys = [$real, $prefix, $relative, preg_replace('/\.[^.]+$/', '', $relative),
            basename($real), basename($prefix)];
        foreach (array_unique($keys) as $key) {
            if (!array_key_exists($key, $catalog)) $catalog[$key] = $prefix;
            elseif ($catalog[$key] !== $prefix) $catalog[$key] = false;
        }
    }
    return $catalog;
}
function egdb_database($value, $catalog) {
    $value = egdb_string($value);
    if ($value === '' || strlen($value) > 4096 || !isset($catalog[$value]) || $catalog[$value] === false) {
        throw new InvalidArgumentException('Unknown or ambiguous database selection.');
    }
    return $catalog[$value];
}
function egdb_extract($binary, $ids, $database, $deadline, $maxBytes) {
    $out = tmpfile();
    $err = tmpfile();
    $process = null;
    try {
        if ($out === false || $err === false) throw new RuntimeException('Temporary file unavailable.');
        $process = proc_open([$binary, '-db', $database, '-entry', implode(',', $ids)],
            [0 => ['file', '/dev/null', 'r'], 1 => $out, 2 => $err], $pipes);
        if (!is_resource($process)) throw new RuntimeException('Cannot start extraction.');
        while (true) {
            $state = proc_get_status($process);
            $outSize = fstat($out)['size'];
            $errSize = fstat($err)['size'];
            if ($outSize > $maxBytes || $errSize > 1048576 || microtime(true) > $deadline) {
                proc_terminate($process, 9);
                throw new RuntimeException('Extraction exceeded resource limits.');
            }
            if (!$state['running']) break;
            usleep(50000);
        }
        $exitCode = $state['exitcode'];
        $closeCode = proc_close($process);
        $process = null;
        if ($exitCode === -1) $exitCode = $closeCode;
        if ($exitCode !== 0) {
            rewind($err);
            error_log('easyGDB blastdbcmd: ' . substr((string) stream_get_contents($err, 4096), 0, 4096));
            throw new RuntimeException('Extraction failed.');
        }
        rewind($out);
        $result = stream_get_contents($out, $maxBytes + 1);
        if ($result === false || strlen($result) > $maxBytes) throw new RuntimeException('Output too large.');
        // Remove the first lcl| occurrence on each line (original sed behavior).
        return preg_replace('/^([^\r\n]*?)lcl\|/m', '$1', $result);
    } finally {
        if (is_resource($process)) { proc_terminate($process, 9); proc_close($process); }
        if (is_resource($out)) fclose($out);
        if (is_resource($err)) fclose($err);
    }
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    egdb_fail(405, 'POST required.');
}
try {
    require_once __DIR__ . '/../../../configuration_path.php';
    if (!isset($conf_path) || !is_string($conf_path)) throw new RuntimeException('Configuration missing.');
    require_once $conf_path . '/easyGDB_conf.php';
    if (!isset($blast_dbs_path) || !is_string($blast_dbs_path)) throw new RuntimeException('Database configuration missing.');
    $maxIds = isset($max_extract_seq_input) ? (int) $max_extract_seq_input : 1000;
    if ($maxIds < 1) $maxIds = 1000;
    $ids = egdb_ids($_POST['gids'] ?? null, min($maxIds, 10000));
    $catalog = egdb_catalog($blast_dbs_path);
    // Optional server-side configuration: $blastdbcmd_binary = '/absolute/path/blastdbcmd';
    $binary = $blastdbcmd_binary ?? '/usr/bin/blastdbcmd';
    if (!is_string($binary) || substr($binary, 0, 1) !== '/' || !is_executable($binary)) {
        throw new RuntimeException('Set blastdbcmd_binary to the executable absolute path.');
    }
    $databases = [egdb_database($_POST['blast_db'] ?? null, $catalog)];

    $deadline = microtime(true) + 60;
    $maxBytes = 20 * 1024 * 1024;
    $results = [];
    $total = 0;
    foreach (array_unique($databases) as $database) {
        $result = egdb_extract($binary, $ids, $database, $deadline, $maxBytes - $total);
        $total += strlen($result) + 2;
        if ($total > $maxBytes) throw new RuntimeException('Output too large.');
        $results[] = $result;
    }
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/octet-stream');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store');
    header('Content-Disposition: attachment; filename="egdb_sequences_' . date('Y-m-d_His') . '.fasta"');
    echo implode("\n\n", $results);
    exit;
} catch (InvalidArgumentException $e) {
    egdb_fail(400, $e->getMessage());
} catch (Throwable $e) {
    error_log('easyGDB extraction: ' . $e->getMessage());
    egdb_fail(500, 'Sequence extraction failed. Please contact the administrator.');
}
