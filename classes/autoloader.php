<?php
spl_autoload_register('ezdata_site_autoloader');

/**
 * Autoloader, include class file
 * @param $className
 */
function ezdata_site_autoloader($className) {
    if ( false === strpos( $className, 'EZDataSite' ) ) {
        return;
    }
    $namespace=str_replace("\\","/",__NAMESPACE__);
    $className = str_replace( 'EZDataSite', '', $className );
    $className=str_replace("\\","/",$className);
    $class=EZDATA_SITE_PLUGIN_DIR."classes".(empty($namespace)?"":$namespace."/")."{$className}.php";
    if ( file_exists( $class ) ) {
        include_once($class);
    }
};
