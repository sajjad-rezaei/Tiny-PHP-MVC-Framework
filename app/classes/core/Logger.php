<?php


class LoggerException extends Exception {}

/**
 *  logger
 * 
 * Log notices, warnings, errors or fatal errors into a log file.
 * 
 * @author gehaxelt
 */
class Logger {
    
    /**
     * Holds the file handle.
     * 
     * @var resource
     */
    protected $fileHandle = NULL;
    
    /**
     * The time format to show in the log.
     * 
     * @var string
     */
    protected $timeFormat = 'd.m.Y - H:i:s';
    
    /**
     * The message
     * 
     * @var string
     */
    private $message;

    /**
     * The file permissions.
     */
    const FILE_CHMOD = 756;
    
    const NOTICE = '[NOTICE]';
    const WARNING = '[WARNING]';
    const ERROR = '[ERROR]';
    const FATAL = '[FATAL]';
    private $omittedErrors = EXCLUDE_CATCH_ERRORS;
    
    /**
     * Opens the file handle.
     * 
     * @param string $logfile The path to the loggable file.
     */
    public function __construct($logfile) {
        if($this->fileHandle == NULL){
            $this->openLogFile($logfile);
        }
    }
    
    /**
     * Closes the file handle.
     */
    public function __destruct() {
        $this->closeLogFile();
    }
    
    /**
     * Logs the message into the log file.
     * 
     * @param  string $message     The log message.
     * @param  int    $messageType Optional: urgency of the message.
     */
    public function log($message, $messageType = Logger::WARNING) {

    
        //$errno, $errstr, $errfile, $errline
        if($this->fileHandle == NULL){
            throw new LoggerException('Logfile is not opened.');
        }
        
        if(!is_string($message)){
            throw new LoggerException('$message is not a string');
        }
        
        if($messageType != Logger::NOTICE &&
            $messageType != Logger::WARNING &&
            $messageType != Logger::ERROR &&
            $messageType != Logger::FATAL
        ){
            throw new LoggerException('Wrong $messagetype given.');
        }
        get_hook("logger_hook" , $message , $messageType);
        $this->message = "[".$this->getTime()."]".$messageType." - ".$message;

        if(in_array($messageType , $this->omittedErrors ))
            return;

        $this->writeToLogFile($this->getMessage());
        if(DEBUG_MODE)
            notFoundResponse($this->getMessage());
        else
            notFoundResponse(ERROR_DEFAULT_MESSAGE);
        
        if($messageType == Logger::FATAL)
            die;
    }
    /**
     * Writes content to out put
     * 
     * 
     */
    public function printMessage() {
        
        notFoundResponse($this->getMessage());

    }

    /**
     * Writes content to the log file.
     * 
     * @param string $message
     */
    private function writeToLogFile($message) {
        flock($this->fileHandle, LOCK_EX);
        fwrite($this->fileHandle, $message.PHP_EOL);
        flock($this->fileHandle, LOCK_UN);
    }
    
    /**
     * Returns the current timestamp.
     * 
     * @return string with the current date
     */
    private function getTime() {
        return date($this->timeFormat);
    }
    /**
     * Returns Message.
     * 
     * @return string with the current date
     */
    public function getMessage() {
        return ($this->message);
    }
    
    /**
     * Closes the current log file.
     */
    protected function closeLogFile() {
        if($this->fileHandle != NULL) {
            fclose($this->fileHandle);
            $this->fileHandle = NULL;
        }
    }
    
    /**
     * Opens a file handle.
     * 
     * @param string $logFile Path to log file.
     */
    public function openLogFile($logFile) {
        $this->closeLogFile();
        
        if(!is_dir(dirname($logFile))){
            if(!mkdir(dirname($logFile), Logger::FILE_CHMOD, true)){
                throw new LoggerException('Could not find or create directory for log file.');
            }
        }
        
        if(!$this->fileHandle = fopen($logFile, 'a+')){
            throw new LoggerException('Could not open file handle.');
        }
    }
    
}


