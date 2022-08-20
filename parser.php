<?php
require 'env.php';
class PerformAction {
    public function processCommand($args){
        if(isset($args[1])){
            if($args[1] == '--file' || $args[1] == '-file'){
                if(isset($args[3])){
                    // fetch third command
                    $ccmd = explode('=',$args[3]);
                    if($ccmd[0] == "--unique-combinations" || $ccmd[0] == "-unique-combinations" ){
                        $firstFile = FOLDER.$args[2];
                        $secondFile = FOLDER.$ccmd[1];
                        // check file format validation

                        if(file_exists($secondFile)){
                            $csv = array_map('str_getcsv', file($firstFile));
                            $csv2 = array_map('str_getcsv', file($secondFile));
                            $filteredArray = array();
                            foreach ($csv2 as $key => $value) {
                                $onlyCreated = $csv2[$key];
                                $filteredArray[] = $onlyCreated;
                            }
                            foreach ($csv as $key => $value) {
                                $onlyCreated = $csv[$key];
                                $filteredArray[] = $onlyCreated;
                            }
                            $filteredArray = array_intersect_key($filteredArray, array_unique(array_map('serialize', $filteredArray)));
                            function randomString($length = 7) {
                                return substr(str_shuffle(implode(array_merge(range('a','z'), range(0,9)))), 0, $length);
                            }
                            $generateNewFileName = randomString(7);
                            $toBeCreatedFileName = 'unique_'.$generateNewFileName.'.csv';
                            header('Content-Type: text/csv');
                            header('Content-Disposition: attachment; filename='.$toBeCreatedFileName);
                            if(file_exists(FOLDER.$toBeCreatedFileName)){
                                unlink(FOLDER.$toBeCreatedFileName);
                            }
                            $fp = fopen(FOLDER.$toBeCreatedFileName, 'wb');
                            foreach ( $filteredArray as $line ) {
                                fputcsv($fp, $line);
                            }
                            fclose($fp);

                            echo 'check the newly created file here: '.FOLDER.$toBeCreatedFileName;
                        }else{
                            throw new Exception("File not found");
                        }
                    }
                }else{
                    if(isset($args[2])){
                        $filename = $args[2];
                        if(file_exists(FOLDER.$filename)){
                            $onlyFileName = FOLDER.$filename;
                            $extensions = explode('.',$filename);
                            $onlyExtension = $extensions[1];
                            if($onlyExtension == 'csv'){
                                // convert csv as an array
                                $csv = array_map('str_getcsv', file($onlyFileName));
                                var_dump($csv);
                            }else if($onlyExtension == 'json'){
                                // convert json as an array
                                $string = file_get_contents($onlyFileName);
                                $json_a = json_decode($string, true);
                                var_dump($json_a);
                            }else if($onlyExtension == 'xml'){
                                // convert xml as an array
                                $xmlfile = file_get_contents($onlyFileName);
                                $new = simplexml_load_string($xmlfile);
                                $con = json_encode($new);
                                $newArr = json_decode($con, true);
                                var_dump($newArr);
                            }else{
                                throw new Exception("Invalid file format");
                            }
                        }else{
                            throw new Exception("File not found");
                        }
                    }else{
                        throw new Exception("No file selected");
                    }
                }
            }else if($args[1] == '--list' || $args[1] == '-list'){
                foreach(glob(FOLDER.'*.*') as $file) {
                    $newFileList = explode(FOLDER,$file);
                    echo $newFileList[1]."\r\n";
                }
            }else if($args[1] == '--help' || $args[1] == '-help'){
                echo '
---------------------------------------------------------------------------------------------
Note : you can run each command with either a single dash(-) or double dash(--)
Note : to avoid memory limit error, please go to this directory if you are using
wamp (C:\wamp64\bin\php\php_ver\php.ini), if you are using xampp (C:\xampp\apache\bin\php.ini)
then update (memory_limit = 32M) to (memory_limit = -1)
----------------------------------------------------------------------------------------------
this are the following list of valid commands and their purposes "\r\n"
php parser.php --list (to list names of available files to perform a query with)
php parser.php --folder (to show the name of the current folder where the files are kept)
php parser.php --help (to list all possible valid commands)
php parser.php --createfolder (to create a Folder/Directory)
php parser.php --createfile (to create a File)
php parser.php --file (scan a given file and displays an array of information in the given file)
                ';
            }else if($args[1] == '--folder' || $args[1] == '-folder'){
                echo FOLDER;
            }else if($args[1] == '--createfolder' || $args[1] == '-createfolder'){
                $newFolderName = mkdir($args[2], 0755, true);
            }else if($args[1] == '--createfile' || $args[1] == '-createfile'){
                $content = "";
                $fp = fopen($_SERVER['DOCUMENT_ROOT'] . $args[2],"wb");
                fwrite($fp,$content);
                fclose($fp);
            }else{
                throw new Exception("invalid command");
            }
        }else{
            throw new Exception("Please enter a valid command");
        }
    }
}

(new PerformAction)->processCommand($argv);