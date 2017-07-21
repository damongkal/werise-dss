<?php
class werise_oryza_terms
{
    public static function getVarietyLabel($variety)
    {
        $tmp = explode('.',$variety);
        return strtoupper($tmp[0]);
    }
}