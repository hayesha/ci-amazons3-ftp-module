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

class lib_amazons3_wrapper 
{


        /**
        * CodeIgniter global  
        *
        * @var string 
        */
        protected $ci;   


        /**
        * Amazon S3 bucket name  
        *
        * @var string 
        */
        private $bucket;   



        public function __construct() 
        {
                $this->ci = & get_instance();
				
				$this->ci->config->load('amazons3', TRUE);
                $s3_config = $this->ci->config->item('amazons3');
                $this->ci->load->library('3rd/amazonaws/s3');
                $this->bucket = $s3_config['bucket'];
        }



        /**
        * Set the bucket name
        *
        * @access	public
        * @param	string $bucket_name Bucket name
        * @return	none
        */ 
        public function set_bucket_name($bucket_name = NULL)
        { 
                $this->bucket = $bucket_name;    

        }



        /**
        * Get the bucket name
        *
        * @access	public
        * @param	none
        * @return	string $bucket_name Bucket name
        */ 
        public function get_bucket_name()
        { 
                return $this->bucket;    

        }



        /**
        * Create a new bucket in S3
        *
        * @access	public
        * @param	string $bucket_name Bucket name
        * @param   constant $acl private, public-read, public-read-write and authenticated-read
        * @return	array
        */ 
        public function create_bucket($bucket_name = NULL, $acl = 'public-read')
        {    

                switch(strtolower($acl))
                {
                        case 'private':
                            return $this->ci->s3->putBucket($bucket_name, S3::ACL_PRIVATE);
                          break;
                        case 'public-read':
                            return $this->ci->s3->putBucket($bucket_name, S3::ACL_PUBLIC_READ);
                          break;
                        case 'public-read-write':
                            return $this->ci->s3->putBucket($bucket_name, S3::ACL_PUBLIC_READ_WRITE);
                          break;
                        case 'authenticated-read':
                            return $this->ci->s3->putBucket($bucket_name, S3:: ACL_AUTHENTICATED_READ);
                          break;
                        default:
                        return $this->ci->s3->putBucket($bucket_name, S3::ACL_PUBLIC_READ);
                        
                }    
        }    


        /**
        * Get the details of the bucket in S3
        *
        * @access	public
        * @param	string $bucket_name
        * @return	array
        */
        public function get_bucket($bucket_name = NULL)
        {
                $content = array();
                $content =$this->ci->s3->getBucket($bucket_name);

                if(count($content) > 0) {
                        return $content;

                } else {
                        return $content;
                }
        } 


        /**
        * Check whether the bucket exists in S3
        *
        * @access	public
        * @param	string $bucket_name Bucket name
        * @return	boolean
        */
        public function is_bucket_exist($bucket_name = NULL)
        {
                $content = array();
                $content =$this->get_bucket($bucket_name);

                if(is_array($content) && count($content) > 0) {
                        return TRUE;

                } else {
                        return FALSE;
                }
        }


        /**
        * Delete an empty bucket
        *
        * @access	public     
        * @param   string $bucket_name Bucket name
        * @return  boolean
        */
        public function delete_bucket($bucket_name = NULL)
        {

                if($this->ci->s3->deleteBucket($bucket_name)) {
                        return TRUE;

                } else {
                        return FALSE;
                }
        }


        /**
        * List all existing buckets in S3
        *
        * @access	public
        * @param	none
        * @return	array
        */
        public function list_buckets()
        {
                $content = array();
                $content =$this->ci->s3->listBuckets();

                if(count($content) > 0) {
                        return $content;

                } else {
                        return $content;
                }
        }


        /**
        * List all existing buckets in S3 with full details
        *
        * @access	public
        * @param	none
        * @return	array
        */
        public function list_buckets_details()
        {
                $content = array();
                $content =$this->ci->s3->listBuckets(TRUE);

                if(count($content) > 0) {
                        return $content;

                } else {
                        return $content;
                }
        }   


        /**
        * List bucketâ€™s files
        * @param $prefix directory name with forward slash in the end
        * @return array files array
        */

        public function list_files($prefix = null) {
                $rv = array();
                $ls = $this->ci->s3->getBucket($this->bucket, $prefix);
                if(!empty($ls))  {
                        foreach($ls as $l) {
                                $fname = str_replace($prefix,"",$l['name']);
                                if(!empty($fname)) { 
                                        $rv[] = $fname; 
                                }
                        }
                }
                if(!empty($rv)) {
                
                        return $rv; 
                }
        }
    

	/**
	* Put an object to a selected folder in a S3 bucket
	*
	* @param mixed $input Input data
	* @param string $uri Object URI
	* @param constant $acl private, public-read, public-read-write and authenticated-read
	* @return boolean
	*/
	public function add_object($input, $uri, $acl = 'public-read') 
	{
                switch(strtolower($acl))
                {
                        case 'private':
                            return $this->ci->s3->putObject($input, $this->bucket, $uri, S3::ACL_PRIVATE, $metaHeaders = array(), $requestHeaders = array(), $storageClass = S3::STORAGE_CLASS_STANDARD);
                          break;
                        case 'public-read':
                            return $this->ci->s3->putObject($input, $this->bucket, $uri, S3::ACL_PUBLIC_READ, $metaHeaders = array(), $requestHeaders = array(), $storageClass = S3::STORAGE_CLASS_STANDARD);
                          break;
                        case 'public-read-write':
                            return $this->ci->s3->putObject($input, $this->bucket, $uri, S3::ACL_PUBLIC_READ_WRITE, $metaHeaders = array(), $requestHeaders = array(), $storageClass = S3::STORAGE_CLASS_STANDARD);
                          break;
                        case 'authenticated-read':
                            return $this->ci->s3->putObject($input, $this->bucket, $uri, S3:: ACL_AUTHENTICATED_READ, $metaHeaders = array(), $requestHeaders = array(), $storageClass = S3::STORAGE_CLASS_STANDARD);
                          break;
                        default:
                        return $this->ci->s3->putObject($input, $this->bucket, $uri, S3::ACL_PUBLIC_READ, $metaHeaders = array(), $requestHeaders = array(), $storageClass = S3::STORAGE_CLASS_STANDARD);
                }
        }



	/**
	* Get an object
	*
	* @param string $uri Object URI
	* @param mixed $saveTo Filename or resource to write to
	* @return mixed
	*/
	public function get_object($uri, $saveTo = false)
	{
	        return $this->ci->s3->getObject($this->bucket, $uri, $saveTo);
	}



	/**
	* Get object information
	*
	* @param string $uri Object URI
	* @param boolean $returnInfo Return response information
	* @return mixed | false
	*/
	public function get_object_info($uri, $return_info = true)
	{
		return $this->ci->s3->getObjectInfo($this->bucket, $uri, $return_info);
	}



	/**
	* Check whether an object exists
	*
	* @param string $uri Object URI
	* @param mixed $saveTo Filename or resource to write to
	* @return mixed
	*/
	public function is_object_exists($uri = NULL)
	{
	        $s3_obj = NULL;
	        $s3_obj = $this->get_object_info($this->bucket, $uri, true);
	        if($s3_obj!=NULL) 
	        {
	             return TRUE;
	        } 
	        else
	        {
	             return FALSE;
	        }
	}



	/**
	* Delete an object
	*
	* @param string $uri Object URI
	* @return boolean
	*/
	public function delete_object($uri = NULL)
	{
		if ($this->ci->s3->deleteObject($this->bucket, $uri))
		{
			return TRUE;
		}
		else
		{
		        return FALSE;		
		}
	}


	/**
	* Put an object from a file (legacy function)
	*
	* @param string $file Input file path
	* @param string $uri Object URI
	* @param string $acl 
	* @return boolean
	*/
	public function put_object_file($file, $uri, $acl = 'public-read')
	{
                switch(strtolower($acl))
                {
                        case 'private':
                            return $this->ci->s3->putObjectFile($file, $this->bucket, $uri, S3::ACL_PRIVATE);
                          break;
                        case 'public-read':
                            return $this->ci->s3->putObjectFile($file, $this->bucket, $uri, S3::ACL_PUBLIC_READ);
                          break;
                        case 'public-read-write':
                            return $this->ci->s3->putObjectFile($file, $this->bucket, $uri, S3::ACL_PUBLIC_READ_WRITE);
                          break;
                        case 'authenticated-read':
                            return $this->ci->s3->putObjectFile($file, $this->bucket, $uri, S3:: ACL_AUTHENTICATED_READ);
                          break;
                        default:
                        return $this->ci->s3->putObjectFile($file, $this->bucket, $uri, S3::ACL_PUBLIC_READ);
                }
	}



	/**
	* Copy an object
	*
	* @param string $srcBucket Source bucket name
	* @param string $srcUri Source object URI
	* @param string $destBucket Destination bucket name
	* @param string $destUri Destination object URI
	* @param constant $acl ACL constant
	* @return mixed | false
	*/
	public function copy_object($srcBucket, $srcUri, $destBucket, $destUri, $acl = 'public-read')
	{
                switch(strtolower($acl))
                {
                        case 'private':
                            return $this->ci->s3->copyObject($srcBucket, $srcUri, $destBucket, $destUri, S3::ACL_PRIVATE);
                          break;
                        case 'public-read':
                            return $this->ci->s3->copyObject($srcBucket, $srcUri, $destBucket, $destUri, S3::ACL_PUBLIC_READ);
                          break;
                        case 'public-read-write':
                            return $this->ci->s3->copyObject($srcBucket, $srcUri, $destBucket, $destUri, S3::ACL_PUBLIC_READ_WRITE);
                          break;
                        case 'authenticated-read':
                            return $this->ci->s3->copyObject($srcBucket, $srcUri, $destBucket, $destUri, S3:: ACL_AUTHENTICATED_READ);
                          break;
                        default:
                        return $this->ci->s3->copyObject($srcBucket, $srcUri, $destBucket, $destUri, S3::ACL_PUBLIC_READ);
                }
		
	}


	/**
	* Get an authenticated URL for a resourse
	*
	* @param string file name $file
	* @param int $seconds
	* @return string URL
	*/
        function get_authenticated_url($file, $seconds)
        {
                return $this->ci->s3->getAuthenticatedURL($this->bucket, $file, $seconds, false, true);
        }
        
}


/* End of file lib_amazons3_wrapper.php */
/* Location: ./application/libraries/cdn/libs/lib_amazons3_wrapper.php */
