<?php


/**
 * MyRouter
 * Routes URLs as class/method/param1/param2/param3/param4 ...
 * @package PHP-MyRouter
 * @author Mohammed Al-Ashaal
 * @copyright 2013
 */
class MyRouter{
    
    // Configuration Vars
    private     $MR;        //  Htaccess Mod Rewrite
    private     $CD;        //  Controllers Directory
    private     $MAIN;      //  The Main Controller
    private     $UP;        //  The Url Parts
    private     $EXT;       //  Controllers Extension
    
    /**
     * MyRouter::__construct()
     * 
     * @param Directory $controllersDir
     * @param string $main
     * @param string $extension
     * @return
     */
    function __construct($controllersDir,$mainController = 'home',$extension = 'php')
    {
        // Set Vars.
        $this->CD = rtrim($controllersDir, '/') . '/';
        $this->MAIN = $mainController;
        $this->EXT = $extension;
        
        // Call Functions
        $this->_modRewrite();
        $this->_prepare();
        $this->_route();
        
    }
    
    /**
     * MyRouter::_modRewrite()
     * 
     * @return
     */
    private function _modRewrite()
    {
        (isset($_SERVER['HTACCESS_MOD_REWRITE']))
        ?   $this->MR = true
        :   $this->MR = false;
    }
    
    /**
     * MyRouter::_prepare()
     * 
     * @return
     */
    private function _prepare()
    {
        // if using .htaccess mod_rewrite & no path_info
        ($this->MR && !isset($_SERVER['PATH_INFO']))
        ? $_SERVER['PATH_INFO'] = ''
        : '';
        
        // if not using .htaccess mo_rewrite & no path_info
        (!$this->MR && !isset($_SERVER['PATH_INFO']))
        ? $this->_redirect($_SERVER['SCRIPT_NAME'] . '/')
        : '';
        
        // Prepare The Url Parts
        $x = explode( '/', trim( $_SERVER['PATH_INFO'] ) );
        array_shift($x);
        
        // Set The Url Parts
        $this->UP = $x;
        
    }
    
    /**
     * MyRouter::_route()
     * 
     * @return
     */
    private function _route()
    {
        ( strlen( $this->UP[0] ) < 1 )
        ? $this->_check( (array) $this->MAIN )
        : $this->_check( $this->UP );
    }
    
    /**
     * MyRouter::_check()
     * 
     * @param mixed $up
     * @return
     */
    private function _check( array $up )
    {
        // Only There is '/class' or '/'
        if( isset($up[0]) ) {
            
            // If The Controller File Exists
            if(file_exists($this->CD . $up[0] . '.' . $this->EXT)) {
                // Load The Controller
                include_once $this->CD . $up[0] . '.' . $this->EXT;
                // if Controller Class exists
                if(class_exists($up[0])) {
                    $Obj = new $up[0];
                    // If There is '/class/method'
                    if(isset($up[1]) && strlen($up[1]) > 0) {
                        // if the method exists
                        if(method_exists($Obj,$up[1])) {
                            // check if the method not private
                            if( is_callable(array($Obj,$up[1])) ) {
                                // if there is not '/class/method/param ...'
                                if(!isset($up[2])) {
                                    $Obj->{$up[1]}();
                                } else { // There is parama ...
                                    // Get The Parameters Array
                                    $params = (array) $this->_params($up);
                                    call_user_func_array(array($Obj,$up[1]),$params);
                                }
                            } else { // The method is private (Forbidden)
                                die( $this->_403() );
                            }
                        } else { // The Method Not Exists
                            die( $this->_404() );
                        }
                    }
                } else { // The Controller Class Not Exists
                    die( $this->_404() );
                }
            } else { // The Controller Not Exists
                die( $this->_404() );
            }
        }
    }
    
    /**
     * MyRouter::_params()
     * 
     * @param mixed $up
     * @return
     */
    private function _params(array $up)
    {
        array_shift($up);
        array_shift($up);
        
        return $up;
    }
    
    /**
     * MyRouter::_redirect()
     * 
     * @param mixed $url
     * @param integer $type
     * @return
     */
    private function _redirect( $url, $type = 301 )
    {
        ($type == '301')
        ? $code = 'HTTP/1.1 301 Moved Permanently'
        : $code = 'Temporarily Moved';
        
        header( 'HTTP /1.1 ' . $code );
        header( 'Location: ' . $url, true, $type );
        exit;
    }
    
    /**
     * MyRouter::_error()
     * 
     * @param mixed $title
     * @param mixed $body
     * @return
     */
    private function _error( $title, $body )
    {
        $boxCss = 'margin:21% auto 21% auto;border:1px solid #ccc;padding:15px;color:#555;border-radius:10px;width:350px;box-shadow:0 0 10px #ddd;text-shadow:0 0 1px #fff';
        $titCss = 'border-bottom: 1px solid #ddd;padding:5px;font-size:20px;color:maroon';
        $bodCss = 'margin-top:5px;word-wrap:break-word;';
        
        $e  = '<!DOCTYPE html><html><head><title>' . $title . '</title></head><body>';
        $e .= '<div style=\'' . $boxCss . '\'>';
            $e .= '<div style=\'' . $titCss . '\'><b>'.$title.'</b></div>';
            $e .= '<div style=\'' . $bodCss . '\'><b>'.$body.'</b></div>';
        $e .= '</div></body></html>';
        
        return $e;
    }
    
    /**
     * MyRouter::_404()
     * 
     * @return
     */
    public function _404()
    {
        header( 'HTTP/1.1 404 Not Found', true ,404 );
        return $this->_error( '404 Page Not Found','Sorry , But The Page You Requested Not Found ' );
        exit;
    }
    
    /**
     * MyRouter::_403()
     * 
     * @return
     */
    public function _403()
    {
        header('HTTP/1.1 403 Forbidden',true,403);
        return $this->_error('403 Forbidden','Sorry , But The Page You Requested is Forbidden');
        exit;
    }
    
}
