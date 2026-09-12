<?php

namespace App\Services\Exports;

use Closure;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportService
{
    public function download(
        string $filename,
        array $headings,
        iterable $rows,
        Closure $mapRow
    ): StreamedResponse {
        return response()->streamDownload(
            function () use ($headings, $rows, $mapRow) {
                $handle = fopen('php://output', 'w');

                /*
                |--------------------------------------------------------------------------
                | UTF-8 BOM
                |--------------------------------------------------------------------------
                |
                | Helps Microsoft Excel correctly recognise UTF-8 encoded CSV files.
                |
                */

                fwrite($handle, "\xEF\xBB\xBF");

                fputcsv($handle, $headings);

                foreach ($rows as $row) {
                    fputcsv(
                        $handle,
                        $mapRow($row)
                    );
                }

                fclose($handle);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]
        );
    }
}
