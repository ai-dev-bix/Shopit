<?php
/**
 * Database Class
 * Handles JSON file operations for the marketplace platform
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class Database {
    private $dataPath;
    private $cache = [];
    private $cacheTTL = [];
    private $defaultTTL = 300; // 5 minutes
    
    public function __construct($dataPath = null) {
        $this->dataPath = $dataPath ?: DATA_PATH;
        
        // Create data directory if it doesn't exist
        if (!is_dir($this->dataPath)) {
            mkdir($this->dataPath, 0755, true);
        }
    }
    
    /**
     * Read data from a JSON file
     * 
     * @param string $filePath Path to the JSON file
     * @param bool $useCache Whether to use caching
     * @return array|false Data from file or false on error
     */
    public function read($filePath, $useCache = true) {
        $fullPath = $this->getFullPath($filePath);
        
        // Check cache first
        if ($useCache && $this->isCached($fullPath)) {
            return $this->getFromCache($fullPath);
        }
        
        // Validate file path
        if (!$this->validateFilePath($fullPath)) {
            $this->logError("Invalid file path: $filePath");
            return false;
        }
        
        // Check if file exists
        if (!file_exists($fullPath)) {
            $this->logError("File not found: $fullPath");
            return false;
        }
        
        // Read file content
        $content = file_get_contents($fullPath);
        if ($content === false) {
            $this->logError("Failed to read file: $fullPath");
            return false;
        }
        
        // Decode JSON
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logError("JSON decode error in $fullPath: " . json_last_error_msg());
            return false;
        }
        
        // Cache the result
        if ($useCache) {
            $this->setCache($fullPath, $data);
        }
        
        return $data;
    }
    
    /**
     * Write data to a JSON file
     * 
     * @param string $filePath Path to the JSON file
     * @param array $data Data to write
     * @param bool $backup Whether to create a backup
     * @return bool Success status
     */
    public function write($filePath, $data, $backup = true) {
        $fullPath = $this->getFullPath($filePath);
        
        // Validate file path
        if (!$this->validateFilePath($fullPath)) {
            $this->logError("Invalid file path: $filePath");
            return false;
        }
        
        // Validate data
        if (!is_array($data)) {
            $this->logError("Data must be an array for file: $filePath");
            return false;
        }
        
        // Create backup if requested
        if ($backup && file_exists($fullPath)) {
            $this->createBackup($fullPath);
        }
        
        // Encode data to JSON
        $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logError("JSON encode error for $filePath: " . json_last_error_msg());
            return false;
        }
        
        // Write to temporary file first
        $tempFile = $fullPath . '.tmp';
        if (file_put_contents($tempFile, $jsonData) === false) {
            $this->logError("Failed to write temporary file: $tempFile");
            return false;
        }
        
        // Atomic move to final location
        if (!rename($tempFile, $fullPath)) {
            $this->logError("Failed to move temporary file to final location: $fullPath");
            unlink($tempFile); // Clean up temp file
            return false;
        }
        
        // Update cache
        $this->setCache($fullPath, $data);
        
        // Log successful write
        $this->logInfo("Data written successfully to: $filePath");
        
        return true;
    }
    
    /**
     * Append data to a JSON file
     * 
     * @param string $filePath Path to the JSON file
     * @param array $newData Data to append
     * @param string $key Key to append under (e.g., 'users', 'products')
     * @return bool Success status
     */
    public function append($filePath, $newData, $key) {
        // Read existing data
        $existingData = $this->read($filePath);
        if ($existingData === false) {
            return false;
        }
        
        // Initialize key if it doesn't exist
        if (!isset($existingData[$key])) {
            $existingData[$key] = [];
        }
        
        // Append new data
        if (is_array($newData)) {
            $existingData[$key] = array_merge($existingData[$key], $newData);
        } else {
            $existingData[$key][] = $newData;
        }
        
        // Update metadata
        if (isset($existingData['metadata'])) {
            $existingData['metadata']['last_updated'] = date(ISO_DATETIME_FORMAT);
            if (isset($existingData['metadata']['total_' . rtrim($key, 's')])) {
                $existingData['metadata']['total_' . rtrim($key, 's')] = count($existingData[$key]);
            }
        }
        
        // Write back to file
        return $this->write($filePath, $existingData);
    }
    
    /**
     * Insert a single record into a JSON file
     * 
     * @param string $filePath Path to the JSON file
     * @param array $record Record to insert
     * @param string $key Key to insert under (e.g., 'users', 'products')
     * @param string $idField Field name for the ID
     * @return string|false Generated ID or false on error
     */
    public function insert($filePath, $record, $key, $idField = 'id') {
        // Read existing data
        $existingData = $this->read($filePath);
        if ($existingData === false) {
            return false;
        }
        
        // Initialize key if it doesn't exist
        if (!isset($existingData[$key])) {
            $existingData[$key] = [];
        }
        
        // Generate unique ID
        $newId = $this->generateUniqueId($existingData[$key], $idField);
        $record[$idField] = $newId;
        
        // Add timestamp
        $record['created_at'] = date(ISO_DATETIME_FORMAT);
        $record['updated_at'] = date(ISO_DATETIME_FORMAT);
        
        // Insert record
        $existingData[$key][] = $record;
        
        // Update metadata
        if (isset($existingData['metadata'])) {
            $existingData['metadata']['last_updated'] = date(ISO_DATETIME_FORMAT);
            if (isset($existingData['metadata']['total_' . rtrim($key, 's')])) {
                $existingData['metadata']['total_' . rtrim($key, 's')]++;
            }
            if (isset($existingData['metadata']['next_' . rtrim($key, 's') . '_id'])) {
                $existingData['metadata']['next_' . rtrim($key, 's') . '_id']++;
            }
        }
        
        // Write back to file
        if ($this->write($filePath, $existingData)) {
            return $newId;
        }
        
        return false;
    }
    
    /**
     * Update a record in a JSON file
     * 
     * @param string $filePath Path to the JSON file
     * @param string $id ID of the record to update
     * @param array $updateData Data to update
     * @param string $key Key to update under (e.g., 'users', 'products')
     * @param string $idField Field name for the ID
     * @return bool Success status
     */
    public function update($filePath, $id, $updateData, $key, $idField = 'id') {
        // Read existing data
        $existingData = $this->read($filePath);
        if ($existingData === false) {
            return false;
        }
        
        // Find record to update
        $recordIndex = $this->findRecordIndex($existingData[$key] ?? [], $id, $idField);
        if ($recordIndex === false) {
            $this->logError("Record not found for update: $id in $filePath");
            return false;
        }
        
        // Update record
        $existingData[$key][$recordIndex] = array_merge(
            $existingData[$key][$recordIndex],
            $updateData,
            ['updated_at' => date(ISO_DATETIME_FORMAT)]
        );
        
        // Update metadata
        if (isset($existingData['metadata'])) {
            $existingData['metadata']['last_updated'] = date(ISO_DATETIME_FORMAT);
        }
        
        // Write back to file
        return $this->write($filePath, $existingData);
    }
    
    /**
     * Delete a record from a JSON file
     * 
     * @param string $filePath Path to the JSON file
     * @param string $id ID of the record to delete
     * @param string $key Key to delete from (e.g., 'users', 'products')
     * @param string $idField Field name for the ID
     * @return bool Success status
     */
    public function delete($filePath, $id, $key, $idField = 'id') {
        // Read existing data
        $existingData = $this->read($filePath);
        if ($existingData === false) {
            return false;
        }
        
        // Find record to delete
        $recordIndex = $this->findRecordIndex($existingData[$key] ?? [], $id, $idField);
        if ($recordIndex === false) {
            $this->logError("Record not found for deletion: $id in $filePath");
            return false;
        }
        
        // Remove record
        array_splice($existingData[$key], $recordIndex, 1);
        
        // Update metadata
        if (isset($existingData['metadata'])) {
            $existingData['metadata']['last_updated'] = date(ISO_DATETIME_FORMAT);
            if (isset($existingData['metadata']['total_' . rtrim($key, 's')])) {
                $existingData['metadata']['total_' . rtrim($key, 's')]--;
            }
        }
        
        // Write back to file
        return $this->write($filePath, $existingData);
    }
    
    /**
     * Search for records in a JSON file
     * 
     * @param string $filePath Path to the JSON file
     * @param array $criteria Search criteria
     * @param string $key Key to search under (e.g., 'users', 'products')
     * @return array Search results
     */
    public function search($filePath, $criteria, $key) {
        // Read existing data
        $existingData = $this->read($filePath);
        if ($existingData === false || !isset($existingData[$key])) {
            return [];
        }
        
        $results = [];
        $records = $existingData[$key];
        
        foreach ($records as $record) {
            if ($this->matchesCriteria($record, $criteria)) {
                $results[] = $record;
            }
        }
        
        return $results;
    }
    
    /**
     * Find a single record by ID
     * 
     * @param string $filePath Path to the JSON file
     * @param string $id ID of the record to find
     * @param string $key Key to search under (e.g., 'users', 'products')
     * @param string $idField Field name for the ID
     * @return array|false Record data or false if not found
     */
    public function findById($filePath, $id, $key, $idField = 'id') {
        // Read existing data
        $existingData = $this->read($filePath);
        if ($existingData === false || !isset($existingData[$key])) {
            return false;
        }
        
        foreach ($existingData[$key] as $record) {
            if (isset($record[$idField]) && $record[$idField] === $id) {
                return $record;
            }
        }
        
        return false;
    }
    
    /**
     * Get all records from a specific key
     * 
     * @param string $filePath Path to the JSON file
     * @param string $key Key to get records from (e.g., 'users', 'products')
     * @return array Records array
     */
    public function getAll($filePath, $key) {
        $data = $this->read($filePath);
        return $data[$key] ?? [];
    }
    
    /**
     * Count records in a specific key
     * 
     * @param string $filePath Path to the JSON file
     * @param string $key Key to count records from (e.g., 'users', 'products')
     * @return int Number of records
     */
    public function count($filePath, $key) {
        $data = $this->read($filePath);
        return count($data[$key] ?? []);
    }
    
    /**
     * Check if a record exists
     * 
     * @param string $filePath Path to the JSON file
     * @param string $id ID of the record to check
     * @param string $key Key to check under (e.g., 'users', 'products')
     * @param string $idField Field name for the ID
     * @return bool Whether record exists
     */
    public function exists($filePath, $id, $key, $idField = 'id') {
        return $this->findById($filePath, $id, $key, $idField) !== false;
    }
    
    /**
     * Clear cache for a specific file
     * 
     * @param string $filePath Path to the JSON file
     */
    public function clearCache($filePath = null) {
        if ($filePath === null) {
            $this->cache = [];
            $this->cacheTTL = [];
        } else {
            $fullPath = $this->getFullPath($filePath);
            unset($this->cache[$fullPath]);
            unset($this->cacheTTL[$fullPath]);
        }
    }
    
    /**
     * Get cache statistics
     * 
     * @return array Cache statistics
     */
    public function getCacheStats() {
        return [
            'total_cached_files' => count($this->cache),
            'cache_size' => count($this->cache),
            'cache_ttl' => $this->cacheTTL
        ];
    }
    
    /**
     * Validate file path for security
     * 
     * @param string $filePath File path to validate
     * @return bool Whether path is valid
     */
    private function validateFilePath($filePath) {
        // Check for directory traversal attempts
        if (strpos($filePath, '..') !== false) {
            return false;
        }
        
        // Ensure path is within data directory
        $realDataPath = realpath($this->dataPath);
        $realFilePath = realpath($filePath);
        
        if ($realFilePath === false || strpos($realFilePath, $realDataPath) !== 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get full path for a file
     * 
     * @param string $filePath Relative file path
     * @return string Full file path
     */
    private function getFullPath($filePath) {
        if (strpos($filePath, $this->dataPath) === 0) {
            return $filePath;
        }
        
        return $this->dataPath . '/' . ltrim($filePath, '/');
    }
    
    /**
     * Check if file is cached and not expired
     * 
     * @param string $fullPath Full file path
     * @return bool Whether file is cached
     */
    private function isCached($fullPath) {
        if (!isset($this->cache[$fullPath])) {
            return false;
        }
        
        if (!isset($this->cacheTTL[$fullPath])) {
            return false;
        }
        
        return $this->cacheTTL[$fullPath] > time();
    }
    
    /**
     * Get data from cache
     * 
     * @param string $fullPath Full file path
     * @return array Cached data
     */
    private function getFromCache($fullPath) {
        return $this->cache[$fullPath] ?? [];
    }
    
    /**
     * Set data in cache
     * 
     * @param string $fullPath Full file path
     * @param array $data Data to cache
     */
    private function setCache($fullPath, $data) {
        $this->cache[$fullPath] = $data;
        $this->cacheTTL[$fullPath] = time() + $this->defaultTTL;
    }
    
    /**
     * Generate unique ID for a record
     * 
     * @param array $records Existing records
     * @param string $idField Field name for the ID
     * @return string Generated unique ID
     */
    private function generateUniqueId($records, $idField) {
        $maxId = 0;
        
        foreach ($records as $record) {
            if (isset($record[$idField])) {
                $currentId = (int) $record[$idField];
                if ($currentId > $maxId) {
                    $maxId = $currentId;
                }
            }
        }
        
        return (string) ($maxId + 1);
    }
    
    /**
     * Find index of a record by ID
     * 
     * @param array $records Records array
     * @param string $id ID to search for
     * @param string $idField Field name for the ID
     * @return int|false Record index or false if not found
     */
    private function findRecordIndex($records, $id, $idField) {
        foreach ($records as $index => $record) {
            if (isset($record[$idField]) && $record[$idField] === $id) {
                return $index;
            }
        }
        
        return false;
    }
    
    /**
     * Check if a record matches search criteria
     * 
     * @param array $record Record to check
     * @param array $criteria Search criteria
     * @return bool Whether record matches criteria
     */
    private function matchesCriteria($record, $criteria) {
        foreach ($criteria as $field => $value) {
            if (!isset($record[$field])) {
                return false;
            }
            
            if (is_array($value)) {
                // Handle array criteria (e.g., tags)
                if (!is_array($record[$field]) || !array_intersect($value, $record[$field])) {
                    return false;
                }
            } else {
                // Handle exact match
                if ($record[$field] !== $value) {
                    return false;
                }
            }
        }
        
        return true;
    }
    
    /**
     * Create backup of a file
     * 
     * @param string $filePath Path to the file to backup
     */
    private function createBackup($filePath) {
        $backupPath = $filePath . '.backup.' . date('Y-m-d-H-i-s');
        if (file_exists($filePath)) {
            copy($filePath, $backupPath);
        }
    }
    
    /**
     * Log error message
     * 
     * @param string $message Error message
     */
    private function logError($message) {
        if (LOG_ERRORS) {
            error_log("[Database Error] $message");
        }
    }
    
    /**
     * Log info message
     * 
     * @param string $message Info message
     */
    private function logInfo($message) {
        if (LOG_ERRORS) {
            error_log("[Database Info] $message");
        }
    }
}