<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
 * FTP Upload Wrapper class
 *
 *
 * @package        Cdn Utility
 * @subpackage     FTP
 * @category       Libraries 
 * Location        application/libraries/cdn/libs
 *
 * @author         <zugunan@gmail.com>, http://sugunan.com
 * @author         Hayesha Somarathne - <hayeshais@gmail.com>, http://thoughtsandideas.wordpress.com
 *
 * Created on      20-04-2012, 3:30PM by <zugunan@gmail.com>
 * Updated on      23-04-2012, 8:55AM by Hayesha Somarathne - <hayeshais@gmail.com>
 *
 * License: GNU LESSER GENERAL PUBLIC LICENSE, Version 3
 *
 * */
 
class lib_ftp {
    
	 /**
	 * CodeIgniter global  
	 *
	 * @var string 
	 */
	 protected $CI; 

    private $_config=array();
    
    function __construct() 
    {
        $this->CI = & get_instance();        
        
        $this->CI->load->config('config', TRUE);
        $config_arr = $this->CI->config->item('config');
        $this->_config['hostname'] = $config_arr['ftp']['media_file_path'];
        $this->_config['username'] = $config_arr['ftp']['media_username'];
        $this->_config['password'] = $config_arr['ftp']['media_password'];
        $this->_config['port'] = $config_arr['ftp']['media_port']; 
        
        $this->_config['server_root'] =$config_arr['server_root'];
        $this->_config['media_root'] =$config_arr['media_root'];
        $this->_config['media_web_path'] =$config_arr['media_web_path'];
                
        $this->CI->load->library('ftp');
        $a=$this->CI->ftp->connect($this->_config);           
    }
    
   
    /**
     * This is to move file from one server to another
     *
     * @access	public
     * @param	string
     * @return	string
     */
    public function file_move($local=NULL,$server=NULL,$file_remove=0,$server_dir_path=NULL)
    {   
        
        $local_dir = $this->_config['server_root'];
        $local_file = $local_dir . $local;
        $cdn_dir = $this->_config['media_root'] . $server;
        $server_dir_path_new=$this->_config['media_root'] .$server_dir_path;
        $ext = end(explode('.', $local));
        $cash_name = time() . md5(basename($local_file)) . mt_rand(1, 1000) . '.' . $ext;
        $cdn_file = $cdn_dir . $cash_name;
        
        
        if(!is_dir($server_dir_path_new))
       {
        	$this->CI->ftp->mkdir($server_dir_path_new, DIR_WRITE_MODE);
        	$this->CI->ftp->chmod($server_dir_path_new, DIR_WRITE_MODE);
        	
        }
        	
        $this->create_dir($server);
      
        $this->CI->ftp->upload($local_file,$cdn_file , 'auto' , 0777);
        
        if (is_file($local_file)&&$file_remove==0)
        {
            unlink($local_file);
        }       

        return $cash_name;
    }
    
    
    /**
     * This is to move file from one server to another
     *
     * @access	public
     * @param	string
     * @return	string
     */
    public function file_move_custom_name($local=NULL,$server=NULL,$name=NULL,$file_remove=0)
    {   
        
        $local_dir = $this->_config['server_root'];
        $local_file = $local_dir . $local;
        $cdn_dir = $this->_config['media_root'] . $server;
        $ext = end(explode('.', $local));
        $cash_name = time() . rand(1, 1000) . '.' . $ext;
        $cdn_file = $cdn_dir.$name;
        

        $this->create_dir($server);

        $this->CI->ftp->upload($local_file,$cdn_file , 'auto' , 0777);
        
        if (is_file($local_file)&&$file_remove==0)
        {
            unlink($local_file);
        }       

        return $cash_name;
    }

    
    /**
     * This is to duplicate a folder on server
     *
     * @access	public
     * @param	string   $source
     * @param	string   $destination
     */
    public function mirror_dir($source,$destination)
    {
        $data['source'] = $source;
        $data['destination'] = $destination;
        $url = $this->_config['media_web_path'] . "mirror.php";
        
        $this->curl($url,$data);
    }

    
    /**
     * This is to copy file from one server to another
     * @author gayan
     * @access	public
     * @param	string
     */
    public function file_tmp_copy($filename=NULL,$server=NULL)
    {
            	//$a=$this->CI->ftp->connect($this->_config);
            	$local_dir = $this->_config['server_root'];
            	$local_dir = $local_dir. $server.'files/tmp/' ;
            	$local_file = $local_dir  . $filename;
            	
            	
	            $file_list = $this->CI->ftp->list_files($local_dir);
            	if (count($file_list)==0)
            	{
            		mkdir($local_dir, 0777);
            		
            	//	$this->fileWrite($local_dir . 'files/tmp/index.php',"");
            	//	$this->CI->ftp->upload($local_dir . 'files/tmp/index.php',$cdn_dir . 'index.php' , 'auto', 0777);
            	}
            	
            	$this->CI->ftp->upload($local_file , 'auto' , 0777);
            	
            	return $local_file;   	
    	
    }
    
    /**
     * This is to remove file by path
     *
     * @access	public
     * @param	string
     * @return	void
     */
    public function file_remove($path=NULL,$file=NULL)
    {   

        if (isset($file) && isset($path))
        {
            $file_data = $this->CI->cdn_model->get_file_by_path($path,$file);
        }
        
        $cdn_path = $this->_config['media_root'] . $path;
        
        $file_list = $this->CI->ftp->list_files($cdn_path);
        
        if (in_array($cdn_file, $file_list)) {
            $this->CI->ftp->delete_file($cdn_file);
            return true;
        }
        else
        {
            return false;
        }   
    }
    
    public function fileWrite($myFile,$stringData)
    {	
        $fh = fopen($myFile, 'w');
        fwrite($fh, $stringData);
        fclose($fh);
    }
    
    /**
     * This is to clear a local folder
     *
     * @access	public
     * @param	string
     * @return	string
     */
    public function local_folder_clear($path)
    {
        $folder_path = $this->_config['server_root'] . $path;
        $this->deleteAll($folder_path);
    }
    
    private function deleteAll($directory, $empty = false) {
        if(substr($directory,-1) == "/") {
            $directory = substr($directory,0,-1);
        }

        if(!file_exists($directory) || !is_dir($directory)) {
            return false;
        } elseif(!is_readable($directory)) {
            return false;
        } else {
            $directoryHandle = opendir($directory);

            while ($contents = readdir($directoryHandle)) {
                if($contents != '.' && $contents != '..') {
                    $path = $directory . "/" . $contents;

                    if(is_dir($path)) {
                        deleteAll($path);
                    } else {
                        unlink($path);
                    }
                }
            }

            closedir($directoryHandle);

            if($empty == false) {
                if(!rmdir($directory)) {
                    return false;
                }
            }

            return true;
        }
    } 
    
    /**
     * This is to close ftp connection
     *
     * @access	public
     * @param	void
     * @return	void
     */
    function __destruct() 
    {
        $this->CI->ftp->close();
    }

    
    
    
    
    /**
     * This is to move file from one server to another with local image name
     *
     * @access	public
     * @param	string
     * @return	string
     */
    public function file_move_CDN($local=NULL,$server=NULL,$file_remove=0,$server_dir_path=NULL,$file_name=null)
    {   

        //$a=$this->CI->ftp->connect($this->_config);
        $local_dir = $this->_config['server_root'];
        $local_file = $local_dir . $local;
        $cdn_dir = $this->_config['media_root'] . $server;
        $server_dir_path_new=$this->_config['media_root'] .$server_dir_path;
        $ext = end(explode('.', $local));
        $cash_name = $file_name ;
        $cdn_file = $cdn_dir . $cash_name;
        
        if(!is_dir($server_dir_path_new))
       {
        	$this->CI->ftp->mkdir($server_dir_path_new, DIR_WRITE_MODE);
        	$this->CI->ftp->chmod($server_dir_path_new, DIR_WRITE_MODE);
        	
        }
        	
        	$file_list = $this->CI->ftp->list_files($cdn_dir);
        if (count($file_list)==0) 
        {
            $this->CI->ftp->mkdir($cdn_dir, DIR_WRITE_MODE);
            $this->CI->ftp->chmod($cdn_dir, DIR_WRITE_MODE);
            $this->fileWrite($local_dir . 'files/tmp/index.php',"");
            $this->CI->ftp->upload($local_dir . 'files/tmp/index.php',$cdn_dir . 'index.php' , 'auto', 0777);
        }
      
        $this->CI->ftp->upload($local_file,$cdn_file , 'auto' , 0777);
        
        if (is_file($local_file)&&$file_remove==0)
        {
            unlink($local_file);

        }       
        
        return $cash_name;

        
        
    }
    
    /**
	 * This is to run curl and execute http post
	 *
	 * @param string,array
	 * @access public
	 * @return string
	 **/
	private function curl($url,$data)
	{
		$fields = $data; //assign postable data key value pair
		$fields_string = "";

		/**
		 * building post string
		 */
		$i=0;
		foreach($fields as $key=>$value)
		{			
			$i++;
			$fields_string .= $key.'='.urlencode($value);
			if($i<count($fields))
			{
				$fields_string .= '&';
			}			
		}

		$ch = curl_init();
		
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
		
		$result = curl_exec($ch);
		
		curl_close($ch);
	}
	
	/**
     * This is to download file from another server
     *
     * @access	public
     * @param	string
     * @return	string
     */
    public function file_download($url,$local_name)
    {
        $local_dir = $this->_config['server_root'];
		$file = $local_dir . $local_name;
		file_put_contents($file, file_get_contents($url));
    }
    
    public function file_download_from_server($target,$destination){
        //$this->CI->ftp->connect($this->_config);
        $down = $this->CI->ftp->download($target,$destination);
        return $down;
    }
    
    /**
     * This is to move file within the server
     *
     * @access	public
     * @param	string
     * @return	string
     */
    function file_move_server($source=NULL,$destination=NULL,$file_remove=0,$server_dir_path=NULL)
    {   
    	$a=$this->CI->ftp->connect($this->_config);
        $local_dir = $this->CI->config->item('server_root');
        $source_file = $this->CI->config->item('media_root'). $source;
        $cdn_dir = $this->CI->config->item('media_root') . $destination;
        $server_dir_path_new=$this->CI->config->item('media_root') .$server_dir_path;
        $ext = end(explode('.', $source));
        $cash_name = time() . rand(1, 1000) . '.' . $ext;
        $cdn_file = $cdn_dir . $cash_name;
        
        if(!is_dir($server_dir_path_new))
       {
        	$this->CI->ftp->mkdir($server_dir_path_new, DIR_WRITE_MODE);
        	$this->CI->ftp->chmod($server_dir_path_new, DIR_WRITE_MODE);
        	
        }
        	
        	$file_list = $this->CI->ftp->list_files($cdn_dir);
        if (count($file_list)==0) 
        {
            $this->CI->ftp->mkdir($cdn_dir, DIR_WRITE_MODE);
            $this->CI->ftp->chmod($cdn_dir, DIR_WRITE_MODE);
            $this->fileWrite($local_dir . 'files/tmp/index.php',"");
            $this->CI->ftp->upload($local_dir . 'files/tmp/index.php',$cdn_dir . 'index.php' , 'auto', 0777);
        }
        $move = $this->CI->ftp->move($source_file,$cdn_file);
        if($move){
            return $cash_name;
        }else{
            return false;
        }
    }
    
    /**
     * This is to create folder structure
     *
     * @access	public
     * @param	string
     */
    public function create_dir($path)
    {
    	$server_path = $this->_config['media_root'];
    	$local_dir = $this->_config['server_root'];
    	$path_array = explode("/",$path);
    	
    	$progress_path = "";
    	foreach($path_array as $path_val)
		{			
			if($path_val!="")
			{
				$progress_path .= $path_val."/";
				$file_list = $this->CI->ftp->list_files($server_path.$progress_path);
				
				if (count($file_list)==0)
		        {
		            $this->CI->ftp->mkdir($server_path.$progress_path, DIR_WRITE_MODE);
		            $this->CI->ftp->chmod($server_path.$progress_path, DIR_WRITE_MODE);
		            $this->fileWrite($local_dir . 'files/tmp/index.php',"");
		            $this->CI->ftp->upload($local_dir . 'files/tmp/index.php',$server_path.$progress_path . 'index.php' , 'auto', 0777);
		        }        
			}
		}       
    }
}


/* End of file lib_ftp.php */
/* Location: ./application/libraries/cdn/libs/lib_ftp.php */
