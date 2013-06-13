<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Amazon S3 Upload Wrapper class
 *
 *
 * @package        Cdn Utility
 * @subpackage     Amazon S3
 * @category       Libraries 
 * Location        application/libraries/cdn/libs
 *
 * @author         Hayesha Somarathne - <hayeshais@gmail.com>, http://thoughtsandideas.wordpress.com
 *
 * Created on      20-04-2012, 3:30PM by Hayesha Somarathne - <hayeshais@gmail.com>
 * Updated on      23-04-2012, 8:55AM by <>
 *
 * License: GNU LESSER GENERAL PUBLIC LICENSE, Version 3
 *
 * */
 
class lib_amazons3 {
    

	 /**
	 * CodeIgniter global  
	 *
	 * @var string 
	 */
	 protected $CI; 
    
    private $config=array();

    function __construct() 
    {
        $this->CI = & get_instance();        
        $this->CI->load->library('cdn/libs/lib_amazons3_wrapper');
        
        $config_arr = $this->CI->config->item('ftp');   
        $this->CI->config->load('amazons3', TRUE);
        $s3_config = $this->CI->config->item('amazons3');
        $this->bucket = $s3_config['bucket'];
        
        
    }
    
    /**
     * This is to move file from one server to another
     *
     * @access	public
     * @param	string
     * @return	string
     */
    function file_move($local=NULL,$server=NULL,$file_remove=0,$server_dir_path=NULL)
    {   

        $local_dir = $this->CI->config->item('server_root');
        $local_file = $local_dir . $local;
        $cdn_dir = $this->CI->config->item('amazons3_media_root') . $server;
        $server_dir_path_new=$this->CI->config->item('media_root') .$server_dir_path;
        $ext = end(explode('.', $local));

    	$cash_name = time() . md5(basename($local_file)) . mt_rand(1, 1000) . '.' . $ext;
    	
        $cdn_file = $cdn_dir . $cash_name;
        
        
        
        $input['file'] = $local_file;
        $this->CI->lib_amazons3_wrapper->add_object($input, $cdn_file, 'public-read');
        
        if (is_file($local_file)&&$file_remove==0)
        {
            unlink($local_file);           
        }       
        
        return $cash_name;
    

    }
    
    /**
     * This is to move file from one server to another with custom name
     *
     * @access	public
     * @param	string
     * @return	string
     */
    function file_move_custom_name($local=NULL,$server=NULL,$name=NULL,$file_remove=0)
    {   

        $local_dir = $this->CI->config->item('server_root');
        $local_file = $local_dir . $local;
        $cdn_dir = $this->CI->config->item('amazons3_media_root') . $server;
        $cdn_file = $cdn_dir.$name;        
        
        $input['file'] = $local_file;
        $this->CI->lib_amazons3_wrapper->add_object($input, $cdn_file, 'public-read');
        
        if (is_file($local_file)&&$file_remove==0)
        {
            unlink($local_file);
        }       
        
        return $name;    

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
    	$file_list = $this->CI->lib_amazons3_wrapper->list_files($this->CI->config->item('amazons3_media_root').$source);
    	
    	foreach($file_list as $file_val)
    	{
    		$pos = strpos($file_val, ".");
    		if ($pos !== false) 
    		{
    			$this->CI->lib_amazons3_wrapper->copy_object($this->bucket, $this->CI->config->item('amazons3_media_root').$source.$file_val, $this->bucket, $this->CI->config->item('amazons3_media_root').$destination.$file_val, 'public-read');
    		}
    	}        
    }
    
    /**
     * This is to copy file from one server to another
     * @author gayan
     * @access	public
     * @param	string
     */
    function file_tmp_copy($filename=NULL,$server=NULL)
    {

    	$local_dir = $this->CI->config->item('server_root') . 'files/tmp/';
    	$cdn_dir = $this->CI->config->item('amazons3_media_root') . $server;
    	$local_file = $local_dir  . $filename;
    	$input['file'] = $local_file;
        	
        $this->CI->lib_amazons3_wrapper->add_object($input, $cdn_dir.$filename, 'public-read');  
        
        return $local_file;        	
    }
    
    /**
     * This is to remove file by path
     *
     * @access	public
     * @param	string
     * @return	void
     */
    function file_remove($path=NULL,$file=NULL)
    {   
        
        if (isset($file) && isset($path))
        {
            $file_data = $this->CI->cdn_model->get_file_by_path($path,$file);
        }
        
        $cdn_path = $this->CI->config->item('amazons3_media_root') . $path;
        
        if (isset($file_data->FileServerName))
        {
            $cdn_file = $cdn_path . $file_data->FileServerName;
            
            $update_data['IsDelete'] = '1';
            $file_name = $file_data->FileServerName;
            
            $this->CI->cdn_model->set_file_id($file_data->FileId)->set_data_array($update_data)->edit_file_detail();
        }
        else 
        {
            $cdn_file = $cdn_path . $file;
            $file_name = $file;
        }

        $file_list = $this->CI->lib_amazons3_wrapper->list_files($cdn_path);
        if (in_array($file_name, $file_list)) {
            $this->CI->lib_amazons3_wrapper->delete_object($cdn_file);
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
    function local_folder_clear($path)
    {
        $folder_path = $this->CI->config->item('server_root') . $path;
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
     * This is to move file from one server to another with local image name
     *
     * @access	public
     * @param	string
     * @return	string
     */
    function file_move_CDN($local=NULL,$server=NULL,$file_remove=0,$server_dir_path=NULL,$file_name=null)
    {   

        $local_dir = $this->CI->config->item('server_root');
        $local_file = $local_dir . $local;
        $cdn_dir = $this->CI->config->item('amazons3_media_root') . $server;
        $ext = end(explode('.', $local));
        $cash_name = time() . md5(basename($local_file)) . mt_rand(1, 1000) . '.' . $ext;
        $cdn_file = $cdn_dir . $cash_name;
        
        	
        $file_list = $this->CI->lib_amazons3_wrapper->list_files($cdn_dir);
        if (count($file_list)==0) 
        {
             $this->CI->lib_amazons3_wrapper->add_object('index.php', $local_dir .'files/tmp/', 'public-read');
        }
        $this->CI->lib_amazons3_wrapper->add_object($local_file, $cdn_file, 'public-read');
        
        if (is_file($local_file)&&$file_remove==0)
        {
            unlink($local_file);
        }       
        
        
        return $cash_name;
    }
    
    /**
	 * tis is to run curl and execute http post
	 *
	 * @param string,array
	 * @access public
	 * @return string
	 * @author Sugunan - sugunan.kumaraguru@cyberlmj.com
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
    function file_download($url,$local_name)
    {
        $local_dir = $this->CI->config->item('server_root');
		$file = $local_dir . $local_name;
		file_put_contents($file, file_get_contents($url));
    }
    
    function file_download_from_server($target,$destination){
        //$this->CI->ftp->connect($this->config);
        //$down = $this->CI->ftp->download($target,$destination);
        return $down;
    }
    
    /**
     * This is to move file within the server
     *
     * @access	public
     * @param	string
     * @return	string
     */
    function file_move_server($source=NULL,$destination=NULL)
    {   
    	$ext = end(explode('.', $source));
    	$cash_name = time() . md5(basename($source)) . mt_rand(1, 1000) . '.' . $ext;
    	
    	$this->CI->lib_amazons3_wrapper->copy_object($this->bucket, $this->CI->config->item('amazons3_media_root').$source, $this->bucket, $this->CI->config->item('amazons3_media_root').$destination.$cash_name, 'public-read');
    	$this->CI->lib_amazons3_wrapper->delete_object($this->CI->config->item('amazons3_media_root').$source);
    	return $cash_name;
    }
    
    /**
     * This is to deploy s3 structure
     *
     * @access	public
     * @param	string
     */
    public function deploy_s3($media_root,$resource_array)
    {        
    	foreach($resource_array as $resource_val)
    	{
        	$this->CI->lib_amazons3_wrapper->add_object($media_root.$resource_val, $resource_val, 'public-read');
    	}
    }
}


/* End of file lib_amazons3.php */
/* Location: ./application/libraries/cdn/libs/lib_amazons3.php */
