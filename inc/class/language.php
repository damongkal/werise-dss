<?php

class language
{

    protected $lang_en = array();
    protected $lang_target = array();
    public $lang_js_en = array();
    public $lang_js_target = array();
    protected static $instance = null;

    protected function __construct()
    {
        //Thou shalt not construct that which is unconstructable!
    }

    protected function __clone()
    {
        //Me not like clones! Me smash clones!
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    public function init()
    {
        // set language by maxmind
        if (!isset($_SESSION['lang'])) {
            $geoip = new maxmind_geoip();
            $this->setLang($geoip->getLang());
        }
        // set language by URL parameter
        if (isset($_GET['lang'])) {
            $this->setLang($_GET['lang']);
        }
        $lang = $_SESSION['lang'];

        // open javascript language files
        $enjs = file_get_contents(_CLASS_DIR . 'lang-files/lang-js-en.txt');
        $tmp = explode('--lang--', $enjs);
        $this->lang_js_en = array();
        foreach ($tmp as $str) {
            $this->lang_js_en[] = trim($str);
        }
        $targetjs = file_get_contents(_CLASS_DIR . "lang-files/lang-js-{$lang}.txt");
        $tmp2 = explode('--lang--', $targetjs);
        $this->lang_js_target = array();
        foreach ($tmp2 as $str) {
            $this->lang_js_target[] = trim($str);
        }

        // open web language files
        $en = file_get_contents(_CLASS_DIR . 'lang-files/lang-en.txt');
        $tmp3 = explode('--lang--', $en);
        $this->lang_en = array_merge($tmp3, $this->lang_js_en);
        //echo '<pre>';print_r($this->lang_en);
        $target = file_get_contents(_CLASS_DIR . "lang-files/lang-{$lang}.txt");
        $tmp4 = explode('--lang--', $target);
        $this->lang_target = array_merge($tmp4, $this->lang_js_target);
        //print_r($this->lang_target);
    }

    /**
     * determine target language
     * @param type $lang
     */
    private function setLang($newlang = null)
    {
        $lang = strtolower($newlang);
        // get from method parameter
        if (!is_null($lang) && in_array($lang, array('id', 'la', 'th', 'en', 'ph'))) {
            $_SESSION['lang'] = $lang;
            return;
        }
        // default
        if (!isset($_SESSION['lang'])) {
            $_SESSION['lang'] = 'en';
        }
    }

    public function translate($str_orig, $debug = false)
    {
        $str = $str_orig;
        // detect langidx
        list($clean, $langidx) = $this->detectLangIndex($str_orig);
        if ($this->getLang() === 'en') {
            return $clean;
        } else {
            if ($langidx === '') {
                $str = $clean;
            } else {
                $str = $langidx;
            }
        }
        $idx_found = false;
        $str_l = strtolower($str);
        foreach ($this->lang_en as $idx => $str_en) {
            $str_en = strtolower(trim($str_en));
            if ($debug) {
                echo "{$str}*{$str_en}<hr/>";
            }
            if ($str_l == $str_en) {
                if ($debug) {
                    echo "found at {$idx} <hr />";
                }
                $idx_found = $idx;
                break;
            }
        }

        // get translated text
        if ($idx_found !== false && isset($this->lang_target[$idx_found])) {
            if ($debug) {
                echo "translate: " . trim($this->lang_target[$idx_found]) . '<hr/>';
            }
            $str_t = trim($this->lang_target[$idx_found]);
            if ($str_t === 'for translate') {
                return $clean;
            } else {
                return $str_t;
            }
        }

        // no translation
        if ($debug) {
            echo "no-translate";
        }
        return $clean;
    }

    public function jstranslate()
    {
        return array('en' => $this->lang_js_en, 'target' => $this->lang_js_target);
    }

    public static function getLang()
    {
        if (isset($_SESSION['lang'])) {
            return $_SESSION['lang'];
        }
        return 'en';
    }

    private function detectLangIndex($str)
    {
        $tmp = strpos($str, '</langidx>');
        if ($tmp !== false) {
            $clean = trim(substr($str, $tmp + 10));
            $tmp = substr($str, 0, $tmp + 10);
            $tmp2 = str_replace('<langidx>', '', $tmp);
            $tmp3 = str_replace('</langidx>', '', $tmp2);
            $langidx = trim($tmp3);
            return array($clean, $langidx);
        } else {
            return array($str, '');
        }
    }

    public static function getLangs($country_code = null)
    {
        $langs = array(
            'en' => 'English',
            'la' => 'ພາສາລາວ',
            'id' => 'Bahasa Indonesia',
            'th' => 'ไทย',
            'ph' => 'Filipino');
        if (is_null($country_code)) {
            return $langs;
        } else {
            $code = strtolower($country_code);
            return $langs[$code];
        }
    }

    public static function convertToCountry($lang)
    {
        if (strtolower($lang) === 'en') {
            return 'us';
        }
        return strtolower($lang);
    }
}

function _t($str, $debug = false)
{
    return language::getInstance()->translate(trim($str), $debug);
}

function __($str, $debug = false)
{
    echo _t($str, $debug);
}
