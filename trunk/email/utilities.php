<?php

class Utilities
{

    static function listFiles($folder, $recursive = true)
    {
        $list = array();
        $dirs = array();

        $d    = dir($folder);

        while (false !== ($entry = $d->read())) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            $filename = $folder . $entry;
            if (is_dir($filename)) {
                if ($recursive) {
                    $dirs[] = $filename . '/';
                }
                continue;
            }
            $list[] = $filename;
        }

        $d->close();

        foreach ($dirs as $dir) {
            $list = array_merge($list, self::listFiles($dir, $recursive));
        }

        return $list;
    }
    //--------------------------------------------------------------------------
}