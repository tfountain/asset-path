<?php

namespace TfAssetPath\View\Helper;

use Zend\View\Helper\AbstractHelper;

class AssetPath extends AbstractHelper
{
    /**
     * Returns a versioned asset path
     *
     * @param  string           $path
     * @param  boolean|integer  $version
     * @return string
     */
    public function __invoke($path, $version = true)
    {
        $filePath = 'public/'.$path;

        if ($version) {
            if (is_int($version)) {
                // use the supplied timestamp
                $lastModified = $version;
            } else {
                // calculate it
                $lastModified = $this->getLastModified($filePath);
            }

            if ($lastModified) {
                $hash = $this->getVersioningHash($lastModified);
                if ($hash) {
                    $path = substr_replace($path, '.'.$hash.'.', strrpos($path, '.'), 1);
                }
            }
        }

        return $path;
    }

    /**
     * Returns the last modified time for the specified path
     *
     * This function exists to make it easier to extend this helper and
     * add caching for these timestamps to reduce file system calls
     *
     * @param  string $filePath
     * @return integer
     */
    public function getLastModified($filePath)
    {
        return filemtime($filePath);
    }

    /**
     * Returns the versioning hash for the given timestamp
     *
     * @param  integer $lastModified
     * @return string
     */
    public function getVersioningHash($lastModified)
    {
        return base_convert($lastModified, 10, 36);
    }
}
