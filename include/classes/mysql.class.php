<?php
/**
 * Obsługa bazy danych MySQL
 * 
 * @author		Lukasz Piekosz <mentat@mentat.net.pl>
 * @copyright	Copyright (c) 2004-2007 ARTplus
 * @package		PanelPlus
 * @version		1.0
 */

/**
 * Parametry połączenia z bazą danych
 *
 */
class DBConnectionSettings {
        /**
         * Host bazy
         * @var string
         */
	public $Host;
        /**
         * Port
         * @var string
         */
	public $Port;
        /**
         * Nazwa bazy
         * @var string
         */
	public $Schema;
        /**
         * Nazwa użytkownika
         * @var string
         */
	public $User;
        /**
         * Hasło do bazy
         * @var string
         */
	public $Password;
        /**
         * konstruktor
         * @param DBConnectionSettings $DBConnectionSettings
         */
	function __construct($DBConnectionSettings) {
		$this->Host = (isset($DBConnectionSettings['host']) ? $DBConnectionSettings['host'] : '');
		$this->Port = (isset($DBConnectionSettings['port']) ? $DBConnectionSettings['port'] : '');
		$this->Schema = (isset($DBConnectionSettings['schema']) ? $DBConnectionSettings['schema'] : '');
		$this->User = (isset($DBConnectionSettings['user']) ? $DBConnectionSettings['user'] : '');
		$this->Password = (isset($DBConnectionSettings['password']) ? $DBConnectionSettings['password'] : '');
	}
}

/**
 * Obsługa bazy danych MySQL
 *
 */
class DBMySQL {
        /**
         * Obiekt DBConnectionSettings
         * @var DBConnectionSettings
         */
	private $ConnectionSettings;
        /**
         * Połączenie z bazą
         * @var
         */
	private $Connection = null;
        /**
         *
         * @var Mysql Result
         */
	private $Result = null;
        /**
         * Numer ostatniego błędu zapytania Mysql
         * @var int
         */
	private $LastErrorNumber = 0;
        /**
         * Ostatni błąd zapytania Mysql
         * @var string
         */
	private $LastError = '';
        /**
         * Ilość zwróconych rekordów z zapytania
         * @var int
         */
	private $NumRows = 0;
        /**
         * Ilość zmienionych rekordów przez zapytania
         * @var int
         */
	private $AffectedRows = 0;
        /**
         * ID ostatnio wprowadzonego rekordu
         * @var int
         */
	private $LastInsertID = 0;
        /**
         * Numer obecnego wiersza
         * @var int
         */
	private $CurrentRow = 0;
        /**
         * Ustawienie czy zapytania mają być zapisywane do logów czy nie
         * @var boolean
         */
	private $LogQuery = false;
        /**
         * Dodatkowe opisy błędów Mysql -> wyświetlane na stronie gdy wystąpi dany błąd
         * @var array
         */
	private $ErrorDescriptions = array(
		1062 => 'Próba dodania elementu o parametrach, które muszą być unikalne a istnieje już element który je posiada.',
		1451 => 'Nie można usunąć elementu do którego odnoszą się inne elementy.'
	);

        /**
         * Konstruktor
         * @param DBConnectionSettings $ConnectionSettings
         */
	function __construct(DBConnectionSettings &$ConnectionSettings) {
		$this->ConnectionSettings = $ConnectionSettings;
		$this->Connect();
	}

        /**
         * Wykonuje połączenie z bazą danych
         * @return boolean
         */
	function Connect() {
		if (!$this->Connected()) {
			if ($this->Connection = mysql_connect($this->ConnectionSettings->Host.($this->ConnectionSettings->Port ? ':'.$this->ConnectionSettings->Port : ''), $this->ConnectionSettings->User, $this->ConnectionSettings->Password)) {
				mysql_selectdb($this->ConnectionSettings->Schema, $this->Connection);
				mysql_query("SET NAMES 'utf8'");
				return true;
			}
			return false;
		}
		return true;
	}

        /**
         * Sprawdza czy jest połączenie z bazą
         * @return boolean
         */
	function Connected() {
		return ($this->Connection && mysql_ping($this->Connection));
	}

        /**
         * Wykonuje zapytanie do bazy
         * @param string $Query
         * @return boolean
         */
	function Query($Query) {
		if ($this->LogQuery) {
			error_log('SQL Query: '.htmlspecialchars($Query, ENT_QUOTES));
		}
		if (!$this->Connect()) {
			return false;
		}
		$this->NumRows = 0;
		$this->CurrentRow = 0;
		$this->LastError = '';
		if ($this->Result = mysql_query($Query, $this->Connection)) {
			if ($this->Result !== true) {
				$this->NumRows = mysql_num_rows($this->Result);
			}
			elseif (!($this->LastInsertID = mysql_insert_id($this->Connection))) {
				$this->AffectedRows = mysql_affected_rows($this->Connection);
			}
			return true;
		}
		else {
			$this->LastError = mysql_error();
			$this->LastErrorNumber = mysql_errno();
			if ($this->LogQuery) {
				error_log("SQL Error $this->LastErrorNumber: $this->LastError");
			}
			return false;
		}
	}
        /**
         * Pobiera wiersz zwrocony przez zapytanie Mysql w formie tablicy
         * @param int $RowNumber
         * @param $ResultType
         * @return array or false
         */
	function GetRow($RowNumber = -1, $ResultType = MYSQL_ASSOC) {
		if ($RowNumber < 0) {
			if ($this->CurrentRow >= $this->NumRows) {
				return false;
			}
			$this->CurrentRow++;
			return mysql_fetch_array($this->Result, $ResultType);
		}
		elseif ($RowNumber <= $this->NumRows) {
			$this->CurrentRow = $RowNumber;
			mysql_data_seek($this->Result, $this->CurrentRow);
			return mysql_fetch_array($this->Result, $ResultType);
		}
		else {
			return false;
		}
	}

        /**
         * Pobiera wiersz zwrocony przez zapytanie Mysql w formie obiektu
         * @param int $RowNumber
         * @return mysql object or false
         */
	function GetObject($RowNumber = -1) {
		if ($RowNumber < 0) {
			if ($this->CurrentRow >= $this->NumRows) {
				return false;
			}
			$this->CurrentRow++;
			return mysql_fetch_object($this->Result);
		}
		elseif ($RowNumber <= $this->NumRows) {
			$this->CurrentRow = $RowNumber;
			mysql_data_seek($this->Result, $this->CurrentRow);
			return mysql_fetch_object($this->Result);
		}
		else {
			return false;
		}
	}
        /**
         * Zwraca rezultat zapytania mysql
         * @param <type> $ResultType
         * @return DBQueryResult
         */
	function GetResult($ResultType = MYSQL_ASSOC) {
		if ($this->NumRows > 0) {
			$Result = array();
			mysql_data_seek($this->Result, 0);
			while ($Wiersz = mysql_fetch_array($this->Result, $ResultType)) {
				$Result[] = $Wiersz;
			}
			return new DBQueryResult($Result);
		}
		else {
			return false;
		}
	}

        /**
         * Zwraca pojedynczą wartość z zapytania
         * @param string $Query
         * @return mysql_result or false
         */
	function GetValue($Query) {
		$this->Query($Query);
		if ($this->NumRows > 0) {
			return mysql_result($this->Result, 0 ,0);
		}
		else {
			return false;
		}
	}
        /**
         * Zwraca rekordy z zapytania w formie tablicy gdzie pierwsza kolumna z zapytania to klucz a druga to wartość
         * Najczęściej wykorzystywana przy pobieraniu listy do selecta
         * @param string $Query
         * @return array or false
         */
	function GetOptions($Query) {
		$this->Query($Query);
		if ($this->NumRows > 0) {
			$Result = array();
			mysql_data_seek($this->Result, 0);
			while ($Wiersz = mysql_fetch_row($this->Result)) {
				$Result[$Wiersz[0]] = $Wiersz[1];
			}
			return $Result;
		}
		else {
			return false;
		}
	}
        /**
         * Zwraca rekord z zapytania w formie tablicy
         * @param string $Query
         * @return array or false
         */
        function GetData($Query){
            $this->Query($Query);
            return $this->GetRow();
        }
        /**
         * Zwraca rekordy zwrócone przez zapytanie w formie tablicy
         * @param string $Query
         * @return array or false
         */
        function GetRows($Query) {
		$this->Query($Query);
		if ($this->NumRows > 0) {
                    $Result = array();
                    mysql_data_seek($this->Result, 0);
                    while ($Wiersz = mysql_fetch_array($this->Result, MYSQL_ASSOC)) {
                        $Result[] = $Wiersz;
                    }
                    return $Result;
		}
		else {
                    return false;
		}
	}

        /**
         * Zwraca rekordy zwrócone przez zapytanie w formie tablicy gdzie wartościami jest kolumna podana w zapytaniu
         * @param string $Query
         * @return array or false
         */
	function GetValues($Query) {
		$this->Query($Query);
		if ($this->NumRows > 0) {
			$Result = array();
			mysql_data_seek($this->Result, 0);
			while ($Wiersz = mysql_fetch_row($this->Result)) {
				$Result[] = $Wiersz[0];
			}
			return $Result;
		}
		else {
			return false;
		}
	}
	/**
         * Zwraca rekordy zwrócone przez zapytanie w formie tablicy gdzie kluczem jest podane w parametrze pole
         * @param string $Query
         * @param string $PoleID
         * @return array or false
         */
	function GetResultAsArray($Query, $PoleID){
		$Result = array();
		$this->Query($Query);
		while($Element = $this->GetRow()){
			$Result[$Element[$PoleID]] = $Element;
		}
		return $Result;
	}

        /**
         * Zwraca id ostatnio dodanego rekordu
         * @return int
         */
	function GetLastInsertID() {
		return $this->LastInsertID;
	}

        function GetAffectedRows() {
		return $this->AffectedRows;
	}
        
        /**
         * Zwraca ostatni błąd zapytania mysql
         * @return string
         */
	function GetLastError() {
		return $this->LastError;
	}

        /**
         * Zwraca numer ostatniego błędu zapytania mysql
         * @return int
         */
	function GetLastErrorNumber() {
		return $this->LastErrorNumber;
	}

        /**
         * Zwraca ilość rekordów zwróconych przez zapytanie
         * @return int
         */
	function GetNumRows() {
		return $this->NumRows;
	}

        /**
         * Zwraca treść informującą o błędzie
         * @return string
         */
	function GetLastErrorDescription() {
		if ($this->LastErrorNumber) {
			if (isset($this->ErrorDescriptions[$this->LastErrorNumber])) {
				return $this->ErrorDescriptions[$this->LastErrorNumber];
			}
			else {
				return 'Numer problemu zwrócony przez baze danych: '.$this->LastErrorNumber."<br />".$this->LastError;
			}
		}
		return false;
	}

        /**
         * Zamknięcie połączenia mysql
         */
	function Close() {
		if ($this->Connection) {
			if ($this->Result) {
				mysql_free_result($this->Result);
				$this->Result = null;
			}
			mysql_close($this->Connection);
			$this->Connection = null;
		}
	}

        /**
         * Przygotowuje zapytanie INSERT
         * @param string $Table - nazwa tabeli
         * @param array $Fields - pola wstawiane gdzie kluczami są nazwy pól w tabeli
         * @return String
         */
	function PrepareInsert($Table, $Fields) {
		$Result = "INSERT INTO $Table SET";
		foreach ($Fields as $FieldName => $Value) {
			$Result .= " $FieldName = ".("$Value" == "My_null" ? "null," : "'".mysql_real_escape_string($Value)."',");
		}
		return rtrim($Result, ',');
	}

        /**
         * Przygotowuje zapytanie UPDATE
         * @param string $Table - nazwa tabeli
         * @param array $Fields - pola wstawiane gdzie kluczami są nazwy pól
         * @param array $WhereFields - pola do warunku WHERE gdzie kluczami są nazwy pól z tabeli
         * @return String
         */
	function PrepareUpdate($Table, $Fields, $WhereFields) {
		$Result = "UPDATE $Table SET";
		foreach ($Fields as $FieldName => $Value) {
                        $Result .= " $FieldName = ".("$Value" == "My_null" ? "null," : "'".mysql_real_escape_string($Value)."',");
		}
		$Result = rtrim($Result, ',');
		if (count($WhereFields)) {
			$Result .= " WHERE";
			foreach ($WhereFields as $FieldName => $Value) {
				$Result .= " $FieldName = '".mysql_real_escape_string($Value)."' AND";
			}
		}
		return rtrim($Result, ' AND');
	}

        /**
         * Włącza i wyłącza zapisywanie zapytań do logów
         * @param boolean
         */
	function EnableLog($Enable = true) {
		$this->LogQuery = $Enable;
	}

        /**
         * Wstawia tablicę wartości do tabeli (używane np. przy zapisywaniu checkboxów)
         * @param string $Table - nazwa tabeli
         * @param string $IDField - nazwa pola id
         * @param string $ValueField - nazwa pola wartości
         * @param int $ID
         * @param array $Values - wartości do zapisania z podanym $ID
         */
	function SaveSet1n($Table, $IDField, $ValueField, $ID, $Values) {
		if ($this->Query("DELETE FROM $Table WHERE $IDField = '$ID'")) {
			if (is_array($Values) && count($Values)) {
				foreach ($Values as $Value) {
					$this->Query("INSERT INTO $Table($IDField, $ValueField) VALUES('$ID', '$Value')");
				}
			}
		}
	}
        /**
         * Pobranie wartości z tabeli powiązanych z podanym $ID
         * @param string $Table - nazwa tabeli
         * @param string $IDField - pole id
         * @param string $ValueField - pole wartości
         * @param int $ID - szukane id
         * @return array
         */
	function GetSet1n($Table, $IDField, $ValueField, $ID) {
		return $this->GetValues("SELECT $ValueField FROM $Table WHERE $IDField = '$ID'");
	}
	/**
         * Wstawia tablicę wartości do tabeli (używane np. przy zapisywaniu checkboxów typu n => n)
         * @param string $GroupTable - tabela grup
         * @param string $Table - nazwa tabeli
         * @param string $GroupNameField - pole nazwy grupy
         * @param string $GroupIDField - pole id grupy
         * @param string $ValueField - nazwa pola wartości
         * @param array $Values - wartości do zapisania z podanym $ID
         */
	function SaveSetnn($GroupTable, $Table, $GroupNameField, $GroupIDField, $ValueField, $Values) {
		if ($this->Query("INSERT INTO $GroupTable($GroupNameField) VALUES(NULL)")) {
			$ID = $this->LastInsertID;
			if (is_array($Values) && count($Values)) {
				foreach ($Values as $Value) {
					$this->Query("INSERT INTO $Table($GroupIDField, $ValueField) VALUES('$ID', '$Value')");
				}
			}
			return $ID;
		}
		return false;
	}

        /**
         * Pobranie wartości z tabeli powiązanej
         * @param string $Table - nazwa tabeli
         * @param string $GroupIDField - nazwa pola id
         * @param string $ValueField - pole wartości
         * @param int $ID - id
         * @return array
         */
	function GetSetnn($Table, $GroupIDField, $ValueField, $ID) {
		return $this->GetValues("SELECT $ValueField FROM $Table WHERE $GroupIDField = '$ID'");
	}
	/**
         * Zapisuje tablicę wartości
         * @param string $Table - nazwa tabeli
         * @param array $Values - tablica wartości
         * @param array $Where - tablica pól użytych do warunku WHERE
         */
	function SaveValues($Table, $Values, $Where = null){
		if(is_array($Values) && count($Values)){
			$SetValue = "";
			foreach($Values as $klucz => $wartosc){
				$SetValue .= " $klucz = '$wartosc',";
			}
			$SetValue = rtrim($SetValue,',');
			if(!is_null($Where) && is_array($Where) && count($Where)){
				$Warunek = "";
				foreach($Where as $klucz => $wartosc){
					$Warunek .= ($Warunek == "" ? " " : " AND ")."$klucz = '$wartosc'";
				}
				$this->Query("UPDATE $Table SET$SetValue".($Warunek != "" ? " WHERE $Warunek" : ""));
			}else{
				$this->Query("INSERT INTO $Table SET$SetValue");
			}
		}
	}
        /* funkcja rozpoczynająca transakcje w mysql */

        function RozpocznijTranzakcje(){
            $this->Query("START TRANSACTION");
        }
/* funkcja konczaca transakcje w mysql */

        function ZakonczTransakcje( $status ){
            if( $status == true)
                $this->Query ("COMMIT");
            else
                $this->Query ( 'ROLLBACK' );

        }

}

/**
 * Wynik zapytania jako tablica
 *
 */
class DBQueryResult {
	public $Data;

	function __construct(&$Data = null) {
		if (is_array($Data)) {
			$this->Data = $Data;
		}
	}
}

?>
