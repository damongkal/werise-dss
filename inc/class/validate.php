<?php
class validate
{
    
    public static function getPostValue($id)
    {
        if (isset($_POST[$id]))
        {
            return $_POST[$id]; 
        }        
        return '';
    }
    
    public static function validateItem($items)
    {
        foreach ($items as $item)
        {
            $value = $item[0];
            $rules = $item[1];
            $len = strlen($value);
            if (isset($rules['required']) && $value==='')
            {
                return false;
            }        
            if (isset($rules['min']) && $len<$rules['min'])
            {
                return false;
            }                
            if (isset($rules['max']) && $len>$rules['max'])
            {
                return false;
            }                        
        }
        return true;
    }  
}