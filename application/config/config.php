<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
| If this is not set then CodeIgniter will guess the protocol, domain and
| path to your installation.
|
*/


$config['base_url']	= 'http://www.example.com/';
$config['server_root']   = $_SERVER['DOCUMENT_ROOT']."/";


/*
| -------------------------------------------------------------------
| Amazon S3 Configuration
| -------------------------------------------------------------------
*/
$config['amazons3_bucket'] = 'cdn.example.com';  // Name of the bucket on the Amazon S3, which the name should be a unique one
$config['amazons3_media_root'] = 'site_media/'; // optional parameter where images, files, etc.. are required to 
																// stored inside a separate folder, then provide it here (site_media)
$config['amazons3_media_web_path'] = 'http://s3-ap-southeast-1.amazonaws.com/'.$config['amazons3_bucket'].'/'.$config['amazons3_media_root'];	// In this example the URL of the Amazon S3 service available on the Singapore region (southeast-1)

/*
  |--------------------------------------------------------------------------
  | Remote media server - FTP server settings
  |--------------------------------------------------------------------------
  | Load media server configeration
  |
 */
$config['media_web_path'] = "http://ftp.example.com/site_media/";
$config['cdn_url'] = $config['base_url'];
$config['media_root'] = "/home/<user>/site_media/";
$config['ftp']['media_file_path'] = "ftp.example.com";
$config['ftp']['media_username'] = 'ftp_username';
$config['ftp']['media_password'] = 'ftp_password';
$config['ftp']['media_port'] = 21;
$config['ftp']['media_passive'] = FALSE;
$config['ftp']['media_debug'] = TRUE;

$config['ftp']['cdn_type'] = 'ftp'; // ftp, amazons3 - key used to switch the among FTP and Amazon S3 services

/*
  |--------------------------------------------------------------------------
  | TEMPORARY IMAGE UPLOAD PATH
  |--------------------------------------------------------------------------
  |
  | The temporary file path used to upload files (images, CSV, etc... )
  | before moving them to CDN.
  |
 */

$config['tmp_upload_path'] = APPPATH.'../temp/';


/* End of file config.php */
/* Location: ./application/config/config.php */
