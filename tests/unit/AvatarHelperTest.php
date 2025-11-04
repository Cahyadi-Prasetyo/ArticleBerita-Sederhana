<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class AvatarHelperTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Load the avatar helper
        helper('avatar');
    }

    // Tests for get_user_initials() function with different name formats
    public function testGetUserInitialsWithFullName(): void
    {
        $initials = get_user_initials('John Doe');
        $this->assertEquals('JD', $initials);
    }

    public function testGetUserInitialsWithSingleName(): void
    {
        $initials = get_user_initials('John');
        $this->assertEquals('J', $initials);
    }

    public function testGetUserInitialsWithEmptyName(): void
    {
        $initials = get_user_initials('');
        $this->assertEquals('U', $initials);
    }

    public function testGetUserInitialsWithNullName(): void
    {
        $initials = get_user_initials(null);
        $this->assertEquals('U', $initials);
    }

    public function testGetUserInitialsWithMultipleNames(): void
    {
        $initials = get_user_initials('John Michael Doe Smith');
        $this->assertEquals('JM', $initials);
    }

    public function testGetUserInitialsWithExtraSpaces(): void
    {
        $initials = get_user_initials('  John   Doe  ');
        $this->assertEquals('JD', $initials);
    }

    public function testGetUserInitialsWithSpecialCharacters(): void
    {
        $initials = get_user_initials('José María');
        $this->assertEquals('JM', $initials);
    }

    public function testGetUserInitialsWithNumbers(): void
    {
        $initials = get_user_initials('John2 Doe3');
        $this->assertEquals('JD', $initials);
    }

    // Tests for generate_default_avatar() URL generation
    public function testGenerateDefaultAvatar(): void
    {
        $avatarUrl = generate_default_avatar('JD', 48);
        
        $this->assertStringContainsString('ui-avatars.com', $avatarUrl);
        $this->assertStringContainsString('name=JD', $avatarUrl);
        $this->assertStringContainsString('size=48', $avatarUrl);
        $this->assertStringContainsString('background=007bff', $avatarUrl);
        $this->assertStringContainsString('color=ffffff', $avatarUrl);
        $this->assertStringContainsString('bold=true', $avatarUrl);
        $this->assertStringContainsString('rounded=false', $avatarUrl);
    }

    public function testGenerateDefaultAvatarWithSpecialCharacters(): void
    {
        $avatarUrl = generate_default_avatar('J&D', 48);
        
        $this->assertStringContainsString('ui-avatars.com', $avatarUrl);
        $this->assertStringContainsString('name=J%26D', $avatarUrl); // URL encoded
    }

    public function testGenerateDefaultAvatarWithDefaultSize(): void
    {
        $avatarUrl = generate_default_avatar('AB');
        
        $this->assertStringContainsString('size=48', $avatarUrl); // Default size
    }

    public function testGenerateDefaultAvatarWithDifferentSizes(): void
    {
        $smallAvatar = generate_default_avatar('AB', 32);
        $largeAvatar = generate_default_avatar('AB', 64);
        
        $this->assertStringContainsString('size=32', $smallAvatar);
        $this->assertStringContainsString('size=64', $largeAvatar);
    }

    // Tests for get_user_avatar() function with various scenarios
    public function testGetUserAvatarWithoutUser(): void
    {
        // Mock session data
        $session = session();
        $session->set([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => null
        ]);

        $avatarUrl = get_user_avatar();
        
        $this->assertStringContainsString('ui-avatars.com', $avatarUrl);
        $this->assertStringContainsString('name=TU', $avatarUrl);
    }

    public function testGetUserAvatarWithUserObject(): void
    {
        $user = (object) [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'avatar' => null
        ];

        $avatarUrl = get_user_avatar($user);
        
        $this->assertStringContainsString('ui-avatars.com', $avatarUrl);
        $this->assertStringContainsString('name=JS', $avatarUrl);
    }

    public function testGetUserAvatarWithDifferentSizes(): void
    {
        $user = (object) [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => null
        ];

        $smallAvatar = get_user_avatar($user, 'small');
        $mediumAvatar = get_user_avatar($user, 'medium');
        $largeAvatar = get_user_avatar($user, 'large');

        $this->assertStringContainsString('size=32', $smallAvatar);
        $this->assertStringContainsString('size=48', $mediumAvatar);
        $this->assertStringContainsString('size=64', $largeAvatar);
    }

    public function testGetUserAvatarWithInvalidSize(): void
    {
        $user = (object) [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => null
        ];

        $avatarUrl = get_user_avatar($user, 'invalid');
        
        // Should fallback to medium size (48px)
        $this->assertStringContainsString('size=48', $avatarUrl);
    }

    public function testGetUserAvatarWithEmptySession(): void
    {
        // Clear session data
        $session = session();
        $session->remove(['name', 'email', 'avatar']);

        $avatarUrl = get_user_avatar();
        
        // Should use default "User" name and generate "U" initial
        $this->assertStringContainsString('ui-avatars.com', $avatarUrl);
        $this->assertStringContainsString('name=U', $avatarUrl);
    }

    public function testGetUserAvatarWithNonExistentCustomAvatar(): void
    {
        $user = (object) [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'avatar' => 'nonexistent.jpg'
        ];

        $avatarUrl = get_user_avatar($user);
        
        // Should fallback to default avatar since file doesn't exist
        $this->assertStringContainsString('ui-avatars.com', $avatarUrl);
        $this->assertStringContainsString('name=TU', $avatarUrl);
    }

    // Tests for error handling with missing files and invalid data
    public function testValidateAvatarFileWithNonExistentFile(): void
    {
        $isValid = validate_avatar_file('nonexistent.jpg');
        $this->assertFalse($isValid);
    }

    public function testValidateAvatarFileWithEmptyFilename(): void
    {
        $isValid = validate_avatar_file('');
        $this->assertFalse($isValid);
    }

    public function testValidateAvatarFileWithNullFilename(): void
    {
        $isValid = validate_avatar_file(null);
        $this->assertFalse($isValid);
    }

    public function testValidateAvatarFileWithInvalidExtension(): void
    {
        // Create a temporary file with invalid extension for testing
        $tempDir = sys_get_temp_dir();
        $testFile = $tempDir . '/test.txt';
        file_put_contents($testFile, 'test content');
        
        $isValid = validate_avatar_file('test.txt');
        $this->assertFalse($isValid);
        
        // Clean up
        if (file_exists($testFile)) {
            unlink($testFile);
        }
    }

    public function testGetUserAvatarWithMalformedUserObject(): void
    {
        $user = (object) [
            'name' => null,
            'email' => null,
            'avatar' => null
        ];

        $avatarUrl = get_user_avatar($user);
        
        // Should handle null name gracefully and generate "U" initial
        $this->assertStringContainsString('ui-avatars.com', $avatarUrl);
        $this->assertStringContainsString('name=U', $avatarUrl);
    }

    // Helper function tests
    public function testGetAvatarSizes(): void
    {
        $sizes = get_avatar_sizes();
        
        $this->assertIsArray($sizes);
        $this->assertArrayHasKey('small', $sizes);
        $this->assertArrayHasKey('medium', $sizes);
        $this->assertArrayHasKey('large', $sizes);
        $this->assertEquals(32, $sizes['small']);
        $this->assertEquals(48, $sizes['medium']);
        $this->assertEquals(64, $sizes['large']);
    }
}