# wirtz-csv-utils

## what
Utilities to prepare a XLS file for import into the Wirtz Archive database project. 

## usage 

To see a visual of information on the console 
```shell
 php n app:process-excel ~/Downloads/2024\ Wirtz\ 1.xlsx
```

To process the XLS file into separate CSV files for import
```shell
 php n  app:convert-to-worksheets ~/Downloads/2024\ Wirtz\ 1.xlsx
 ```

## Break down
To explore, start with these following command files
- ConvertToWorksheetsCommand.php, creates individual CSVs ready for import
- TestFirstCommand.php, simple command to look up by name
- ProcessExcelCommand.php, processes each tab in workbook
- Worker.php, workhorse file that encapsulates functionality 
