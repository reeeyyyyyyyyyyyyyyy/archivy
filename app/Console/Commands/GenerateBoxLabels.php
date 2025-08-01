<?php

namespace App\Console\Commands;

use App\Models\StorageBox;
use App\Models\Archive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Dompdf\Options;

class GenerateBoxLabels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:generate-box-labels {--box-numbers= : Specific box numbers (comma-separated)} {--format=pdf : Output format (pdf or word)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate box labels in PDF or Word format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $boxNumbers = $this->option('box-numbers');
        $format = $this->option('format');

        if ($boxNumbers) {
            $boxNumbers = explode(',', $boxNumbers);
            $boxes = StorageBox::whereIn('box_number', $boxNumbers)->get();
        } else {
            $boxes = StorageBox::where('archive_count', '>', 0)->get();
        }

        if ($boxes->isEmpty()) {
            $this->error('No boxes found to generate labels for.');
            return Command::FAILURE;
        }

        $this->info("Generating {$format} labels for {$boxes->count()} boxes...");

        if ($format === 'pdf') {
            $this->generatePdfLabels($boxes);
        } else {
            $this->error("Format '{$format}' not supported. Use 'pdf' or 'word'.");
            return Command::FAILURE;
        }

        $this->info('Box labels generated successfully!');
        return Command::SUCCESS;
    }

    /**
     * Generate PDF labels
     */
    private function generatePdfLabels($boxes)
    {
        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf->setOptions($options);

        $html = $this->generateLabelsHtml($boxes);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $outputPath = storage_path('app/public/box-labels.pdf');
        file_put_contents($outputPath, $dompdf->output());

        $this->info("PDF saved to: {$outputPath}");
    }

    /**
     * Generate HTML for labels
     */
    private function generateLabelsHtml($boxes)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Box Labels</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                .label {
                    width: 300px;
                    height: 200px;
                    border: 2px solid #000;
                    margin: 10px;
                    padding: 15px;
                    display: inline-block;
                    page-break-inside: avoid;
                    box-sizing: border-box;
                }
                .header {
                    text-align: center;
                    border-bottom: 2px solid #000;
                    padding-bottom: 10px;
                    margin-bottom: 15px;
                    font-weight: bold;
                    font-size: 14px;
                }
                .content {
                    display: flex;
                    justify-content: space-between;
                    margin-top: 20px;
                }
                .left-column {
                    width: 60%;
                    border-right: 1px solid #000;
                    padding-right: 10px;
                }
                .right-column {
                    width: 35%;
                    padding-left: 10px;
                }
                .field {
                    margin-bottom: 8px;
                    font-size: 12px;
                }
                .field-label {
                    font-weight: bold;
                    margin-bottom: 2px;
                }
                .field-value {
                    border-bottom: 1px solid #000;
                    min-height: 15px;
                }
                .box-number {
                    font-size: 18px;
                    font-weight: bold;
                    text-align: center;
                    margin-top: 10px;
                }
                @media print {
                    .label { page-break-inside: avoid; }
                }
            </style>
        </head>
        <body>';

        foreach ($boxes as $box) {
            $archives = Archive::where('box_number', $box->box_number)->get();
            $archiveCount = $archives->count();

            $html .= '
            <div class="label">
                <div class="header">
                    DINAS PENANAMAN MODAL DAN PTSP<br>
                    PROVINSI JAWA TIMUR
                </div>
                <div class="content">
                    <div class="left-column">
                        <div class="field">
                            <div class="field-label">NOMOR BERKAS</div>
                            <div class="field-value">' . ($archives->first()->file_number ?? '') . '</div>
                        </div>
                        <div class="field">
                            <div class="field-label">JUMLAH ARSIP</div>
                            <div class="field-value">' . $archiveCount . '</div>
                        </div>
                        <div class="field">
                            <div class="field-label">KAPASITAS</div>
                            <div class="field-value">' . $box->capacity . '</div>
                        </div>
                    </div>
                    <div class="right-column">
                        <div class="field">
                            <div class="field-label">NO. BOKS</div>
                            <div class="field-value">' . $box->box_number . '</div>
                        </div>
                        <div class="field">
                            <div class="field-label">STATUS</div>
                            <div class="field-value">' . ucfirst($box->status) . '</div>
                        </div>
                    </div>
                </div>
                <div class="box-number">
                    BOX ' . $box->box_number . '
                </div>
            </div>';
        }

        $html .= '</body></html>';

        return $html;
    }
}
