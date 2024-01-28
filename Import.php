<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Импорт");
?>

<html> 
<body style="text-align:center;"> 
<div class="Kon">
<hr class="Khr">
    <h1 >Импорт</h1> 
</div>     
  
    <?php
    if(isset($_POST['butimp'])) { 

        $id = $_POST['Link'];
        $csv = file_get_contents('https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&gid=0');
        $csv = explode("\r\n", $csv);
        $array = array_map('str_getcsv', $csv);
            
            
        foreach ($array as &$strin) {
            if($strin[0]<>"Бренд"){
                foreach($strin as &$value){
                    switch($value){
                        case "Дубайское золото":
                            array_push($strin, "370");
                        break;
                        case "Fallon":
                            array_push($strin, "369");
                        break;
                        case "Другое":
                            array_push($strin, "4712");
                        break;
                        case "Swarowski":
                            array_push($strin, "368");
                        break;
                        case "Xuping":
                            array_push($strin, "367");
                        break;
                    }
                }
               
                CModule::IncludeModule('iblock');
                CModule::IncludeModule('sale');
    
                $ciBlockElement = new CIBlockElement;

                $PROP[61] = $strin[11];
                $PROP[270] = $strin[2];
                $PROP[279] = $strin[0];

                $kto = $strin[1];

                //разделы
                switch($strin[1]){
                    case "Браслеты":
                        $strin[1]= "91,102" ;
                        break;
                    case "Серьги":
                        $strin[1]= "98,102" ;                       
                        break;
                    case "Комплекты":
                        $strin[1]= "94,102" ;                        
                        break;
                    case "Цепочки":
                        $strin[1]= "99,102" ;                        
                        break;
                    case "Кольца":
                        $strin[1]= "93,102" ;                        
                        break;
                    case "Кулоны":
                        $strin[1]= "95,102" ;                        
                        break;
                    case "Часы":
                        $strin[1]= "100,102" ;                        
                        break;
                    case "Броши":
                        $strin[1]= "92,102" ;                        
                        break;
                    case "Подарочная упаковка":
                        $strin[1]= "96,102" ;                        
                        break;
                    case "Подставки":
                        $strin[1]= "97,102" ;                        
                        break;
                }
    
                // Добавляем товар-родитель, у которго будут торг. предложения
                $product_id = $ciBlockElement->Add(
                    array(
                        "IBLOCK_ID" => 13, // IBLOCK товаров
                        "IBLOCK_SECTION_ID" => $strin[1],
                        "NAME" => $strin[9],
                        "ACTIVE" => "Y",
                        "PROPERTY_VALUES"=> $PROP,  // Добавим нашему элементу заданные свойства
                        "PREVIEW_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"]."/upload/pics_catalog/".$strin[6])  // ссылка на детальную картинку
                        
                    )
                );

                // проверка на ошибки
                if (!empty($ciBlockElement->LAST_ERROR)) {
                    echo "Ошибка добавления товара: ". $ciBlockElement->LAST_ERROR;
                    die();
                }

                // ---------------------------------предложения------------------------------------------

                $PROPER[91] = $product_id; //sku
                
                $Size = explode(",",$strin[3]);
                $Width = explode(",",$strin[5]);
            
                // if($strin[4][0] > 30 ){
                    $Length2 = explode(",",$strin[4]);
                    
                // }
                // else{
                    $Length = explode(",",$strin[4]);
                    
                // }
                
                switch($kto){ 
                    case"Кольца":
                    case"Комплекты":

                        $PROPER[277] = null;
                        $PROPER[283] = null;
                        $PROPER[276] = null;

                        $LengthSize = count($Size);//кольцо
                        $kol=0;//количество повторений цикла

                        while ($LengthSize<>0){
                            switch($Size[$kol]){ //id  размер кольца
                                case "15":
                                    $PROPER[90] = 116 ;
                                break;
                                case "16":
                                    $PROPER[90] = 117 ;                       
                                break;
                                case "17":
                                    $PROPER[90] = 120 ;                        
                                break;
                                case "18":
                                    $PROPER[90] = 121 ;                        
                                break;
                                case "19":
                                    $PROPER[90] = 122 ;                        
                                break;
                                case "20":
                                    $PROPER[90] = 123 ;                        
                                 break;
                                case "21":
                                    $PROPER[90] = 124 ;                        
                                 break;
                                case "22":
                                    $PROPER[90] = 125 ;                        
                                break;
                                case "23":
                                    $PROPER[90] = 126 ;                        
                                break;
                            }

                                // добавляем нужное кол-во торговых предложений
                            $arLoadProductArray = array(
                                "IBLOCK_ID"      => 14, // IBLOCK торговых предложений
                                "NAME"           => $strin[9],
                                "ACTIVE"         => "Y",
                                'PROPERTY_VALUES'=> $PROPER
                                
                                
                                // Прочие параметры товара 
                            );

                            $product_offer_id = $ciBlockElement->Add($arLoadProductArray);
                            // проверка на ошибки
                            if (!empty($ciBlockElement->LAST_ERROR)) {
                                echo "Ошибка добавления торгового предложения: ". $ciBlockElement->LAST_ERROR;
                                die();
                            }

                            // Добавляем параметры к торг. предложению
                            CCatalogProduct::Add(
                                array(
                                    "ID" => $product_offer_id,
                                    "QUANTITY" => 9999
                                )
                            );

                            // Добавляем цены к торг. предложению
                            CPrice::Add(
                                array(
                                    "CURRENCY" => "RUB",
                                    "PRICE" => $strin[7],
                                    "CATALOG_GROUP_ID" => 1,
                                    "PRODUCT_ID" => $product_offer_id
                                )
                            );

                            $kol++;
                            $LengthSize-- ;
                        }
                    break;

                    case "Браслеты":
            
                        $PROPER[90] = null;
                        $PROPER[283] = null;
                        $PROPER[285] = null;
                        

                        $LengtLengt = count($Length);//браслет
                        $kol=0;//количество повторений цикла
                        
                        while ($LengtLengt<>0){

                            switch($Length[$kol]){ //длина браслета
                                case "15-16":
                                    $PROPER[284] = 156;
                                    $Length_prom = [118, 119];
                                break;
                                case "15-16,5":
                                    $PROPER[284] = 157;
                                    $Length_prom = [118, 119];                       
                                break;
                                case "15-17,5":
                                    $PROPER[284] = 158;
                                    $Length_prom = [118,119,127];                        
                                 break;
                                case "15-18":
                                    $PROPER[284] = 159;
                                    $Length_prom = [118,119,127,128];                        
                                 break;
                                case "15-18,5":
                                    $PROPER[284] = 160;
                                    $Length_prom = [118,119,127,128];                        
                                 break;
                                case "15-19":
                                    $PROPER[284] = 161;
                                    $Length_prom = [118,119,127,128,129];                        
                                break;
                                case "15-19,5":
                                    $PROPER[284] = 162;
                                    $Length_prom = [118,119,127,128,129];                       
                                break;
                                case "15-20":
                                    $PROPER[284] = 163;
                                    $Length_prom = [118,119,127,128,129,130];                        
                                break;
                                case "15-20,5":
                                    $PROPER[284] = 164;
                                    $Length_prom = [118,119,127,128,129,130];                        
                                break;
                                case "15-21":
                                    $PROPER[284] = 165;
                                    $Length_prom = [118,119,127,128,129,130,131];                        
                                break;
                                case "15-21,5":
                                    $PROPER[284] = 166;
                                    $Length_prom = [118,119,127,128,129,130,131];
                                break;
                                case "15-22":
                                    $PROPER[284] = 167;
                                    $Length_prom = [118,119,127,128,129,130,131,132];                       
                                break;
                                case "15-22,5":
                                    $PROPER[284] = 168;
                                    $Length_prom = [118,119,127,128,129,130,131,132];                         
                                 break;
                                case "15-23":
                                    $PROPER[284] = 169;
                                    $Length_prom = [118,119,127,128,129,130,131,132,133];                         
                                 break;
                                case "15-23,5":
                                    $PROPER[284] = 170;
                                    $Length_prom = [118,119,127,128,129,130,131,132,133];                        
                                 break;
                                case "15-24":
                                    $PROPER[284] = 171;
                                    $Length_prom = [118,119,127,128,129,130,131,132,133,134];                        
                                break;

                                case "15,5-16":
                                    $PROPER[284] = 172;
                                    $Length_prom = [119];                       
                                break;
                                case "15,5-16,5":
                                    $PROPER[284] = 173;
                                    $Length_prom = [119];                        
                                break;
                                case "15,5-17":
                                    $PROPER[284] = 174;
                                    $Length_prom = [119,127];                        
                                break;
                                case "15,5-17,5":
                                    $PROPER[284] = 175;
                                    $Length_prom = [119,127];                        
                                break;
                                case "15,5-18":
                                    $PROPER[284] = 176;
                                    $Length_prom = [119,127,128];
                                break;
                                case "15,5-18,5":
                                    $PROPER[284] = 177;
                                    $Length_prom = [119,127,128];                       
                                break;
                                case "15,5-19":
                                    $PROPER[284] = 178;
                                    $Length_prom = [119,127,128,129];                        
                                 break;
                                case "15,5-19,5":
                                    $PROPER[284] = 179;
                                    $Length_prom = [119,127,128,129];                        
                                 break;
                                case "15,5-20":
                                    $PROPER[284] = 180;
                                    $Length_prom = [119,127,128,129,130];                        
                                 break;
                                case "15,5-20,5":
                                    $PROPER[284] = 181;
                                    $Length_prom = [119,127,128,129,130];                        
                                break;
                                case "15,5-21":
                                    $PROPER[284] = 182;
                                    $Length_prom = [119,127,128,129,130,131];                       
                                break;
                                case "15,5-21,5":
                                    $PROPER[284] = 183;
                                    $Length_prom = [119,127,128,129,130,131];                        
                                break;
                                case "15,5-22":
                                    $PROPER[284] = 184;
                                    $Length_prom = [119,127,128,129,130,131,132];                        
                                break;
                                case "15,5-22,5":
                                    $PROPER[284] = 185;
                                    $Length_prom = [119,127,128,129,130,131,132];                        
                                break;
                                case "15,5-23":
                                    $PROPER[284] = 186;
                                    $Length_prom = [119,127,128,129,130,131,132,133];
                                break;
                                case "15,5-23,5":
                                    $PROPER[284] = 187;
                                    $Length_prom = [119,127,128,129,130,131,132,133];                       
                                break;
                                case "15,5-24":
                                    $PROPER[284] = 188;
                                    $Length_prom = [119,127,128,129,130,131,132,133,134];                        
                                break;
                                
                                 
                                case "16-16,5":
                                    $PROPER[284] = 189;
                                    $Length_prom = [119];                        
                                break;
                                case "16-17":
                                    $PROPER[284] = 190;
                                    $Length_prom = [119,127];                        
                                break;
                                case "16-17,5":
                                    $PROPER[284] = 191;
                                    $Length_prom = [119,127];                        
                                break;
                                case "16-18":
                                    $PROPER[284] = 192;
                                    $Length_prom = [119,127,128];
                                break;
                                case "16-18,5":
                                    $PROPER[284] = 193;
                                    $Length_prom = [119,127,128];                       
                                break;
                                case "16-19":
                                    $PROPER[284] = 194;
                                    $Length_prom = [119,127,128,129];                        
                                 break;
                                case "16-19,5":
                                    $PROPER[284] = 195;
                                    $Length_prom = [119,127,128,129];                        
                                 break;
                                case "16-20":
                                    $PROPER[284] = 196;
                                    $Length_prom = [119,127,128,129,130];                        
                                 break;
                                case "16-20,5":
                                    $PROPER[284] = 197;
                                    $Length_prom = [119,127,128,129,130];                        
                                break;
                                case "16-21":
                                    $PROPER[284] = 198;
                                    $Length_prom = [119,127,128,129,130,131];                       
                                break;
                                case "16-21,5":
                                    $PROPER[284] = 199;
                                    $Length_prom = [119,127,128,129,130,131];                        
                                break;
                                case "16-22":
                                    $PROPER[284] = 200;
                                    $Length_prom = [119,127,128,129,130,131,132];                        
                                break;
                                case "16-22,5":
                                    $PROPER[284] = 201;
                                    $Length_prom = [119,127,128,129,130,131,132];                        
                                break;
                                case "16-23":
                                    $PROPER[284] = 202;
                                    $Length_prom = [119,127,128,129,130,131,132,133];
                                break;
                                case "16-23,5":
                                    $PROPER[284] = 203;
                                    $Length_prom = [119,127,128,129,130,131,132,133];                       
                                break;
                                case "16-24":
                                    $PROPER[284] = 204;
                                    $Length_prom = [119,127,128,129,130,131,132,133,134];                        
                                break;
                                 
                                 
                                
                                case "16,5-17":
                                    $PROPER[284] = 205;
                                    $Length_prom = [127];                        
                                break;
                                case "16,5-17,5":
                                    $PROPER[284] = 206;
                                    $Length_prom = [127];                        
                                break;
                                case "16,5-18":
                                    $PROPER[284] = 207;
                                    $Length_prom = [127,128];
                                break;
                                case "16,5-18,5":
                                    $PROPER[284] = 208;
                                    $Length_prom = [127,128];                       
                                break;
                                case "16,5-19":
                                    $PROPER[284] = 209;
                                    $Length_prom = [127,128,129];                        
                                 break;
                                case "16,5-19,5":
                                    $PROPER[284] = 210;
                                    $Length_prom = [127,128,129];                        
                                 break;
                                case "16,5-20":
                                    $PROPER[284] = 211;
                                    $Length_prom = [127,128,129,130];                        
                                 break;
                                case "16,5-20,5":
                                    $PROPER[284] = 212;
                                    $Length_prom = [127,128,129,130];                        
                                break;
                                case "16,5-21":
                                    $PROPER[284] = 213;
                                    $Length_prom = [127,128,129,130,131];                       
                                break;
                                case "16,5-21,5":
                                    $PROPER[284] = 214;
                                    $Length_prom = [127,128,129,130,131];                        
                                break;
                                case "16,5-22":
                                    $PROPER[284] = 215;
                                    $Length_prom = [127,128,129,130,131,132];                        
                                break;
                                case "16,5-22,5":
                                    $PROPER[284] = 216;
                                    $Length_prom = [127,128,129,130,131,132];                        
                                break;
                                case "16,5-23":
                                    $PROPER[284] = 217;
                                    $Length_prom = [127,128,129,130,131,132,133];
                                break;
                                case "16,5-23,5":
                                    $PROPER[284] = 218;
                                    $Length_prom = [127,128,129,130,131,132,133];                       
                                break;
                                case "16,5-24":
                                    $PROPER[284] = 219;
                                    $Length_prom = [127,128,129,130,131,132,133,134];                        
                                break;

                                case "17-17,5":
                                    $PROPER[284] = 220;
                                    $Length_prom = [127];                        
                                break;
                                case "17-18":
                                    $PROPER[284] = 221;
                                    $Length_prom = [127,128];
                                break;
                                case "17-18,5":
                                    $PROPER[284] = 222;
                                    $Length_prom = [127,128];                       
                                break;
                                case "17-19":
                                    $PROPER[284] = 223;
                                    $Length_prom = [127,128,129];                        
                                break;
                                case "17-19,5":
                                    $PROPER[284] = 224;
                                    $Length_prom = [127,128,129];                        
                                break;
                                case "17-20":
                                    $PROPER[284] = 225;
                                    $Length_prom = [127,128,129,130];                        
                                 break;
                                case "17-20,5":
                                    $PROPER[284] = 226;
                                    $Length_prom = [127,128,129,130];                        
                                break;
                                case "17-21":
                                    $PROPER[284] = 227;
                                    $Length_prom = [127,128,129,130,131];                       
                                break;
                                case "17-21,5":
                                    $PROPER[284] = 228;
                                    $Length_prom = [127,128,129,130,131];                        
                                break;
                                case "17-22":
                                    $PROPER[284] = 229;
                                    $Length_prom = [127,128,129,130,131,132];                        
                                break;
                                case "17-22,5":
                                    $PROPER[284] = 230;
                                    $Length_prom = [127,128,129,130,131,132];                        
                                break;
                                case "17-23":
                                    $PROPER[284] = 231;
                                    $Length_prom = [127,128,129,130,131,132,133];
                                break;
                                case "17-23,5":
                                    $PROPER[284] = 232;
                                    $Length_prom = [127,128,129,130,131,132,133];                       
                                break;
                                case "17-24":
                                    $PROPER[284] = 233;
                                    $Length_prom = [127,128,129,130,131,132,133,134];                        
                                break;

                                 
                                case "17,5-18":
                                    $PROPER[284] = 234;
                                    $Length_prom = [128];
                                break;
                                case "17,5-18,5":
                                    $PROPER[284] = 235;
                                    $Length_prom = [128];                       
                                break;
                                case "17,5-19":
                                    $PROPER[284] = 236;
                                    $Length_prom = [128,129];                        
                                break;
                                case "17,5-19,5":
                                    $PROPER[284] = 237;
                                    $Length_prom = [128,129];                        
                                break;
                                case "17,5-20":
                                    $PROPER[284] = 238;
                                    $Length_prom = [128,129,130];                        
                                break;
                                case "17,5-20,5":
                                    $PROPER[284] = 239;
                                    $Length_prom = [128,129,130];                        
                                break;
                                case "17,5-21":
                                    $PROPER[284] = 240;
                                    $Length_prom = [128,129,130,131];                       
                                break;
                                case "17,5-21,5":
                                    $PROPER[284] = 241;
                                    $Length_prom = [128,129,130,131];                        
                                break;
                                case "17,5-22":
                                    $PROPER[284] = 242;
                                    $Length_prom = [128,129,130,131,132];                        
                                break;
                                case "17,5-22,5":
                                    $PROPER[284] = 243;
                                    $Length_prom = [128,129,130,131,132];                        
                                break;
                                case "17,5-23":
                                    $PROPER[284] = 244;
                                    $Length_prom = [128,129,130,131,132,133];
                                break;
                                case "17,5-23,5":
                                    $PROPER[284] = 245;
                                    $Length_prom = [128,129,130,131,132,133];                       
                                break;
                                case "17,5-24":
                                    $PROPER[284] = 246;
                                    $Length_prom = [128,129,130,131,132,133,134];                        
                                break;
                                 
                                case "18-18,5":
                                    $PROPER[284] = 247;
                                    $Length_prom = [128];                       
                                break;
                                case "18-19":
                                    $PROPER[284] = 248;
                                    $Length_prom = [128,129];                        
                                break;
                                case "18-19,5":
                                    $PROPER[284] = 249;
                                    $Length_prom = [128,129];                        
                                break;
                                case "18-20":
                                    $PROPER[284] = 250;
                                    $Length_prom = [128,129,130];                        
                                break;
                                case "18-20,5":
                                    $PROPER[284] = 251;
                                    $Length_prom = [128,129,130];                        
                                break;
                                case "18-21":
                                    $PROPER[284] = 252;
                                    $Length_prom = [128,129,130,131];                       
                                break;
                                case "18-21,5":
                                    $PROPER[284] = 253;
                                    $Length_prom = [128,129,130,131];                        
                                break;
                                case "18-22":
                                    $PROPER[284] = 254;
                                    $Length_prom = [128,129,130,131,132];                        
                                break;
                                case "18-22,5":
                                    $PROPER[284] = 255;
                                    $Length_prom = [128,129,130,131,132];                        
                                break;
                                case "18-23":
                                    $PROPER[284] = 256;
                                    $Length_prom = [128,129,130,131,132,133];
                                break;
                                case "18-23,5":
                                    $PROPER[284] = 257;
                                    $Length_prom = [128,129,130,131,132,133];                       
                                break;
                                case "18-24":
                                    $PROPER[284] = 258;
                                    $Length_prom = [128,129,130,131,132,133,134];                        
                                break;

                                case "18,5-19":
                                    $PROPER[284] = 259;
                                    $Length_prom = [129];                        
                                break;
                                case "18,5-19,5":
                                    $PROPER[284] = 260;
                                    $Length_prom = [129];                        
                                break;
                                case "18,5-20":
                                    $PROPER[284] = 261;
                                    $Length_prom = [129,130];                        
                                break;
                                case "18,5-20,5":
                                    $PROPER[284] = 262;
                                    $Length_prom = [129,130];                        
                                break;
                                case "18,5-21":
                                    $PROPER[284] = 263;
                                    $Length_prom = [129,130,131];                       
                                break;
                                case "18,5-21,5":
                                    $PROPER[284] = 264;
                                    $Length_prom = [129,130,131];                        
                                break;
                                case "18,5-22":
                                    $PROPER[284] = 265;
                                    $Length_prom = [129,130,131,132];                        
                                break;
                                case "18,5-22,5":
                                    $PROPER[284] = 266;
                                    $Length_prom = [129,130,131,132];                        
                                break;
                                case "18,5-23":
                                    $PROPER[284] = 267;
                                    $Length_prom = [129,130,131,132,133];
                                break;
                                case "18,5-23,5":
                                    $PROPER[284] = 268;
                                    $Length_prom = [129,130,131,132,133];                       
                                break;
                                case "18,5-24":
                                    $PROPER[284] = 269;
                                    $Length_prom = [129,130,131,132,133,134];                        
                                break;

                                case "19-19,5":
                                    $PROPER[284] = 270;
                                    $Length_prom = [129];                        
                                break;
                                case "19-20":
                                    $PROPER[284] = 271;
                                    $Length_prom = [129,130];                        
                                break;
                                case "19-20,5":
                                    $PROPER[284] = 272;
                                    $Length_prom = [129,130];                        
                                break;
                                case "19-21":
                                    $PROPER[284] = 273;
                                    $Length_prom = [129,130,131];                       
                                break;
                                case "19-21,5":
                                    $PROPER[284] = 274;
                                    $Length_prom = [129,130,131];                        
                                break;
                                case "19-22":
                                    $PROPER[284] = 275;
                                    $Length_prom = [129,130,131,132];                        
                                break;
                                case "19-22,5":
                                    $PROPER[284] = 276;
                                    $Length_prom = [129,130,131,132];                        
                                break;
                                case "19-23":
                                    $PROPER[284] = 277;
                                    $Length_prom = [129,130,131,132,133];
                                break;
                                case "19-23,5":
                                    $PROPER[284] = 278;
                                    $Length_prom = [129,130,131,132,133];                       
                                break;
                                case "19-24":
                                    $PROPER[284] = 279;
                                    $Length_prom = [129,130,131,132,133,134];                        
                                break;

                                case "19,5-20":
                                   $PROPER[284] = 280;
                                   $Length_prom = [130];                        
                                break;
                                case "19,5-20,5":
                                    $PROPER[284] = 281;
                                    $Length_prom = [130];                        
                                break;
                                case "19,5-21":
                                    $PROPER[284] = 282;
                                    $Length_prom = [130,131];                       
                                break;
                                case "19,5-21,5":
                                    $PROPER[284] = 283;
                                    $Length_prom = [130,131];                        
                                break;
                                case "19,5-22":
                                    $PROPER[284] = 284;
                                    $Length_prom = [130,131,132];                        
                                break;
                                case "19,5-22,5":
                                    $PROPER[284] = 285;
                                    $Length_prom = [130,131,132];                        
                                break;
                                case "19,5-23":
                                    $PROPER[284] = 286;
                                    $Length_prom = [130,131,132,133];
                                break;
                                case "19,5-23,5":
                                    $PROPER[284] = 287;
                                    $Length_prom = [130,131,132,133];                       
                                break;
                                case "19,5-24":
                                    $PROPER[284] = 288;
                                    $Length_prom = [130,131,132,133,134];                        
                                break;

                                case "20-20,5":
                                    $PROPER[284] = 289;
                                    $Length_prom = [130];                        
                                break;
                                case "20-21":
                                    $PROPER[284] = 290;
                                    $Length_prom = [130,131];                       
                                break;
                                case "20-21,5":
                                    $PROPER[284] = 291;
                                    $Length_prom = [130,131];                        
                                break;
                                case "20-22":
                                    $PROPER[284] = 292;
                                    $Length_prom = [130,131,132];                        
                                break;
                                case "20-22,5":
                                    $PROPER[284] = 293;
                                    $Length_prom = [130,131,132];                        
                                break;
                                case "20-23":
                                    $PROPER[284] = 294;
                                    $Length_prom = [130,131,132,133];
                                break;
                                case "20-23,5":
                                    $PROPER[284] = 295;
                                    $Length_prom = [130,131,132,133];                       
                                break;
                                case "20-24":
                                    $PROPER[284] = 296;
                                    $Length_prom = [130,131,132,133,134];                        
                                break;

                                case "20,5-21":
                                    $PROPER[284] = 297;
                                    $Length_prom = [131];                       
                                break;
                                case "20,5-21,5":
                                    $PROPER[284] = 298;
                                    $Length_prom = [131];                        
                                break;
                                case "20,5-22":
                                    $PROPER[284] = 299;
                                    $Length_prom = [131,132];                        
                                break;
                                case "20,5-22,5":
                                    $PROPER[284] = 300;
                                    $Length_prom = [131,132];                        
                                break;
                                case "20,5-23":
                                    $PROPER[284] = 301;
                                    $Length_prom = [131,132,133];
                                break;
                                case "20,5-23,5":
                                    $PROPER[284] = 302;
                                    $Length_prom = [131,132,133];                       
                                break;
                                case "20,5-24":
                                    $PROPER[284] = 303;
                                    $Length_prom = [131,132,133,134];                        
                                break;

                                case "21-21,5":
                                    $PROPER[284] = 304;
                                    $Length_prom = [131];                        
                                break;
                                case "21-22":
                                    $PROPER[284] = 305;
                                    $Length_prom = [131,132];                        
                                break;
                                case "21-22,5":
                                    $PROPER[284] = 306;
                                    $Length_prom = [131,132];                        
                                break;
                                case "21-23":
                                    $PROPER[284] = 307;
                                    $Length_prom = [131,132,133];
                                break;
                                case "21-23,5":
                                    $PROPER[284] = 308;
                                    $Length_prom = [131,132,133];                       
                                break;
                                case "21-24":
                                    $PROPER[284] = 309;
                                    $Length_prom = [131,132,133,134];                        
                                break;

                                case "21,5-22":
                                    $PROPER[284] = 310;
                                    $Length_prom = [132];                        
                                break;
                                case "21,5-22,5":
                                    $PROPER[284] = 311;
                                    $Length_prom = [132];                        
                                break;
                                case "21,5-23":
                                    $PROPER[284] = 312;
                                    $Length_prom = [132,133];
                                break;
                                case "21,5-23,5":
                                    $PROPER[284] = 313;
                                    $Length_prom = [132,133];                       
                                break;
                                case "21,5-24":
                                    $PROPER[284] = 314;
                                    $Length_prom = [132,133,134];                        
                                break;

                                case "22-22,5":
                                    $PROPER[284] = 315;
                                    $Length_prom = [132];                        
                                break;
                                case "22-23":
                                    $PROPER[284] = 316;
                                    $Length_prom = [132,133];
                                break;
                                case "22-23,5":
                                    $PROPER[284] = 317;
                                    $Length_prom = [132,133];                       
                                break;
                                case "22-24":
                                    $PROPER[284] = 318;
                                    $Length_prom = [132,133,134];                        
                                break;

                                case "22,5-23":
                                    $PROPER[284] = 319;
                                    $Length_prom = [133];
                                break;
                                case "22,5-23,5":
                                    $PROPER[284] = 320;
                                    $Length_prom = [133];                       
                                break;
                                case "22,5-24":
                                    $PROPER[284] = 321;
                                    $Length_prom = [133,134];
                                break;

                                case "23-23,5":
                                    $PROPER[284] = 322;
                                    $Length_prom = [133];                  
                                break;
                                case "23-24":
                                    $PROPER[284] = 323;
                                    $Length_prom = [133,134];
                                break;

                                case "15":
                                    $PROPER[284] = 324;
                                    $Length_prom = [118];                       
                                break;
                                case "15,5":
                                    $PROPER[284] = 325;
                                    $Length_prom = [118];                        
                                break;
                                case "16":
                                    $PROPER[284] = 326;
                                    $Length_prom = [119];                        
                                break;
                                case "16,5":
                                    $PROPER[284] = 327;
                                    $Length_prom = [119];
                                break;
                                case "17":
                                    $PROPER[284] = 328;
                                    $Length_prom = [127];                      
                                break;
                                case "17,5":
                                    $PROPER[284] = 329;
                                    $Length_prom = [127];                       
                                break;
                                case "18":
                                    $PROPER[284] = 330;
                                    $Length_prom = [128];                        
                                break;
                                case "18,5":
                                    $PROPER[284] = 331;
                                    $Length_prom = [128];                        
                                break;
                                case "19":
                                    $PROPER[284] = 332;
                                    $Length_prom = [129];                        
                                break;
                                case "19,5":
                                    $PROPER[284] = 333;
                                    $Length_prom = [129];
                                break;
                                case "20":
                                    $PROPER[284] = 334;
                                    $Length_prom = [130];                       
                                break;
                                case "20,50":
                                    $PROPER[284] = 335;
                                    $Length_prom = [130];                        
                                break;
                                case "21":
                                    $PROPER[284] = 336;
                                    $Length_prom = [131];                        
                                break;
                                case "21,5":
                                    $PROPER[284] = 337;
                                    $Length_prom = [131];                       
                                break;
                                case "22":
                                    $PROPER[284] = 338;
                                    $Length_prom = [132];                       
                                break;
                                case "22,5":
                                    $PROPER[284] = 339;
                                    $Length_prom = [132];
                                break;
                                case "23":
                                    $PROPER[284] = 340;
                                    $Length_prom = [133];                      
                                break;
                                case "23,5":
                                    $PROPER[284] = 341;
                                    $Length_prom = [133];                       
                                break;
                                case "24":
                                    $PROPER[284] = 342;
                                    $Length_prom = [134];                       
                                break;
                                case "24,5":
                                   $PROPER[284] = 343;
                                   $Length_prom = [134];                        
                                break;
                                case "15-17":
                                   $PROPER[284] = 344;
                                   $Length_prom = [188,119,127];                        
                                break;
                                 
                            }  

                            $LengtWidth = count($Width);//ширина браслета
                            $k=0;

                            while($LengtWidth<>0){
                                
                                switch($Width[$k]){//ширина браслета
                                    case "1":
                                        $PROPER[277] = 135;
                                    break;
                                    case "2":
                                        $PROPER[277] = 136;                       
                                    break;
                                    case "3":
                                        $PROPER[277] = 137;                        
                                    break;
                                    case "4":
                                        $PROPER[277] = 138;                        
                                    break;
                                    case "5":
                                        $PROPER[277] = 139;                        
                                    break;
                                    case "6":
                                        $PROPER[277] = 140;                        
                                    break;
                                    case "7":
                                        $PROPER[277] = 141;                        
                                    break;
                                    case "8":
                                        $PROPER[277] = 142;                        
                                    break;
                                    case "9":
                                        $PROPER[277] = 143;                        
                                    break;
                                    case "10":
                                        $PROPER[277] = 144;                        
                                    break;
                                    case "11":
                                        $PROPER[277] = 145;                        
                                    break;
                                }

                                foreach($Length_prom as &$PROPER[276]){
                                    

                                    // добавляем нужное кол-во торговых предложений
                                $arLoadProductArray = array(
                                    "IBLOCK_ID"      => 14, // IBLOCK торговых предложений
                                    "NAME"           => $strin[9],
                                    "ACTIVE"         => "Y",
                                    'PROPERTY_VALUES'=> $PROPER
                                    
                                    
                                    // Прочие параметры товара 
                                );

                                $product_offer_id = $ciBlockElement->Add($arLoadProductArray);
                                // проверка на ошибки
                                if (!empty($ciBlockElement->LAST_ERROR)) {
                                    echo "Ошибка добавления торгового предложения: ". $ciBlockElement->LAST_ERROR;
                                    die();
                                }

                                // Добавляем параметры к торг. предложению
                                CCatalogProduct::Add(
                                    array(
                                        "ID" => $product_offer_id,
                                        "QUANTITY" => 9999
                                    )
                                );

                                // Добавляем цены к торг. предложению
                                CPrice::Add(
                                    array(
                                        "CURRENCY" => "RUB",
                                        "PRICE" => $strin[7],
                                        "CATALOG_GROUP_ID" => 1,
                                        "PRODUCT_ID" => $product_offer_id
                                    )
                                );
                            }

                                $LengtWidth-- ;
                                $k++;
                            }

                            $LengtLengt--;
                            $kol++;
                        }
                    break;

                    case "Цепочки":

                        $PROPER[90] = null;
                        $PROPER[276] = null;
                        $PROPER[284] = null;

                        $LengtLength2 = count($Length2);//длина цепочки
                        $kol=0; 

                        while ($LengtLength2<>0){

                            switch($Length2[$kol]){//длина цепочки
                                case "35": 
                                    $Length2_prom = [146];
                                    $PROPER[285] = 346;
                                break;
                                case "40":
                                    $Length2_prom = [147];
                                    $PROPER[285] = 347;                       
                                break;
                                case "42":
                                    $Length2_prom = [148];
                                    $PROPER[285] = 348;                        
                                break;
                                case "45":
                                    $Length2_prom = [149];
                                    $PROPER[285] = 349;                        
                                break;
                                case "48":
                                    $Length2_prom = [150];
                                    $PROPER[285] = 350;                        
                                break;
                                case "50":
                                    $Length2_prom = [151];
                                    $PROPER[285] = 351;                        
                                break;
                                case "55":
                                    $Length2_prom = [152];
                                    $PROPER[285] = 352;                        
                                break;
                                case "60":
                                    $Length2_prom = [153];
                                    $PROPER[285] = 353;                        
                                break;
                                case "65":
                                    $Length2_prom = [154];
                                    $PROPER[285] = 354;                        
                                break;
                                case "70":
                                    $Length2_prom = [155];
                                    $PROPER[285] = 355;                        
                                break;

                                case "35+5":
                                    $Length2_prom = [146,147]; 
                                    $PROPER[285] = 356;
                                break;
                                case "40+5":
                                    $Length2_prom = [147,148,149];
                                    $PROPER[285] = 357;                       
                                break;
                                case "42+5":
                                    $Length2_prom = [148,149];
                                    $PROPER[285] = 358;                        
                                break;
                                case "45+5":
                                    $Length2_prom = [149,150,151];
                                    $PROPER[285] = 359;                        
                                break;
                                case "48+5":
                                    $Length2_prom = [150,151];
                                    $PROPER[285] = 360;                        
                                break;
                                case "50+5":
                                    $Length2_prom = [151,152];
                                    $PROPER[285] = 361;                        
                                break;
                                case "55+5":
                                    $Length2_prom = [152,153];
                                    $PROPER[285] = 362;                        
                                break;
                                case "60+5":
                                    $Length2_prom = [153,154];
                                    $PROPER[285] = 363;                        
                                break;
                                case "65+5":
                                    $Length2_prom = [154,155];
                                    $PROPER[285] = 364;                        
                                break;
                                case "70+5":
                                    $Length2_prom = [155];
                                    $PROPER[285] = 365;                        
                                break;
                            }

                            $LengtWidth = count($Width);//ширина браслета
                            $k=0; 

                            while($LengtWidth<>0){
                                
                                switch($Width[$k]){//ширина 
                                    case "1":
                                        $PROPER[277] = 135;
                                    break;
                                    case "2":
                                        $PROPER[277] = 136;                       
                                    break;
                                    case "3":
                                        $PROPER[277] = 137;                        
                                    break;
                                    case "4":
                                        $PROPER[277] = 138;                        
                                    break;
                                    case "5":
                                        $PROPER[277] = 139;                        
                                    break;
                                    case "6":
                                        $PROPER[277] = 140;                        
                                    break;
                                    case "7":
                                        $PROPER[277] = 141;                        
                                    break;
                                    case "8":
                                        $PROPER[277] = 142;                        
                                    break;
                                    case "9":
                                        $PROPER[277] = 143;                        
                                    break;
                                    case "10":
                                        $PROPER[277] = 144;                        
                                    break;
                                    case "11":
                                        $PROPER[277] = 145;                        
                                    break;
                                } 

                                foreach($Length2_prom as &$PROPER[283]){

                                    // добавляем нужное кол-во торговых предложений
                                $arLoadProductArray = array(
                                    "IBLOCK_ID"      => 14, // IBLOCK торговых предложений
                                    "NAME"           => $strin[9],
                                    "ACTIVE"         => "Y",
                                    'PROPERTY_VALUES'=> $PROPER
                                    
                                    // Прочие параметры товара 
                                );

                                $product_offer_id = $ciBlockElement->Add($arLoadProductArray);
                                // проверка на ошибки
                                if (!empty($ciBlockElement->LAST_ERROR)) {
                                    echo "Ошибка добавления торгового предложения: ". $ciBlockElement->LAST_ERROR;
                                    die();
                                }

                                // Добавляем параметры к торг. предложению
                                CCatalogProduct::Add(
                                    array(
                                        "ID" => $product_offer_id,
                                        "QUANTITY" => 9999
                                    )
                                );

                                // Добавляем цены к торг. предложению
                                CPrice::Add(
                                    array(
                                        "CURRENCY" => "RUB",
                                        "PRICE" => $strin[7],
                                        "CATALOG_GROUP_ID" => 1,
                                        "PRODUCT_ID" => $product_offer_id
                                    )
                                );}

                                $LengtWidth-- ;
                                $k++;
                            }
                            $LengtLength2--;
                            $kol++;
                        }
                    break;

                    default:
                        $PROPER[90] = null;
                        $PROPER[283] = null;
                        $PROPER[277] = null;
                        $PROPER[276] = null;
                        $PROPER[284] = null;
                        $PROPER[285] = null;
                        

                        // добавляем нужное кол-во торговых предложений
                        $arLoadProductArray = array(
                        "IBLOCK_ID"      => 14, // IBLOCK торговых предложений
                        "NAME"           => $strin[9],
                        "ACTIVE"         => "Y",
                        'PROPERTY_VALUES'=> $PROPER

                        );

                        $product_offer_id = $ciBlockElement->Add($arLoadProductArray);
                        // проверка на ошибки
                        if (!empty($ciBlockElement->LAST_ERROR)) {
                            echo "Ошибка добавления торгового предложения: ". $ciBlockElement->LAST_ERROR;
                            die();
                        }

                        // Добавляем параметры к торг. предложению
                        CCatalogProduct::Add(
                            array(
                                "ID" => $product_offer_id,
                                "QUANTITY" => 9999
                            )
                        );

                        // Добавляем цены к торг. предложению
                        CPrice::Add(
                            array(
                                "CURRENCY" => "RUB",
                                "PRICE" => $strin[7],
                                "CATALOG_GROUP_ID" => 1,
                                "PRODUCT_ID" => $product_offer_id
                            )
                        );
                    break;
                }
            }

        }
        
    }
             
   
   ?> 
      
    <form method="post"> 
        <input  name="Link"/>   
        <input type="submit" name="butimp" value="Импортировать товары" class="butimp"/> 
    </form> 

</body> 
</html> 


<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>