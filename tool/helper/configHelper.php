<?php

use Symfony\Component\Yaml\Yaml;

class ConfigHelper {
    public static function getSiteInformations() {
        return self::filterFullConfigByParamType('siteInformations');
    }

    public static function getDbConfig() {
        return self::filterFullConfigByParamType('database');
    }

    public static function getOpenWheaterMapConfig() {
        return self::filterFullConfigByParamType('OpenWeatherMap');
    }

    public static function getYoutubeConfig() {
        return self::filterFullConfigByParamType('Youtube');
    }
    
    protected static function filterFullConfigByParamType($type) {
        return Yaml::parse(file_get_contents('config/config.yml'))[$type];
    }
}