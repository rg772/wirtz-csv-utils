<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Prompts\Output\ConsoleOutput;
use LaravelZero\Framework\Commands\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Helper\Table;


class ProcessExcelCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'app:process-excel {filename}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Breaks down an incoming excel file';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $filename = $this->argument('filename');
        $spreadsheet = IOFactory::load($filename);
        $reformatted_worksheets = [];





        foreach ($spreadsheet->getAllSheets() as $worksheet) {
            $this->line($worksheet->getTitle());
            $reformatted_worksheets[$worksheet->getTitle()] = Worker::ProcessWorksheet($worksheet);

            $output = new ConsoleOutput();
            $table = new Table($output);


            $table->setHeaders(["First", "Last", "Details"]);
            foreach($reformatted_worksheets[$worksheet->getTitle()] as $row) {
                $table->addRow($row);
            }
            $table->render();

            unset($table);


        }


    }


}
