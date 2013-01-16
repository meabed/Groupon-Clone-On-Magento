<?php

class Webkul_Customerprofile_Model_Uploadzipsample {
    var $the_file;
	var $the_temp_file;
    var $upload_dir;
	var $replace;
	var $do_filename_check;
	var $max_length_filename = 50;
    var $extensions;
	var $ext_string;
	var $language;
	var $http_error;
	var $rename_file; // if this var is true the file copy get a new name
	var $file_copy; // the new name
	var $message = array();
	var $create_directory = true;
	var $filename;

	function file_upload() {
		$this->language = "en"; // choice of en, nl, es
		$this->rename_file = false;
		$this->ext_string = "";
	}
	function show_error_string() {
		$msg_string = "";
		foreach ($this->message as $value) {
			$msg_string .= $value."<br>\n";
		}
		return $msg_string;
	}
	function set_file_name($new_name = "") { // this "conversion" is used for unique/new filenames
		if ($this->rename_file) {
			#if ($this->the_file == "") return;
			#$name = ($new_name == "") ? strtotime("now") : $new_name;
			#$name = $this->get_filename($this->filename)."_".$this->RemoveExtension($this->the_file)."_".$name.$this->get_extension($this->the_file);
			$name = $this->get_filename($this->filename).$this->get_extension($this->the_file);
		} else {
			$name = $this->the_file;
		}
		return $name;
	}

	function RemoveExtension($strName) { // Get Filename remove extension.
    	$exte = strrchr($strName, '.');
	     if($exte !== false){
			$strName = substr($strName, 0, -strlen($exte));
	     }
     	return $strName;
	}

	function get_filename($fname = ""){ // Get Customer Name
		$fname = $this->filename = str_replace(' ', '', $fname);
		return $fname;
	}

	function upload($to_name = "") {
		$new_name = $this->set_file_name($to_name);
		if ($this->check_file_name($new_name)) {
			if ($this->validateExtension()) {
				if (is_uploaded_file($this->the_temp_file)) {
					$this->file_copy = $new_name;
					if ($this->move_upload($this->the_temp_file, $this->file_copy)) {
						$this->message[] = $this->error_text($this->http_error);
						//if ($this->rename_file) $this->message[] = $this->error_text(16);  // Delete this!
						return true;
					}
				} else {
					$this->message[] = $this->error_text($this->http_error);
					return false;
				}
			} else {
				$this->show_extensions();
				$this->message[] = $this->error_text(11);
				return false;
			}
		} else {
			return false;
		}
	}
	function check_file_name($the_name) {
		if ($the_name != "") {
			if (strlen($the_name) > $this->max_length_filename) {
				$this->message[] = $this->error_text(13);
				return false;
			} else {
				if ($this->do_filename_check == "y") {
					if (preg_match("/^[a-z0-9_  -]*\.(.){1,5}$/i", $the_name)) {
						return true;
					} else {
						$this->message[] = $this->error_text(12);
						return false;
					}
				} else {
					return true;
				}
			}
		} else {
			$this->message[] = $this->error_text(10);
			return false;
		}
	}
	function get_extension($from_file) {
		$ext = strtolower(strrchr($from_file,"."));
		return $ext;
	}
	function validateExtension() {
		$extension = $this->get_extension($this->the_file);
		$ext_array = $this->extensions;
		if (in_array($extension, $ext_array)) {
			// check mime type hier too against allowed/restricted mime types (boolean check mimetype)
			return true;
		} else {
			return false;
		}
	}
	// this method is only used for detailed error reporting
	function show_extensions() {
		$this->ext_string = implode(" ", $this->extensions);
	}
	function move_upload($tmp_file, $new_file) {
		umask(0);
		if ($this->existing_file($new_file)) {
			$fileext=explode(".",$new_file);
			$newfile = $this->upload_dir.$new_file."2@300@.".$fileext[1];
			$newfile=  str_replace(" ", "_",$newfile );
			$newfile=  str_replace("-", "_",$newfile );
			if ($this->check_dir($this->upload_dir)) {
				if (move_uploaded_file($tmp_file, $newfile)) {
					if ($this->replace == "y") {
						//system("chmod 0777 $newfile"); // maybe you need to use the system command in some cases...
						chmod($newfile , 0755);
					} else {
						// system("chmod 0755 $newfile");
						chmod($newfile , 0755);
					}
					return true;
				} else {
					return false;
				}
			} else {
				$this->message[] = $this->error_text(14);
				return false;
			}
		} else {
			$this->message[] = $this->error_text(15);
			return false;
		}
	}
	function check_dir($directory) {
		if (!is_dir($directory)) {
			if ($this->create_directory) {
				umask(0);
				mkdir($directory, 0755);
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	function existing_file($file_name) {
		if ($this->replace == "y") {
			return true;
		} else {
			if (file_exists($this->upload_dir.$file_name)) {
				return false;
			} else {
				return true;
			}
		}
	}
	function get_uploaded_file_info($name) {
		$str = "File name: ".basename($name)."\n";
		$str .= "File size: ".filesize($name)." bytes\n";
		if (function_exists("mime_content_type")) {
			$str .= "Mime type: ".mime_content_type($name)."\n";
		}
		return $str;
	}
	// this method was first located inside the foto_upload extension
	function del_temp_file($file) {
		$delete = @unlink($file);
		clearstatcache();
		if (@file_exists($file)) {
			$filesys = eregi_replace("/","\\",$file);
			$delete = @system("del $filesys");
			clearstatcache();
			if (@file_exists($file)) {
				$delete = @chmod ($file, 0775);
				$delete = @unlink($file);
				$delete = @system("del $filesys");
			}
		}
	}
	// some error (HTTP)reporting, change the messages or remove options if you like.
	function error_text($err_num) {
		switch ($this->language) {
			default:
			// start http errors
			$error[0] = "File: <b>".$this->the_file."</b> successfully uploaded!";
			$error[1] = "The uploaded file exceeds the max. upload filesize directive in the server configuration.";
			$error[2] = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form.";
			$error[3] = "The uploaded file was only partially uploaded";
			$error[4] = "No file was uploaded";
			// end  http errors
			$error[10] = "Please select a file for upload.";
			$error[11] = "Only files with the following extensions are allowed: <b>".$this->ext_string."</b>";
			$error[12] = "Filename contains invalid characters. Use only alphanumerical chars and remove spaces. <br>A valid filename ends with one dot followed by the extension.";
			$error[13] = "The filename exceeds the maximum length of ".$this->max_length_filename." characters.";
			$error[14] = "Sorry, the upload directory doesn't exist!";
			$error[15] = "Uploading <b>".$this->the_file."...Error!</b> Sorry, a file with this name already exitst.";
			$error[16] = "The uploaded file is renamed to <b>".$this->file_copy."</b>.";

		}
		return $error[$err_num];
	}
}
?>
