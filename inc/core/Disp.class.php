<?php

class Disp {

    public static $meta = array();
    public static $content = "";
    
    public static function addMeta($meta) {
        if (is_array($meta)) {
            self::$meta = array_merge(self::$meta, $meta);
        }
    }
    
    public static function render()
    {
        $disp = file_get_contents(Config::$template['index']);
        
        $init = show(self::loadingPanels($disp),array("content" => self::$content));        
        $init = show($init, convertMatchDyn(searchBetween("{dyn_", $init, "}")));
        $init = preg_replace("/\s+/", " ", $init);

        //unset($_SESSION['note']);
        self::$meta['copyright'] = Config::$settings->copyright;
        self::$meta['google_analytics'] = Config::$settings->google_analytics;
        self::$meta['domain'] = $_SERVER['HTTP_HOST'];
        self::$meta['title'] .= ' | '.Config::$settings->website_title;

        self::$meta = array_merge(Config::$path, self::$meta);

        if (!isset(self::$meta['google_plus'])) {
            self::$meta['google_plus'] = "";
        }

        $sn = !empty($_SESSION['simple_note']) ? $_SESSION['simple_note'] : '';
        $init = show($init, array('simple_note' => $sn));
        $_SESSION['simple_note'] = NULL;
        Debug::log('test123');
        $init = show($init,self::$meta);
        $init = show($init, convertMatch(searchBetween("{s_", $init, "}")));
        $init = preg_replace("/\{\w\}/", "", $init);
        self::display($init);
    }
    
    public static function replace_paths($init) {

        self::$meta = array_merge(Config::$path, self::$meta);

        $init = show($init, self::$meta);
        return $init;
    }

    public static function renderMin() {
        $init = show(self::$content, convertMatch(searchBetween("{s_", self::$content, "}")));
        self::display($init);
    }

    public static function renderMinStyle() {
        self::$meta = array_merge(Config::$path, self::$meta);

        self::$meta['conten'] = self::$content;
     // $init = show(self::$content, convertMatch(searchBetween("{s_", self::$content, "}")));
        $meta = array_merge(self::$meta, array('content' => self::$content));
        $init = show(file_get_contents('../templates/default/index.html'), $meta);
        $init = preg_replace("/\{(/w+)\}/", "", $init);
        self::display($init);
    }

    private function display($content) {
        echo $content;
    }
    function loadingPanels($disp)
    {
        global $path;
        $panels = opendir($path['panels']);
        
        while ($panel = readdir($panels))
        {
            if ($panel != ".." && $panel != "." && $panel != "disable")
            {
                include($path['panels'].$panel);
                $panel = substr($panel,0,-4);
                if (function_exists($panel)){
                    $disp = show($disp, array( $panel => $panel() ));
                }
            }
        }
        closedir($panels);

        return $disp;
    }
}
