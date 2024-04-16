<?php

namespace App\Commands;

use Exception;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;


class Worker
{
    static public function NameLookUp ($first, $last): array {


        $full_url = sprintf("%s?last-name=%s&first-name=%s&isStrict=false", env('BASICLOOKUP_END_POINT'), $last, $first);

        $ch = curl_init($full_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'Apikey: ' . env('BASICLOOKUPKEY')]);

        $result = curl_exec($ch);
        curl_close($ch);

        // Was there an error?
        if ($result === false) {
            $error = curl_error($ch);
            return ["Curl error: $error"];
        }


        $data = json_decode($result, true);


        if (!empty($data['results'])) {
            return array_map(function ($item) {
                return implode(', ', [
                    'displayName' => $item['displayName'][0],
                    'netid' => $item['uid'],
                    'employeeNumber'=> $item['employeeNumber']
                ]);
            }, $data['results']);
        } else {
            return [
                'data'=>sprintf("no matches on %s %s", $first, $last)
            ];
        }



    }


    public static function ProcessWorksheet($worksheet): array
    {

        // Loop through the rows
        // Get the first row (assuming it contains the headers)
        $headers = $worksheet->rangeToArray('A1:' . $worksheet->getHighestColumn() . '1');

        // Find the column index for "First name" and "Last name"
        $firstNameIndex = array_search('First name', $headers[0]);
        $lastNameIndex = array_search('Last name', $headers[0]);

        // Convert column index to Excel column letter
        $firstNameColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($firstNameIndex + 1);
        $lastNameColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastNameIndex + 1);

        // Get the highest row number
        $highestRow = $worksheet->getHighestRow();

        // Get the values from the "First name" and "Last name" columns
        $firstNames = $worksheet->rangeToArray($firstNameColumn . '2:' . $firstNameColumn . $highestRow);
        $lastNames = $worksheet->rangeToArray($lastNameColumn . '2:' . $lastNameColumn . $highestRow);



        // Print the values
        $toReturn = [];
        foreach ($firstNames as $index => $firstName) {
            $first =  $firstName[0];
            $last = $lastNames[$index][0];

            // catch blank last name
            if ($last == "") {
               echo "Skipping $first $last";
                continue;
            }

            try{
                $toReturn[] = [$first, $last, implode(separator: '; ' , array: Worker::NameLookUp($first, $last))];
            } catch(Exception $e) {
                $toReturn[] = [$first, $last, ""];

            }
        }

        return $toReturn;

    }

}
