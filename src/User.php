<?php
/** The single profile used by the app (no login). */
class User extends Model
{
    protected const TABLE = 'users';
    protected const PK = 'user_id';
    protected const FIELDS = ['full_name', 'email', 'phone_number'];

    private const PROFILE_IMAGE_DIR = __DIR__ . '/../uploads/profile';
    private const PROFILE_IMAGE_MAX_SIZE = 2 * 1024 * 1024;

    public function validate(array $d): ?string
    {
        if (trim($d['full_name'] ?? '') === '')
            return 'Enter your full name.';
        if (!filter_var($d['email'] ?? '', FILTER_VALIDATE_EMAIL))
            return 'Enter a valid email address.';
        if (strlen($d['phone_number'] ?? '') > 15)
            return 'Phone number can be up to 15 characters.';
        return null;
    }

    public function profile(): array
    {
        return $this->find(USER_ID) ?? ['full_name' => '', 'email' => '', 'phone_number' => '', 'profile_image' => null];
    }

    public function updateProfile(array $data, ?string $profileImage = null): bool
    {
        $data = array_intersect_key($data, array_flip(self::FIELDS));
        if ($profileImage === null) {
            return $this->update(USER_ID, $data);
        }

        $statement = $this->db->prepare(
            'UPDATE users SET full_name = :full_name, email = :email, phone_number = :phone_number, profile_image = :profile_image WHERE user_id = :user_id'
        );
        return $statement->execute($data + ['profile_image' => $profileImage, 'user_id' => USER_ID]);
    }

    public function saveProfileImage(array $upload): ?string
    {
        if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('The profile picture could not be uploaded. Please try again.');
        }
        if (($upload['size'] ?? 0) > self::PROFILE_IMAGE_MAX_SIZE) {
            throw new InvalidArgumentException('Choose a profile picture smaller than 2 MB.');
        }
        if (!isset($upload['tmp_name']) || !is_uploaded_file($upload['tmp_name'])) {
            throw new InvalidArgumentException('Choose a valid image file.');
        }
        $actualSize = filesize($upload['tmp_name']);
        if ($actualSize === false || $actualSize > self::PROFILE_IMAGE_MAX_SIZE) {
            throw new InvalidArgumentException('Choose a profile picture smaller than 2 MB.');
        }

        $imageInfo = getimagesize($upload['tmp_name']);
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!$imageInfo || !isset($extensions[$mime]) || $imageInfo['mime'] !== $mime) {
            throw new InvalidArgumentException('Use a JPG, PNG, or WebP image.');
        }
        if ($imageInfo[0] > 6000 || $imageInfo[1] > 6000 || $imageInfo[0] * $imageInfo[1] > 20000000) {
            throw new InvalidArgumentException('Choose an image with dimensions no larger than 6000 by 6000 pixels.');
        }

        if (!is_dir(self::PROFILE_IMAGE_DIR) && !mkdir(self::PROFILE_IMAGE_DIR, 0755, true) && !is_dir(self::PROFILE_IMAGE_DIR)) {
            throw new RuntimeException('The profile picture folder could not be created.');
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
        if (!move_uploaded_file($upload['tmp_name'], self::PROFILE_IMAGE_DIR . '/' . $filename)) {
            throw new RuntimeException('The profile picture could not be saved.');
        }

        return $filename;
    }

    public function removeProfileImage(?string $filename): void
    {
        if ($filename && preg_match('/\A[a-f0-9]{32}\.(jpg|png|webp)\z/', $filename)) {
            $path = self::PROFILE_IMAGE_DIR . '/' . $filename;
            if (is_file($path) && !unlink($path)) {
                throw new RuntimeException('The previous profile picture could not be removed.');
            }
        }
    }
}
