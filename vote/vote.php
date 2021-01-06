<?php

//buat class dan koneksi dengan database
class Vote {
		private $_host 	      = 'localhost';    //host
		private $_database    = 'vote_db';      //nama db
		private $_dbUser      = 'root';         //uname
		private $_dbPwd       = '';				//pass
		private $_con 	   	  = false;          //buat koneksi
		private $_optionTable = 'opsi';         //tabel
		private $_voterTable  = 'pemilih';      //tabel
		
        //buat construct
        public function __construct()
        {
	        if(!$this->_con)
	        {
	            $this->_con = mysqli_connect($this->_host,$this->_dbUser,$this->_dbPwd);
	            if(!$this->_con){
	            	die('Could not connect: ' . mysqli_error());
	            }
	            mysqli_select_db($this->_con,$this->_database)|| die('Could not select database: ' . mysqli_connect_error());
	            
	        } 
        }
		
        //buat private functions
		private function  _query($sql)
		{
			$result = mysqli_query($this->_con,$sql);
        	if(!$result){
        		die('unable to query'. mysqli_error());
        	} 
        	$data= array();
	        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	        	$data[]=$row;			  
			}
			return $data; 
		}
		
		private function _alreadyVote($ip)
		{
			$sql    = 'SELECT * FROM '.$this->_voterTable.' WHERE ip="'.$ip.'"';
			$result = $this->_query($sql);
			return sizeof($result)>0;
		}
		
		//public functions
        public function vote($optionId)
        {        	
        	$ip  = $_SERVER['REMOTE_ADDR'];
	        	if(!$this->_alreadyVote($ip)){
	        	$sql ='INSERT INTO '.$this->_voterTable.' (id,option_id,ip) '.' VALUES(NULL,"'.mysqli_real_escape_string($this->_con,$optionId).'","'.mysqli_real_escape_string($this->_con,$ip).'")';
	        	
	        	$result = mysqli_query($this->_con,$sql);
	        	if(!$result){
	        		die('unable to insert'. mysqli_connect_error());
	        	}
        	}        
        }
        
        public function getList()
        {
        	$sql    = 'SELECT * FROM '.$this->_optionTable;
        	return	$this->_query($sql);        	
        }
        
        public function showResults()
        {
        	$sql =
        	'SELECT * FROM  '.$this->_optionTable.' LEFT JOIN '.'(SELECT option_id, COUNT(*) as number FROM  '.$this->_voterTable.' GROUP BY option_id) as votes'.
        	' ON '.$this->_optionTable.'.id = votes.option_id';
        	return	$this->_query($sql);
        }
		
        public function getTotal()
        {
        	$sql    = 'SELECT count(*)as total FROM '.$this->_voterTable;
			$data	= $this->_query($sql);
			if(sizeof($data)>0){
				return $data[0]['total'];
			}
			return 0;
        }
}
?>