<?php

class FileValidator {

    private array $allowedExtensions = [
        "jpg", "jpeg", "png", "gif", "webp",
        "pdf", "txt", "doc", "docx",
        "xls", "xlsx", "ppt", "pptx",
        "zip", "mp4", "mp3"
    ];

    private array $allowedMimeTypes = [
        "image/jpeg", "image/png", "image/gif", "image/webp",
        "application/pdf", "text/plain",
        "application/msword",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/vnd.ms-excel",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "application/vnd.ms-powerpoint",
        "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        "application/zip", "video/mp4", "audio/mpeg"
    ];

    private int $maxFileSize = 10 * 1024 * 1024;

    private array $errors = [];

    public function validate(array $file): bool {

        $this->errors = [];

        if ($file["error"] !== UPLOAD_ERR_OK) {
            $this->errors[] = "File upload failed with error code: " . $file["error"];
            return false;
        }

        if ($file["size"] === 0) {
            $this->errors[] = "File is empty.";
        }

        if ($file["size"] > $this->maxFileSize) {
            $this->errors[] = "File exceeds the 10MB size limit.";
        }

        $fileExt  = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $fileMime = mime_content_type($file["tmp_name"]);

        if (!in_array($fileExt, $this->allowedExtensions)) {
            $this->errors[] = "File type not allowed.";
        }

        if (!in_array($fileMime, $this->allowedMimeTypes)) {
            $this->errors[] = "File content does not match its extension.";
        }

        return empty($this->errors);
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function getAllowedExtensions(): array {
        return $this->allowedExtensions;
    }
}