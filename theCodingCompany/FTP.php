<?php
/**
 * Intellectual Property of Svensk Coding Company AB - Sweden All rights reserved.
 * 
 * @copyright (c) 2016, Svensk Coding Company AB
 * @author V.A. (Victor) Angelier <victor@thecodingcompany.se>
 * @version 1.0
 * @license http://www.apache.org/licenses/GPL-compatibility.html GPL
 * 
 */

namespace TheCodingCompany;

class FTP
{
    /**
     * FTP object
     * @var type
     */
    protected $ftp = null;

    /**
     * Upload status
     * @var type
     */
    private $status = false;

    /**
     * Filelist
     * @var type
     */
    protected $filelist = [];

    /**
     * Construct new FTP class
     */
    public function __construct()
    {
    }

    /**
     * Log information to cache and syslog
     * @param string $log_info
     */
    private function setStatus($log_info = ""){
        echo "\r\n{$log_info}\r\n";
        syslog(LOG_INFO, $log_info);
    }

    /**
     * Connect to FTP server
     * @param string $host
     * @param int $port = 21
     */
    public function connect($host, $port = 21){
        $this->setStatus("Connecting to {$host} on port {$port}.");
        $this->ftp = ftp_connect($host, $port) or die("Can't connect to host.");
        return $this;
    }

    /**
     * Chdir into upload path
     * @param string $path
     */
    public function chdir($path = ""){
        if(!ftp_chdir($this->ftp, $path)){
            $this->setStatus("Can't chdir into {$path}");
            exit(0);
        }else{
            $this->setStatus("Chdir into {$path} success");
        }
        return $this;
    }

    /**
     * Login to the FTP server
     * @param string $username
     * @param string $password
     * @return \FTP
     */
    public function login($username = "", $password = ""){
        $this->setStatus("Authenticating with FTP server.");
        if(ftp_login($this->ftp, $username, $password)){
            $this->setStatus("Current directory: ".ftp_pwd($this->ftp));
        }else{
            $this->setStatus("Can't login to FTP server. Authentication failed.");
        }
        return $this;
    }

    /**
     * Enable Passiv mode
     * @return type
     */
    public function passv(){
        if(!ftp_pasv($this->ftp, TRUE)){
            $this->setStatus("Error while switching to passive mode.");
        }else{
            $this->setStatus("Passive mode enabled.");
        }
        return $this;
    }

    /**
     * List files in current directory
     */
    public function listFiles(){
        set_time_limit(1); //Minutes
        
        $this->setStatus("Listing files in " . ftp_pwd($this->ftp));

        $this->filelist = ftp_nlist($this->ftp, ftp_pwd($this->ftp));
        if(!$this->filelist){
            $this->setStatus("Error while getting directory listing.");
        }

        return $this;
    }

    /**
     * Store file
     * @param string $filename Full path to the file
     */
    public function upload($filename){
        set_time_limit(1); //Minutes
        
        if(!empty($filename)){

            $this->setStatus("Uploading {$filename} as ".basename($filename)." to client.");

            if(ftp_put($this->ftp, basename($filename), $filename, FTP_ASCII, 0)){
                $this->setStatus("Upload success.");
            }else{
                $this->setStatus("Upload failed.");
            }
            
            if($this->filelist && in_array("/".basename($filename), $this->filelist)){
                $this->setStatus("File found in filelist.");
            }

            $this->status = true;
        }
        return $this;
    }

    /**
     * Close the FTP connection
     */
    public function close(){
        if(!empty($this->ftp)){
            $this->setStatus("Closing connection.");
            ftp_close($this->ftp);
        }
    }
}