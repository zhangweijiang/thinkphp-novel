<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage __PUBLIC__/Pc/pluginsFilter
 */

/**
 * Smarty htmlspecialchars variablefilter plugin
 *
 * @param string                   $source input string
 * @param Smarty_Internal_Template $smarty Smarty object
 * @return string filtered output
 */
function smarty_variablefilter_htmlspecialchars($source, $smarty)
{
    return htmlspecialchars($source, ENT_QUOTES, SMARTY_RESOURCE_CHAR_SET);
}

?>