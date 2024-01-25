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
                $Length = explode(",",$strin[4]);
                $Width = explode(",",$strin[5]);
                $Length2 = explode(",",$strin[4]);
                
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
                        

                        $LengtLengt = count($Length);//браслет
                        $kol=0;//количество повторений цикла
                        
                        while ($LengtLengt<>0){

                            switch($Length[$kol]){ //длина браслета
                                case "15":
                                    $PROPER[276] = 118;
                                break;
                                case "16":
                                    $PROPER[276] = 119;                       
                                break;
                                case "17":
                                    $PROPER[276] = 127;                        
                                 break;
                                case "18":
                                    $PROPER[276] = 128;                        
                                 break;
                                case "19":
                                    $PROPER[276] = 129;                        
                                 break;
                                case "20":
                                    $PROPER[276] = 130;                        
                                break;
                                case "21":
                                    $PROPER[276] = 131;                       
                                break;
                                case "22":
                                    $PROPER[276] = 132;                        
                                break;
                                case "23":
                                    $PROPER[276] = 133;                        
                                break;
                                case "24":
                                    $PROPER[276] = 134;                        
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

                        $LengtLength2 = count($Length2);//длина цепочки
                        $kol=0; 

                        while ($LengtLength2<>0){

                            switch($Length2[$kol]){//длина цепочки
                                case "35": 
                                    $PROPER[283] = 146;
                                break;
                                case "40":
                                    $PROPER[283] = 147;                       
                                break;
                                case "42":
                                    $PROPER[283] = 148;                        
                                break;
                                case "45":
                                    $PROPER[283] = 149;                        
                                break;
                                case "48":
                                    $PROPER[283] = 150;                        
                                break;
                                case "50":
                                    $PROPER[283] = 151;                        
                                break;
                                case "55":
                                    $PROPER[283] = 152;                        
                                break;
                                case "60":
                                    $PROPER[283] = 153;                        
                                break;
                                case "65":
                                    $PROPER[283] = 154;                        
                                break;
                                case "70":
                                    $PROPER[283] = 155;                        
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
                                } print_r($PROPER[283]);

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