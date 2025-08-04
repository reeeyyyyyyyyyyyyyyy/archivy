<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StorageBox;
use App\Models\StorageRack;
use Barryvdh\DomPDF\Facade\Pdf;

class GenerateBoxLabels extends Command
{
    protected $signature = 'storage:generate-box-labels {--rack-id=} {--box-start=} {--box-end=} {--format=pdf}';
    protected $description = 'Generate printable box labels in PDF format for a specific rack and box range';

    public function handle()
    {
        $rackId = $this->option('rack-id');
        $boxStart = $this->option('box-start');
        $boxEnd = $this->option('box-end');
        $format = $this->option('format');

        if (!$rackId) {
            $this->error('Please specify rack ID using --rack-id option');
            return 1;
        }

        if (!$boxStart || !$boxEnd) {
            $this->error('Please specify box range using --box-start and --box-end options');
            return 1;
        }

        $rack = StorageRack::find($rackId);
        if (!$rack) {
            $this->error('Rack not found with ID: ' . $rackId);
            return 1;
        }

        // Get boxes in the specified range
        $boxes = StorageBox::where('rack_id', $rackId)
            ->whereBetween('box_number', [$boxStart, $boxEnd])
            ->orderBy('box_number')
            ->get();

        if ($boxes->isEmpty()) {
            $this->error('No boxes found in rack: ' . $rack->name . ' with range ' . $boxStart . '-' . $boxEnd);
            return 1;
        }

        $this->info("Generating labels for rack: {$rack->name} (ID: {$rackId})");
        $this->info("Box range: {$boxStart} - {$boxEnd}");
        $this->info("Found {$boxes->count()} boxes in this range");

        $labels = [];
        foreach ($boxes as $box) {
            $capacity = $box->capacity ?? 20; // Default capacity
            $actualCount = $box->archive_count;

            if ($actualCount == 0) {
                // Empty box - show dashes for file numbers
                $labels[] = [
                    'box_number' => $box->box_number,
                    'first_range' => 'NO.ARSIP -',
                    'second_range' => 'NO.ARSIP -',
                    'capacity' => $capacity,
                    'actual_count' => 0
                ];
            } else {
                // Calculate file number ranges based on actual count
                $halfCount = (int)($actualCount / 2);
                $remainder = $actualCount % 2;

                if ($remainder == 0) {
                    // Even number
                    $firstRange = "NO.ARSIP 1-" . $halfCount;
                    $secondRange = "NO.ARSIP " . ($halfCount + 1) . "-" . $actualCount;
                } else {
                    // Odd number - give extra to first range
                    $firstRange = "NO.ARSIP 1-" . ($halfCount + 1);
                    $secondRange = "NO.ARSIP " . ($halfCount + 2) . "-" . $actualCount;
                }

                // Special case for count = 1
                if ($actualCount == 1) {
                    $firstRange = "NO.ARSIP 1-1";
                    $secondRange = "NO.ARSIP 1-1";
                }

                $labels[] = [
                    'box_number' => $box->box_number,
                    'first_range' => $firstRange,
                    'second_range' => $secondRange,
                    'capacity' => $capacity,
                    'actual_count' => $actualCount
                ];
            }
        }

        if ($format === 'pdf') {
            $this->generatePDF($labels, $rack);
        } else {
            $this->error('Only PDF format is supported');
            return 1;
        }

        $this->info('Labels generated successfully!');
        return 0;
    }

    private function generatePDF($labels, $rack)
    {
        $html = $this->generateHTML($labels, $rack);

        $pdf = PDF::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'rack_labels_' . $rack->id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $filepath = storage_path('app/public/' . $filename);

        $pdf->save($filepath);

        $this->info("PDF saved to: " . $filepath);
        $this->info("Download URL: " . asset('storage/' . $filename));

        return asset('storage/' . $filename);
    }

    private function generateHTML($labels, $rack)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Box Labels - ' . $rack->name . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 15px;
                    background-color: white;
                }
                .label-container {
                    display: flex;
                    flex-direction: column;
                    gap: 15px;
                }
                .label {
                    width: 100%;
                    height: 120px;
                    border: 2px solid #000000;
                    position: relative;
                    margin-bottom: 120px;
                    page-break-inside: avoid;
                    background-color: white;
                }
                .header {
                    height: 40px;
                    background-color: white;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    border-bottom: 2px solid #000000;
                    font-weight: bold;
                    font-size: 14px;
                    text-align: center;
                }
                .header-text {
                    color: #000000;
                    font-weight: bold;
                    font-size: 14px;
                    text-align: center;
                    margin: 0;
                    line-height: 1.2;
                }
                .content {
                    height: 80px;
                    display: flex;
                }
                .file-numbers {
                    flex: 0 0 72.7%;
                    background-color: white;
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-start;
                    align-items: center;
                    border-right: 2px solid #000000;
                    padding: 8px;
                    text-align: center;
                }
                .box-number {
                    flex: 0 0 27.3%;
                    background-color: white;
                    display: flex;
                    flex-direction: column;
                    justify-content: flex-start;
                    align-items: center;
                    padding: 8px;
                }
                .content-text {
                    color: #000000;
                    font-weight: bold;
                    font-size: 16px;
                    text-align: center;
                    margin: 0;
                    line-height: 1.3;
                }
                .file-range {
                    margin: 6px 0;
                    text-align: left;
                    padding-left: 20px;
                }
                .label-title {
                    font-weight: bold;
                    font-size: 14px;
                    margin-bottom: 10px;
                    color: #000000;
                    text-align: center;
                }
                @media print {
                    body {
                        background-color: white;
                    }
                    .label {
                        page-break-inside: avoid;
                        margin-bottom: 15px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="label-container">';

        foreach ($labels as $label) {
            $html .= '
                <div class="label">
                    <div class="header">
                        <p class="header-text">DINAS PENANAMAN MODAL DAN PTSP PROVINSI JAWA TIMUR</p>
                    </div>
                    <div class="content">
                        <div class="file-numbers">
                            <div class="label-title">NOMOR BERKAS</div>
                            <div class="file-range">
                                <p class="content-text">' . $label['first_range'] . '</p>
                            </div>
                            <div class="file-range">
                                <p class="content-text">' . $label['second_range'] . '</p>
                            </div>
                        </div>
                        <div class="box-number">
                            <div class="label-title">NO. BOKS</div>
                            <p class="content-text">' . $label['box_number'] . '</p>
                        </div>
                    </div>
                </div>';
        }

        $html .= '
            </div>
        </body>
        </html>';

        return $html;
    }
}
