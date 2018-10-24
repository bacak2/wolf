<?php
/**
 * Moduł 
 * 
 * @author		Maciej Kura <Maciej.Kura@gmail.com>
 * @copyright           Copyright (c) 2012 Maciej Kura
 * @package		PanelPlus 
 * @version		1.0
 */

/*
 *  begin
 */

class ModulGenerateExcel {

    /*
     * ObjPHPExcel - obiekt PHPExcel;
     * object
     */
    private $ObjPHPExcel;
    
    /*
     * $SheetsTitles - lista tytułów arkuszy
     * struktura array ( id_arkusza => Nazwa ); // liczba musi odpowiadać docelowej ilości arkuszy
     * array
     */
    private $SheetsTitles = array();
    
    /*
     * $Headers - nagłówki do kolumn 
     * struktura array ( id_arkusza => array( 'nazwa pola z Query' => 'nazwa nagłówka' ) ); // liczba musi odpowiadać docelowej ilości arkuszy
     * array 
     */
    private $Headers = array();
    
    /*
     * $Querys - zapytania do bazy dla arkuszy
     * struktura array ( id_arkusza => query, )  // liczba musi odpowiadać docelowej ilości arkuszy
     * var array
     */
    private $Querys = array();
    
    /*
     * $Titles - nie wiem po co jeszcze :)
     * 
     */
    private $Titles = array();
    
    /*
     * $ObjTitles - tytuł arkusza kalkulacyjnego
     */
    
    private $ObjTitles = 'PANELplus';
    
    /*
     * czy automatycznie ustawić szerokość kolumny (true - dla wszystkich arkuszy array(ustawienie tylko dla wybranych)
     */
    
    private $AutoColumnDimensions = false;
    
    
    /*
     * $ObjColumns - parametry kolumny (na razie szerokość)
     * struktura (każda szer inna) array ( id_arkusza => array( id_kolumny => array('Width' => szerokość ) ), ... )  // liczba musi odpowiadać docelowej ilości arkuszy
     * szerokość - wartość całkowita lub auto
     * 
     * $ObjColumns 
     */
    
    
    
    
    private $ObjColumns = array();
    
    /*
     * $Orientation - układ strony danego arkusza
     * struktura array ( id_arkusza => ORIENTATION_LANDSCAPE, )  // liczba musi odpowiadać docelowej ilości arkuszy
     * 
     */
    private $Orientation = array();
    
    /*
     * Offset - przesunięcie o n kolumn w danym arkuszu
     * struktura array ( id_arkusza => przesunięcie, )  // liczba musi odpowiadać docelowej ilości arkuszy
     */
    
    private $Offset = array();
    
    private $StyleAlignment = 'VERTICAL_TOP';
    /*
     * $Functions - lista funkcji wykonywanych na rekordzie
     * 
     */
    
    private $Functions;
    
    /*
     * FunctionOnElementsBeforeMakeRow - lista funkcji wykonywanych na wszystkich elementach elementach
     * 
     */
    
    private $FunctionOnElementsBeforeMakeRow;
    /*
     * $ObjCreator - kreator obiektu
     * defaults 'PANELplus'
     * 
     */
    private $ObjCreator = 'PANELplus';
    
    private $Baza;
    
        function __construct(&$Baza) {
            $this->Baza = $Baza;
            include( SCIEZKA_PHPEXCEL.'PHPExcel.php' );
            $this->ObjPHPExcel = new PHPExcel();            
        }

        public function SetHeaders($Headers){
            $this->Headers = $Headers;
        }
        
        public function SetQuerys($Querys){
            $this->Querys=$Querys;
        }
        
        public function SetTitles($Titles){
            $$this->Titles=$Titles;
        }
        
        public function SetSheetsTitles($SheetsTitles){
            $this->SheetsTitles = $SheetsTitles;
        }
        
        public function SetObjTitles($ObjTitles){
            $this->ObjTitles=$ObjTitles;
        }
        
        public function SetColumns($Columns){
            $this->Columns=$Columns;
        }
        
        public function SetFunctions($Functions){
            $this->Functions=$Functions;
        }
        public function SetFunctionOnElementsBeforeMakeRow($FunctionOnElementsBeforeMakeRow){
            $this->FunctionOnElementsBeforeMakeRow = $FunctionOnElementsBeforeMakeRow;
        }
        
        public function SetObjCreator($ObjCreator){
            $this->ObjCreator = $ObjCreator;
        }
        
        public function SetOffset($Offset){
            $this->Offset = $Offset;
        }
        
        public function SetAutoColumnDimensions( $AutoColumnDimensions ){
            $this->AutoColumnDimensions = $AutoColumnDimensions;
        }
        
        public function SetDefaultStyle(){
            $this->ObjPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
            if(isset($this->StyleAlignment))
                $this->ObjPHPExcel->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::$this->StyleAlignment)->setWrapText(true);
            $this->ObjPHPExcel->getDefaultStyle()->getFont()->setSize(9);
        }
        public function SetProperties(){            
            $this->ObjPHPExcel->getProperties()->setCreator($this->ObjCreator)->setTitle($this->ObjTitles);
        }
        
        public function SetColumnDimensions($Columns){
            foreach($Columns as $ColumnKey=>$ColumnValue){
                if( isset($ColumnValue['Width']) || strtolower($ColumnValue['Width']) == 'auto' )
                    $this->ObjPHPExcel->getActiveSheet()->getColumnDimensionByColumn($ColumnKey)->setAutoSize( true );
                else
                    $this->ObjPHPExcel->getActiveSheet()->getColumnDimensionByColumn($ColumnKey)->setWidth( $ColumnValue['Width'] );
            }
        }
        
        
        public function AutoColumnDimensions($NumOfColumns){
            for($i=0; $i<$NumOfColumns; $i++){
                $this->ObjPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize( true );
            }
        }
        
        public function  GenerateExcel(){
            $this->SetProperties();
            $this->SetDefaultStyle();
//            error_reporting(E_ALL);
            $sheetId = 0;
            foreach($this->SheetsTitles as $SheetIndex=>$SheetTitle){
                if($sheetId > 0)
                    $this->ObjPHPExcel->createSheet(NULL, $sheetId);
                $this->ObjPHPExcel->setActiveSheetIndex($sheetId);
                $this->ObjPHPExcel->getActiveSheet()->setTitle(( isset($this->SheetsTitles[$SheetIndex]) ? $this->SheetsTitles[$SheetIndex] : 'Sheet-'.$SheetIndex) );                
//                $this->ObjPHPExcel->setActiveSheetIndexByName($SheetIndex); // ustawienie aktualnego arkusza
                if(isset($this->Orientation[$SheetIndex]))
                    $this->ObjPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::$this->Orientation[$SheetIndex]);
                if(!empty($this->ObjColumns[$SheetIndex]) && is_array($this->ObjColumns[$SheetIndex])){
                    $this->SetColumnDimensions($this->ObjColumns[$SheetIndex]);
                }elseif( $this->AutoColumnDimensions === true ){
                    if(isset($this->Headers[$SheetIndex]) && count($this->Headers[$SheetIndex]) > 0)
                        $this->AutoColumnDimensions( count($this->Headers[$SheetIndex]) );
                }elseif( is_array($this->AutoColumnDimensions) && isset($this->AutoColumnDimensions[$SheetIndex]) && $this->AutoColumnDimensions[$SheetIndex] === true){
                    
                }

                $i = 1;
                $NumRow = 0;
                $Offset = isset( $this->Offset[$SheetIndex] ) ? $this->Offset[$SheetIndex] : 0;
                if( isset($this->Headers[$SheetIndex]) && count($this->Headers[$SheetIndex]) > 0){
                    foreach( $this->Headers[$SheetIndex] AS $head_key=>$head_value)
                    {
                        $this->ObjPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( $NumRow+$Offset, $i, ( is_array($head_value) ? $head_value['naglowek'] :  $head_value));
                        $NumRow++;
                    }
                    //$this->Baza->Query($this->Querys[$SheetIndex]);
                    $Elements = $this->Baza->GetRows($this->Querys[$SheetIndex]);
                    //while( $row = $this->Baza->GetRow() ){
                    if(isset($this->FunctionOnElementsBeforeMakeRow[$SheetIndex])){
                        if(is_array($this->FunctionOnElementsBeforeMakeRow[$SheetIndex])){
                            foreach($this->FunctionOnElementsBeforeMakeRow[$SheetIndex] as $Func=>$Params){
                                if($Params !== NULL)
                                    $Elements = call_user_func_array($Func, array( $Elements, $Params ));
                                else
                                    $Elements = call_user_func_array($Func, array( $Elements ));
                            }
                        }elseif(is_string($this->FunctionOnElementsBeforeMakeRow[$SheetIndex])){
                            $Elements = call_user_func_array($this->FunctionOnElementsBeforeMakeRow[$SheetIndex], array( $Elements ));
                        }
                    }
                    foreach($Elements as $key=>$row){
                        $i++;
                        $NumRow = 0;
                        if(!empty( $this->Functions) ){
                            if( isset($this->Functions[$SheetIndex]) ){
                                if(is_array($this->Functions[$SheetIndex])){
                                    foreach($this->Functions[$SheetIndex] as $Func=>$Params){
                                        if($Params !== NULL)
                                            $row = call_user_func_array($Func, array( $row, $Params ));
                                        else
                                            $row = call_user_func_array($Func, array( $row ));
                                    }
                                }elseif(is_string($this->Functions[$SheetIndex])){
                                    $row = call_user_func_array($this->Functions[$SheetIndex], array( $row ));
                                }
                            }
                        }

                        foreach($this->Headers[$SheetIndex] AS $head_key=>$head_value){
                            if( is_array($head_value) ){
                                if(isset($head_value['function'])){
                                    call_user_func_array($head_value['function'], array( $row[$head_key] ));
                                }
                                if(isset($head_value['elementy'])){
                                    if($row[$head_key]!=NULL)
                                        $val = $head_value['elementy'][$row[$head_key]];
                                    else
                                        $val = '';
                                }else{
                                    $val = $row[$head_key];
                                }
                            }else{
                                $val = $row[$head_key];
                            }

                            $this->ObjPHPExcel->getActiveSheet()->setCellValueByColumnAndRow( $NumRow+$Offset, $i, $val );
                            $NumRow++;
                        }
                        unset($Elements[$key]);
                    }
                }
                $sheetId++;
            }
        }
        
        public function seve(){
            header('Content-Disposition: attachment; filename='.$this->ObjTitles.date('_Y_M_d_H_i_s', time()).'.xls');
            header('Content-Type: application/vnd.ms-excel');
            header('Cache-Control: max-age=86400, private');
            header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
            header('filename: '.$this->ObjTitles.date('_Y_m_d_H_i_s', time()).'xls');
            $objWriter = PHPExcel_IOFactory::createWriter($this->ObjPHPExcel, 'Excel5');
            ob_end_clean();
            $objWriter->save('php://output');

    }
    
    protected function GetTitleXLS($title)    {
        $title['Dnia'] = date('Y-m-d');
        $title['Raport'] = 'Raport';
        $title['ORDERplus'] = $title;
        return $title;
    }
}
?>
