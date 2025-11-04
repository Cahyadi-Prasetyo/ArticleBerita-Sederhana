<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class UserProfileComponentTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Load required helpers
        helper(['avatar', 'url']);
    }

    public function testUserProfileComponentRendersWithSessionData(): void
    {
        // Mock session data
        $session = session();
        $session->set([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'user_id' => 1,
            'avatar' => null
        ]);

        // Capture the output of the user profile component
        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify the component renders correctly
        $this->assertStringContainsString('user-profile', $output);
        $this->assertStringContainsString('John Doe', $output);
        $this->assertStringContainsString('john.doe@example.com', $output);
        $this->assertStringContainsString('avatar-img', $output);
        $this->assertStringContainsString('ui-avatars.com', $output);
        // URL is escaped, so check for URL-encoded version
        $this->assertStringContainsString('name%3DJD', $output);
    }

    public function testUserProfileComponentWithoutEmail(): void
    {
        // Mock session data without email
        $session = session();
        $session->set([
            'name' => 'Jane Smith',
            'email' => '',
            'user_id' => 2,
            'avatar' => null
        ]);

        // Capture the output of the user profile component
        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify the component renders correctly without email
        $this->assertStringContainsString('user-profile', $output);
        $this->assertStringContainsString('Jane Smith', $output);
        $this->assertStringNotContainsString('user-email', $output);
        $this->assertStringContainsString('avatar-img', $output);
    }

    public function testUserProfileComponentWithNoSessionData(): void
    {
        // Clear session data
        $session = session();
        $session->destroy();

        // Capture the output of the user profile component
        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify the component doesn't render without session data
        $this->assertEmpty(trim($output));
    }

    public function testUserProfileComponentEscapesUserData(): void
    {
        // Mock session data with potentially dangerous content
        $session = session();
        $session->set([
            'name' => '<script>alert("xss")</script>John',
            'email' => '<script>alert("xss")</script>john@example.com',
            'user_id' => 1,
            'avatar' => null
        ]);

        // Capture the output of the user profile component
        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify the content is properly escaped
        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringContainsString('&lt;script&gt;', $output);
        $this->assertStringContainsString('John', $output);
        $this->assertStringContainsString('john@example.com', $output);
    }

    public function testUserProfileComponentHasAccessibilityFeatures(): void
    {
        // Mock session data
        $session = session();
        $session->set([
            'name' => 'Accessible User',
            'email' => 'accessible@example.com',
            'user_id' => 1,
            'avatar' => null
        ]);

        // Capture the output of the user profile component
        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify accessibility features
        $this->assertStringContainsString('alt="Accessible User Avatar"', $output);
        $this->assertStringContainsString('title="Accessible User"', $output);
        $this->assertStringContainsString('title="accessible@example.com"', $output);
        $this->assertStringContainsString('onerror=', $output); // Fallback for broken images
    }

    protected function tearDown(): void
    {
        // Clean up session
        session()->destroy();
        parent::tearDown();
    }
}