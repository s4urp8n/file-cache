<?php

namespace Zver
{
    
    /**
     * Caching system with grouping support using file system
     *
     * @package Zver
     */
    class FileCache
    {
        
        /**
         * Enabled or disabled flag
         *
         * @var bool
         */
        protected static $enabled = true;
        
        /**
         * Full path to directory to store cached data
         *
         * @var string
         */
        protected static $directory = null;
        
        /**
         * Default key group
         *
         * @var string
         */
        protected static $defaultGroup = 'default';
        
        /**
         * Extension of cached data file
         *
         * @var string
         */
        protected static $extension = 'tmp';
        
        /**
         * Disable caching, get() always return null
         */
        public static function disable()
        {
            static::$enabled = false;
        }
        
        /**
         * Enable caching
         */
        public static function enable()
        {
            static::$enabled = true;
        }
        
        /**
         * Add data to cache associated with group and key
         *
         * @param string $key
         * @param mixed  $value
         *
         * @return int
         */
        public static function set($key, $value)
        {
            if (!is_null($key))
            {
                static::setValue(null, $key, $value);
                
                return true;
            }
            throw new \InvalidArgumentException('Invalid arguments passed to set method');
        }
        
        /**
         * Add data to cache associated with group and key
         *
         * @param null   $group
         * @param string $key
         * @param mixed  $value
         *
         * @return int
         */
        protected static function setValue($group = null, $key, $value)
        {
            $serialized = serialize($value);
            $path = static::getCachePath($group, $key);
            static::checkPath($path);
            
            return file_put_contents($path, $serialized, LOCK_EX);
        }
        
        /**
         * Get hashed unique filesystem path according to group and key to store cached data
         *
         * @param null $group
         * @param null $key
         *
         * @return string
         * @throws \Exception
         */
        protected static function getCachePath($group = null, $key = null)
        {
            $name = '';
            if (is_null($group))
            {
                $group = static::$defaultGroup;
            }
            
            $name = static::getDirectory() . static::getHash($group) . DIRECTORY_SEPARATOR;
            
            if (!is_null($key))
            {
                $fileHash = static::getHash($key);
                $name .= static::splitToDirectories(mb_substr($fileHash, 0, 12, Encoding::get())) . mb_substr(
                        $fileHash, 12, null, Encoding::get()
                    ) . '.' . static::$extension;
            }
            
            return $name;
        }
        
        /**
         * Check path, create it if not exists
         *
         * @param $path
         */
        protected static function checkPath(&$path)
        {
            if (!file_exists($path))
            {
                $directory = dirname($path);
                if (!file_exists($directory))
                {
                    mkdir($directory, 0755, true);
                }
            }
        }
        
        /**
         * Get current cache directory
         *
         * @return string
         * @throws \Exception
         */
        public static function getDirectory()
        {
            if (is_null(static::$directory))
            {
                throw new \Exception('You must specify directory to store cache data.');
            }
            
            return rtrim(static::$directory, '\\/') . DIRECTORY_SEPARATOR;
        }
        
        /**
         * Set directory to store cache data
         *
         * @param $directory
         */
        public static function setDirectory($directory)
        {
            static::$directory = $directory;
        }
        
        /**
         * Main hash method to take unique file name
         *
         * @param $value
         *
         * @return string
         */
        protected static function getHash($value)
        {
            return md5($value . __DIR__ . __METHOD__ . __CLASS__);
        }
        
        /**
         * Split value to directories, to prevent filesystem overload
         *
         * @param $value
         *
         * @return string
         */
        protected static function splitToDirectories($value)
        {
            return chunk_split($value, 4, DIRECTORY_SEPARATOR);
        }
        
        /**
         * Add data to cache associated with group and key
         *
         * @param null   $group
         * @param string $key
         * @param mixed  $value
         *
         * @return int
         */
        public static function setGroup($group, $key, $value)
        {
            if (!is_null($key))
            {
                static::setValue($group, $key, $value);
                
                return true;
            }
            throw new \InvalidArgumentException('Invalid arguments passed to set method');
        }
        
        /**
         * Get value from cache if exists, null otherwise
         *
         * @return mixed|null
         */
        public static function get($key)
        {
            return static::getValue(null, $key);
        }
        
        /**
         * Get value from cache if exists, null otherwise
         *
         * @param null $group
         * @param      $key
         *
         * @return mixed|null
         */
        protected static function getValue($group = null, $key)
        {
            if (static::$enabled)
            {
                $cachePath = static::getCachePath($group, $key);
                if (file_exists($cachePath))
                {
                    return unserialize(file_get_contents($cachePath));
                }
            }
            
            return null;
        }
        
        /**
         * Get value from cache if exists, null otherwise
         *
         * @return mixed|null
         */
        public static function getGroup($group, $key)
        {
            return static::getValue($group, $key);
        }
        
        /**
         * Clear all cache data with cache directory
         *
         * @throws \Exception
         */
        public static function clearAll()
        {
            static::remove(static::getDirectory());
        }
        
        /**
         * Remove file or directory recursively
         *
         * @param $path
         */
        protected static function remove($path)
        {
            if (file_exists($path))
            {
                
                if (is_file($path))
                {
                    unlink($path);
                }
                else
                {
                    $contents = glob(rtrim($path, '\\/') . DIRECTORY_SEPARATOR . '*');
                    foreach ($contents as $content)
                    {
                        static::remove($content);
                    }
                    rmdir($path);
                }
            }
        }
        
        /**
         * Clear all group data. If no argument passed, default group data will cleared
         *
         * @param null $group Group name
         */
        public static function clearGroup($group = null)
        {
            static::remove(static::getCachePath($group));
        }
        
        /**
         * Clear data according to key
         *
         */
        public static function clearKey($key)
        {
            static::remove(static::getCachePath(null, $key));
        }
        
        /**
         * Clear data according to key in group
         */
        public static function clearGroupKey($group, $key)
        {
            static::remove(static::getCachePath($group, $key));
        }
        
        /**
         * Get value from cache if it exist, otherwise return callback result and set callback result to cache for
         * future usage. It's combination of get() and set()
         *
         * @param      $key
         * @param      $callback
         *
         * @return mixed|null
         */
        public static function retrieve($key, $callback)
        {
            if (!empty($key) && !empty($callback))
            {
                return static::retrieveValue(null, $key, $callback);
            }
            throw new \InvalidArgumentException();
        }
        
        /**
         * Get value from cache if it exist, otherwise return callback result and set callback result to cache for
         * future usage. It's combination of get() and set()
         *
         * @param null $group
         * @param      $key
         * @param      $callback
         *
         * @return mixed|null
         */
        protected static function retrieveValue($group = null, $key, $callback)
        {
            $value = static::getValue($group, $key);
            if (is_null($value))
            {
                $value = is_callable($callback)
                    ? call_user_func($callback, $group, $key)
                    : $callback;
                static::setValue($group, $key, $value);
            }
            
            return $value;
        }
        
        /**
         * Get value from cache if it exist, otherwise return callback result and set callback result to cache for
         * future usage. It's combination of get() and set()
         *
         * @param null $group
         * @param      $key
         * @param      $callback
         *
         * @return mixed|null
         */
        public static function retrieveGroup($group, $key, $callback)
        {
            if (!empty($key) && !empty($callback))
            {
                return static::retrieveValue($group, $key, $callback);
            }
            throw new \InvalidArgumentException();
        }
        
    }
}
