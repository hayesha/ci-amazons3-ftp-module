<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * Cdn Wrapper class
 *
 *
 * @package        Cdn Utility
 * @subpackage     Cdn
 * @category       Libraries 
 * Location        application/libraries/cdn
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
 
class Cdn {
    
	 /**
	 * CodeIgniter global  
	 *
	 * @var string 
	 */
	 protected $ci;

    private $config=array();  
    
    private $cdn = NULL;
    
    function __construct() 
    {
        
        $this->ci = & get_instance();        

        
        $conf = $this->ci->config->item('ftp'); 
        $cdn_lib = 'lib_'.$conf['cdn_type'];

        $this->ci->load->library('cdn/libs/'.$cdn_lib);
       
        $this->cdn = clone($this->ci->$cdn_lib);
        
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
        return $this->cdn->file_move($local,$server,$file_remove,$server_dir_path);        
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
        
        return $this->cdn->file_move_custom_name($local,$server,$name,$file_remove);
        
    }
    
    /**
     * This is to duplicate a folder on server
     *
     * @access	public
     * @param	string   $source
     * @param	string   $destination
     */
    public function mirror_dir($source = NULL, $destination = NULL)
    {
        $this->cdn->mirror_dir($source,$destination);   
    }
    
    /**
     * This is to copy file from one server to another
     * @author gayan
     * @access	public
     * @param	string
     */
    public function file_tmp_copy($filename=NULL,$server=NULL)
    {
        return $this->cdn->file_tmp_copy($filename, $server);
    	
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
        return $this->cdn->file_remove($path, $file);   
    }
    
    public function fileWrite($myFile,$stringData)
    {
        $this->cdn->fileWrite($myFile,$stringData); 
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
        $this->cdn->local_folder_clear($path); 
    }
    
    private function deleteAll($directory, $empty = false) 
    {
        return $this->cdn->deleteAll($directory, $empty);
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
        return $this->cdn->file_move_CDN($local, $server, $file_remove, $server_dir_path, $file_name);
        
        
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
        $this->cdn->file_download($url,$local_name);
    }



    
    public function file_download_from_server($target,$destination)
    {
        return $this->cdn->file_download_from_server($target,$destination);	
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
    	return $this->cdn->file_move_server($source,$destination,$file_remove,$server_dir_path);	
    }
    
    /**
     * This is to remove file by name and group
     *
     * @access	public
     * @param	string
     * @return	void
     */
    function file_remove_by_group_and_name($group,$file)
    {    
    	
        return $this->cdn->file_remove_by_group_and_name($group,$file);	

    }
    
    /**
     * This is to create folder structure
     *
     * @access	public
     * @param	string
     */
    public function create_dir($path)
    {        
        return $this->cdn->create_dir($path);
    }
    
    /**
     * This is to deploy s3 structure
     *
     * @access	public
     * @param	string
     */
    public function deploy_s3($media_root,$resource_array)
    {        
        return $this->cdn->deploy_s3($media_root,$resource_array);
    }
}


/* End of file Cdn.php */
/* Location: ./application/libraries/cdn/Cdn.php */
