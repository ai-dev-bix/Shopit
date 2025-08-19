<?php
/**
 * ImageHandler Class
 * Handles secure image uploads, processing, and management for the marketplace platform
 */

// Prevent direct access
if (!defined('SECURE_ACCESS')) {
    define('SECURE_ACCESS', true);
}

class ImageHandler {
    private $db;
    private $uploadPath;
    private $allowedTypes;
    private $maxFileSize;
    private $maxImagesPerListing;
    
    public function __construct() {
        $this->db = new Database();
        $this->uploadPath = UPLOADS_PATH;
        $this->allowedTypes = ALLOWED_IMAGE_TYPES;
        $this->maxFileSize = MAX_FILE_SIZE_BYTES;
        $this->maxImagesPerListing = MAX_IMAGES_PER_LISTING;
        
        // Create upload directories if they don't exist
        $this->createUploadDirectories();
    }
    
    /**
     * Upload image from file
     * 
     * @param array $file $_FILES array element
     * @param string $type Upload type (product, service, user)
     * @param string $entityId Entity ID (listing ID or user ID)
     * @return array|false Upload result or false on error
     */
    public function uploadImage($file, $type, $entityId) {
        // Validate file
        if (!$this->validateFile($file)) {
            return false;
        }
        
        // Check upload limits
        if (!$this->checkUploadLimits($type, $entityId)) {
            return false;
        }
        
        // Generate unique filename
        $filename = $this->generateUniqueFilename($file['name'], $type);
        $uploadPath = $this->getUploadPath($type, $entityId);
        $fullPath = $uploadPath . '/' . $filename;
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            $this->logError("Failed to move uploaded file: " . $file['name']);
            return false;
        }
        
        // Process image (resize, compress, etc.)
        if (!$this->processImage($fullPath)) {
            $this->logError("Failed to process image: " . $filename);
            unlink($fullPath); // Clean up
            return false;
        }
        
        // Generate image metadata
        $metadata = $this->generateImageMetadata($fullPath, $file);
        
        // Save image record to database
        $imageRecord = [
            'id' => $this->generateImageId(),
            'filename' => $filename,
            'original_name' => $file['name'],
            'type' => $type,
            'entity_id' => $entityId,
            'path' => $fullPath,
            'url' => $this->getImageUrl($type, $entityId, $filename),
            'size' => filesize($fullPath),
            'dimensions' => $metadata['dimensions'],
            'mime_type' => $metadata['mime_type'],
            'uploaded_at' => date(ISO_DATETIME_FORMAT),
            'status' => 'active'
        ];
        
        $success = $this->saveImageRecord($imageRecord);
        
        if ($success) {
            $this->logInfo("Image uploaded successfully: $filename");
            return [
                'success' => true,
                'image_id' => $imageRecord['id'],
                'filename' => $filename,
                'url' => $imageRecord['url'],
                'dimensions' => $metadata['dimensions'],
                'size' => $imageRecord['size']
            ];
        }
        
        // Clean up if database save failed
        unlink($fullPath);
        return false;
    }
    
    /**
     * Capture image from camera
     * 
     * @param string $base64Data Base64 encoded image data
     * @param string $type Upload type (product, service, user)
     * @param string $entityId Entity ID (listing ID or user ID)
     * @return array|false Upload result or false on error
     */
    public function captureImage($base64Data, $type, $entityId) {
        // Validate base64 data
        if (!$this->validateBase64Data($base64Data)) {
            return false;
        }
        
        // Check upload limits
        if (!$this->checkUploadLimits($type, $entityId)) {
            return false;
        }
        
        // Decode base64 data
        $imageData = base64_decode($base64Data);
        if ($imageData === false) {
            $this->logError("Failed to decode base64 image data");
            return false;
        }
        
        // Validate image data
        if (!$this->validateImageData($imageData)) {
            $this->logError("Invalid image data from camera");
            return false;
        }
        
        // Generate unique filename
        $filename = $this->generateUniqueFilename('camera_capture.jpg', $type);
        $uploadPath = $this->getUploadPath($type, $entityId);
        $fullPath = $uploadPath . '/' . $filename;
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Save image data
        if (file_put_contents($fullPath, $imageData) === false) {
            $this->logError("Failed to save camera image: $filename");
            return false;
        }
        
        // Process image
        if (!$this->processImage($fullPath)) {
            $this->logError("Failed to process camera image: $filename");
            unlink($fullPath);
            return false;
        }
        
        // Generate image metadata
        $metadata = $this->generateImageMetadata($fullPath, [
            'name' => 'camera_capture.jpg',
            'type' => 'image/jpeg',
            'size' => strlen($imageData)
        ]);
        
        // Save image record to database
        $imageRecord = [
            'id' => $this->generateImageId(),
            'filename' => $filename,
            'original_name' => 'camera_capture.jpg',
            'type' => $type,
            'entity_id' => $entityId,
            'path' => $fullPath,
            'url' => $this->getImageUrl($type, $entityId, $filename),
            'size' => filesize($fullPath),
            'dimensions' => $metadata['dimensions'],
            'mime_type' => $metadata['mime_type'],
            'uploaded_at' => date(ISO_DATETIME_FORMAT),
            'status' => 'active',
            'source' => 'camera'
        ];
        
        $success = $this->saveImageRecord($imageRecord);
        
        if ($success) {
            $this->logInfo("Camera image captured successfully: $filename");
            return [
                'success' => true,
                'image_id' => $imageRecord['id'],
                'filename' => $filename,
                'url' => $imageRecord['url'],
                'dimensions' => $metadata['dimensions'],
                'size' => $imageRecord['size']
            ];
        }
        
        // Clean up if database save failed
        unlink($fullPath);
        return false;
    }
    
    /**
     * Delete image
     * 
     * @param string $imageId Image ID to delete
     * @param string $userId User ID requesting deletion
     * @return bool Success status
     */
    public function deleteImage($imageId, $userId) {
        // Get image record
        $imageRecord = $this->getImageRecord($imageId);
        if (!$imageRecord) {
            $this->logError("Image not found: $imageId");
            return false;
        }
        
        // Check if user can delete this image
        if (!$this->canUserDeleteImage($imageRecord, $userId)) {
            $this->logError("User cannot delete image: $imageId");
            return false;
        }
        
        // Delete file from filesystem
        if (file_exists($imageRecord['path'])) {
            if (!unlink($imageRecord['path'])) {
                $this->logError("Failed to delete image file: " . $imageRecord['path']);
                return false;
            }
        }
        
        // Delete image record from database
        $success = $this->deleteImageRecord($imageId);
        
        if ($success) {
            $this->logInfo("Image deleted successfully: $imageId");
        }
        
        return $success;
    }
    
    /**
     * Get images for entity
     * 
     * @param string $type Entity type (product, service, user)
     * @param string $entityId Entity ID
     * @return array Entity images
     */
    public function getEntityImages($type, $entityId) {
        $db = new Database();
        $criteria = [
            'type' => $type,
            'entity_id' => $entityId,
            'status' => 'active'
        ];
        
        return $db->search($this->getImagesFilePath(), $criteria, 'images');
    }
    
    /**
     * Resize image
     * 
     * @param string $imagePath Path to image file
     * @param int $maxWidth Maximum width
     * @param int $maxHeight Maximum height
     * @param bool $maintainAspectRatio Whether to maintain aspect ratio
     * @return bool Success status
     */
    public function resizeImage($imagePath, $maxWidth = 800, $maxHeight = 600, $maintainAspectRatio = true) {
        if (!file_exists($imagePath)) {
            return false;
        }
        
        // Get image info
        $imageInfo = getimagesize($imagePath);
        if ($imageInfo === false) {
            return false;
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];
        
        // Check if resizing is needed
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            return true; // No resizing needed
        }
        
        // Calculate new dimensions
        if ($maintainAspectRatio) {
            $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
            $newWidth = round($originalWidth * $ratio);
            $newHeight = round($originalHeight * $ratio);
        } else {
            $newWidth = $maxWidth;
            $newHeight = $maxHeight;
        }
        
        // Create image resource
        $sourceImage = $this->createImageResource($imagePath, $mimeType);
        if (!$sourceImage) {
            return false;
        }
        
        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG images
        if ($mimeType === 'image/png') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefill($newImage, 0, 0, $transparent);
        }
        
        // Resize image
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        // Save resized image
        $success = $this->saveImageResource($newImage, $imagePath, $mimeType);
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($newImage);
        
        return $success;
    }
    
    /**
     * Compress image
     * 
     * @param string $imagePath Path to image file
     * @param int $quality JPEG quality (1-100)
     * @return bool Success status
     */
    public function compressImage($imagePath, $quality = 85) {
        if (!file_exists($imagePath)) {
            return false;
        }
        
        $imageInfo = getimagesize($imagePath);
        if ($imageInfo === false) {
            return false;
        }
        
        $mimeType = $imageInfo['mime'];
        
        // Only compress JPEG images
        if ($mimeType !== 'image/jpeg') {
            return true;
        }
        
        // Create image resource
        $sourceImage = imagecreatefromjpeg($imagePath);
        if (!$sourceImage) {
            return false;
        }
        
        // Save with compression
        $success = imagejpeg($sourceImage, $imagePath, $quality);
        
        // Clean up
        imagedestroy($sourceImage);
        
        return $success;
    }
    
    /**
     * Generate thumbnail
     * 
     * @param string $imagePath Path to source image
     * @param string $thumbnailPath Path for thumbnail
     * @param int $width Thumbnail width
     * @param int $height Thumbnail height
     * @return bool Success status
     */
    public function generateThumbnail($imagePath, $thumbnailPath, $width = 150, $height = 150) {
        if (!file_exists($imagePath)) {
            return false;
        }
        
        $imageInfo = getimagesize($imagePath);
        if ($imageInfo === false) {
            return false;
        }
        
        $mimeType = $imageInfo['mime'];
        
        // Create image resource
        $sourceImage = $this->createImageResource($imagePath, $mimeType);
        if (!$sourceImage) {
            return false;
        }
        
        // Create thumbnail
        $thumbnail = imagecreatetruecolor($width, $height);
        
        // Preserve transparency for PNG images
        if ($mimeType === 'image/png') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefill($thumbnail, 0, 0, $transparent);
        }
        
        // Create thumbnail
        imagecopyresampled($thumbnail, $sourceImage, 0, 0, 0, 0, $width, $height, $imageInfo[0], $imageInfo[1]);
        
        // Save thumbnail
        $success = $this->saveImageResource($thumbnail, $thumbnailPath, $mimeType);
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($thumbnail);
        
        return $success;
    }
    
    /**
     * Validate uploaded file
     * 
     * @param array $file $_FILES array element
     * @return bool Whether file is valid
     */
    private function validateFile($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->logError("File upload error: " . $file['error']);
            return false;
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            $this->logError("File too large: " . $file['size'] . " bytes");
            return false;
        }
        
        // Check file type
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedTypes)) {
            $this->logError("Invalid file type: $extension");
            return false;
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, ALLOWED_IMAGE_MIME_TYPES)) {
            $this->logError("Invalid MIME type: $mimeType");
            return false;
        }
        
        // Validate image dimensions
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $this->logError("Invalid image file: " . $file['name']);
            return false;
        }
        
        // Check minimum dimensions
        if ($imageInfo[0] < 100 || $imageInfo[1] < 100) {
            $this->logError("Image too small: " . $imageInfo[0] . "x" . $imageInfo[1]);
            return false;
        }
        
        // Check maximum dimensions
        if ($imageInfo[0] > 4000 || $imageInfo[1] > 4000) {
            $this->logError("Image too large: " . $imageInfo[0] . "x" . $imageInfo[1]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Check upload limits
     * 
     * @param string $type Upload type
     * @param string $entityId Entity ID
     * @return bool Whether upload is allowed
     */
    private function checkUploadLimits($type, $entityId) {
        $currentImages = $this->getEntityImages($type, $entityId);
        
        if (count($currentImages) >= $this->maxImagesPerListing) {
            $this->logError("Upload limit reached for entity: $entityId");
            return false;
        }
        
        return true;
    }
    
    /**
     * Generate unique filename
     * 
     * @param string $originalName Original filename
     * @param string $type Upload type
     * @return string Unique filename
     */
    private function generateUniqueFilename($originalName, $type) {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $timestamp = time();
        $random = bin2hex(random_bytes(8));
        
        return "{$type}_{$timestamp}_{$random}.{$extension}";
    }
    
    /**
     * Get upload path for type and entity
     * 
     * @param string $type Upload type
     * @param string $entityId Entity ID
     * @return string Upload path
     */
    private function getUploadPath($type, $entityId) {
        switch ($type) {
            case 'product':
                return PRODUCT_IMAGES_PATH . '/' . $entityId;
            case 'service':
                return SERVICE_IMAGES_PATH . '/' . $entityId;
            case 'user':
                return USER_IMAGES_PATH . '/' . $entityId;
            default:
                return $this->uploadPath . '/' . $type . '/' . $entityId;
        }
    }
    
    /**
     * Get image URL
     * 
     * @param string $type Upload type
     * @param string $entityId Entity ID
     * @param string $filename Filename
     * @return string Image URL
     */
    private function getImageUrl($type, $entityId, $filename) {
        return "/uploads/{$type}/{$entityId}/{$filename}";
    }
    
    /**
     * Process uploaded image
     * 
     * @param string $imagePath Path to image file
     * @return bool Success status
     */
    private function processImage($imagePath) {
        // Resize if too large
        if (!$this->resizeImage($imagePath, 1200, 800)) {
            return false;
        }
        
        // Compress image
        if (!$this->compressImage($imagePath, 85)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Generate image metadata
     * 
     * @param string $imagePath Path to image file
     * @param array $fileInfo File information
     * @return array Image metadata
     */
    private function generateImageMetadata($imagePath, $fileInfo) {
        $imageInfo = getimagesize($imagePath);
        
        return [
            'dimensions' => [
                'width' => $imageInfo[0] ?? 0,
                'height' => $imageInfo[1] ?? 0
            ],
            'mime_type' => $fileInfo['type'] ?? 'image/jpeg',
            'file_size' => $fileInfo['size'] ?? 0
        ];
    }
    
    /**
     * Create image resource from file
     * 
     * @param string $imagePath Path to image file
     * @param string $mimeType MIME type
     * @return resource|false Image resource or false on error
     */
    private function createImageResource($imagePath, $mimeType) {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                return imagecreatefromjpeg($imagePath);
            case 'image/png':
                return imagecreatefrompng($imagePath);
            case 'image/webp':
                return imagecreatefromwebp($imagePath);
            default:
                return false;
        }
    }
    
    /**
     * Save image resource to file
     * 
     * @param resource $image Image resource
     * @param string $path File path
     * @param string $mimeType MIME type
     * @return bool Success status
     */
    private function saveImageResource($image, $path, $mimeType) {
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                return imagejpeg($image, $path, 85);
            case 'image/png':
                return imagepng($image, $path, 6);
            case 'image/webp':
                return imagewebp($image, $path, 85);
            default:
                return false;
        }
    }
    
    /**
     * Validate base64 data
     * 
     * @param string $base64Data Base64 encoded data
     * @return bool Whether data is valid
     */
    private function validateBase64Data($base64Data) {
        if (empty($base64Data)) {
            return false;
        }
        
        // Check if it's valid base64
        if (base64_decode($base64Data, true) === false) {
            return false;
        }
        
        // Check data size
        $dataSize = strlen($base64Data);
        if ($dataSize > $this->maxFileSize) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate image data
     * 
     * @param string $imageData Raw image data
     * @return bool Whether data is valid image
     */
    private function validateImageData($imageData) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $imageData);
        finfo_close($finfo);
        
        return in_array($mimeType, ALLOWED_IMAGE_MIME_TYPES);
    }
    
    /**
     * Generate unique image ID
     * 
     * @return string Unique image ID
     */
    private function generateImageId() {
        return 'img_' . time() . '_' . bin2hex(random_bytes(8));
    }
    
    /**
     * Save image record to database
     * 
     * @param array $imageRecord Image record data
     * @return bool Success status
     */
    private function saveImageRecord($imageRecord) {
        return $this->db->insert($this->getImagesFilePath(), $imageRecord, 'images');
    }
    
    /**
     * Get image record by ID
     * 
     * @param string $imageId Image ID
     * @return array|false Image record or false if not found
     */
    private function getImageRecord($imageId) {
        return $this->db->findById($this->getImagesFilePath(), $imageId, 'images');
    }
    
    /**
     * Delete image record from database
     * 
     * @param string $imageId Image ID
     * @return bool Success status
     */
    private function deleteImageRecord($imageId) {
        return $this->db->delete($this->getImagesFilePath(), $imageId, 'images');
    }
    
    /**
     * Check if user can delete image
     * 
     * @param array $imageRecord Image record
     * @param string $userId User ID
     * @return bool Whether user can delete
     */
    private function canUserDeleteImage($imageRecord, $userId) {
        // Check if user is admin
        $user = new User();
        if ($user->loadById($userId) && $user->isAdmin()) {
            return true;
        }
        
        // Check if user owns the entity
        $entityType = $imageRecord['type'];
        $entityId = $imageRecord['entity_id'];
        
        if ($entityType === 'user' && $entityId === $userId) {
            return true;
        }
        
        if (in_array($entityType, ['product', 'service'])) {
            $listing = new Listing();
            if ($listing->loadById($entityId) && $listing->getUserId() === $userId) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get images file path
     * 
     * @return string Images file path
     */
    private function getImagesFilePath() {
        return DATA_PATH . '/images/images.json';
    }
    
    /**
     * Create upload directories
     */
    private function createUploadDirectories() {
        $directories = [
            $this->uploadPath,
            PRODUCT_IMAGES_PATH,
            SERVICE_IMAGES_PATH,
            USER_IMAGES_PATH
        ];
        
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }
    }
    
    /**
     * Log error message
     * 
     * @param string $message Error message
     */
    private function logError($message) {
        if (LOG_ERRORS) {
            error_log("[ImageHandler Error] $message");
        }
    }
    
    /**
     * Log info message
     * 
     * @param string $message Info message
     */
    private function logInfo($message) {
        if (LOG_ERRORS) {
            error_log("[ImageHandler Info] $message");
        }
    }
}