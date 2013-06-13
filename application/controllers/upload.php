<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * The controller that manages the resource Uploading to the selected CDN
 *
 *
 * @package        Cdn Utility
 * @subpackage     Cdn
 * @category       Controllers 
 * Location        application/controllers
 *
 * @author         Hayesha Somarathne - <hayeshais@gmail.com>, http://thoughtsandideas.wordpress.com
 *
 * Created on      20-04-2012, 3:30PM by Hayesha Somarathne - <hayeshais@gmail.com>
 * Updated on      23-04-2012, 8:55AM by <>
 *
 * License: GNU LESSER GENERAL PUBLIC LICENSE, Version 3
 *
 * */

class upload extends CI_Controller {

    function __construct() {
        parent::__construct();	
	$this->load->helper(array('form', 'url')); 
    }


	function index()
	{
		$this->load->view('upload_form', array('error' => ' ' ));
	}

	function do_upload()
	{
		$config['upload_path'] = './files/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());

			$this->load->view('upload_form', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());

			$cdn_image_path = 'profile/albums/'.$data['file_name'];
			$input = $data['file_path']."".$data['file_name'];
			$this->load->library('util/Cdn');
    			$this->cdn->file_move_custom_name($input,$image_path);

			$this->load->view('upload_success', $data);
		}
	}


