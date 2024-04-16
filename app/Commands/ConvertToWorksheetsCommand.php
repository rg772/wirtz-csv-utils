<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

class ConvertToWorksheetsCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'app:convert-to-worksheets {file}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Separates worksheets out from an excel file and sames them as separate CSVs in the same folder';

    /**
     * @return void
     */
    public function handle(): void
    {
        $excelFile = $this->argument('file');

        // output folder
        $fullPath = realpath($excelFile);
        $outputPath = pathinfo($fullPath, PATHINFO_DIRNAME);

        $spreadsheet = IOFactory::load($excelFile);

        $sheetNames = $spreadsheet->getSheetNames();

        foreach ($sheetNames as $sheetName) {
            $spreadsheet->setActiveSheetIndexByName($sheetName);
            $sheet = $spreadsheet->getActiveSheet();
            // Check if the first cell in the first row is "First name"
            if ($sheet->getCell('A1')->getValue() === 'First name') {
                $sheet->removeRow(1);  // Remove the title row
            }
            $writer = new Csv($spreadsheet);
            $writer->setEnclosure('');  // Disable enclosing fields with double-quotes
            $csvFileName = $outputPath . DIRECTORY_SEPARATOR .  $sheetName . '.csv';
            $writer->save($csvFileName);
            $this->info("Saved {$sheetName} to {$csvFileName}");
        }
    }



}
