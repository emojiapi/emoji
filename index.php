<?php

$typeEmoji = "google";
$urlEmoji = "https://emojipedia.org/es/".$typeEmoji."/";

function getFileContent($url)
{
    $html = file_get_contents($url);
    $dom  = new DOMDocument();
    libxml_use_internal_errors(1);
    $dom->loadHTML($html);
    return $dom->getElementsByTagName('img');
}

function validateNameFile($url){
    $file_name = basename(parse_url($url, PHP_URL_PATH));
    return $file_name;
}

function prepareNewNameFile($name){
    $variable = strstr($name, "_");
    $newName = substr($variable, 1);
    if(preg_match('(dark|mountain|skin|tone|medium|light)', $newName) === 1) { 
        $newVariable = substr(strstr($newName, "_"), 1);
        return str_replace("_" , "-",$newVariable);
    } 
    return str_replace("_" , "-", $newName);
}


$tags= getFileContent($urlEmoji);

$output = [];
foreach ($tags as $key => $tag) {
    $get144 = $tag->getAttribute('data-srcset');
    $getDefault = $tag->getAttribute('srcset');
    if(!empty($get144) && strlen($get144) > 10){
        $urlNew = trim(str_replace("2x" , "", $get144));
        $output[] = (object) [
            "id" => $key + 1,
            "link" => $urlNew,
            "name" => validateNameFile($urlNew),
            "newName" => prepareNewNameFile(validateNameFile($urlNew))
        ];
    }else{
        if(!empty($getDefault) && strlen($getDefault) > 10 && strpos($getDefault, "144")){
            $urlNewDev = trim(str_replace("2x" , "", $getDefault));
            $output[] = (object) [
                "id" => $key + 1,
                "link" => $urlNewDev,
                "name" => validateNameFile($urlNewDev),
                "newName" => prepareNewNameFile(validateNameFile($urlNewDev))
            ];
        }
    }
}


// file_put_contents('facebook.json', json_encode($output, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

foreach($output as $key =>  $item) {
    $img = "./".$typeEmoji."/".$item->newName;
    file_put_contents($img, file_get_contents($item->link));
    echo " ". ($key + 1) . " => ".$item->newName. " ".PHP_EOL;
}

