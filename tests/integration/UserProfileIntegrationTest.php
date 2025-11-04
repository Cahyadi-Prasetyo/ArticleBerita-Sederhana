<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Integration Tests for User Profile Component
 * 
 * Tests user profile component rendering with different user data,
 * responsive design, accessibility features, and integration with
 * existing admin layout.
 * 
 * Requirements: 1.3, 1.5, 2.3, 3.5
 * 
 * @internal
 */
final class UserProfileIntegrationTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Load required helpers
        helper(['avatar', 'url']);
        
        // Ensure uploads directory exists for testing
        $uploadsDir = FCPATH . 'uploads/avatars';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }
    }

    /**
     * Test user profile component rendering with different user data
     * Requirements: 1.3, 2.3
     */
    public function testUserProfileComponentRenderingWithDifferentUserData(): void
    {
        // Test with complete user data
        $completeUserData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'user_id' => 1,
            'avatar' => null,
            'logged_in' => true
        ];

        $session = session();
        $session->set($completeUserData);

        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify complete rendering
        $this->assertStringContainsString('user-profile', $output);
        $this->assertStringContainsString('John Doe', $output);
        $this->assertStringContainsString('john.doe@example.com', $output);
        $this->assertStringContainsString('avatar-img', $output);
        $this->assertStringContainsString('user-name', $output);
        $this->assertStringContainsString('user-email', $output);

        // Test with minimal user data (no email)
        $minimalUserData = [
            'name' => 'Jane Smith',
            'email' => '',
            'user_id' => 2,
            'avatar' => null,
            'logged_in' => true
        ];

        $session->set($minimalUserData);

        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify rendering without email
        $this->assertStringContainsString('user-profile', $output);
        $this->assertStringContainsString('Jane Smith', $output);
        $this->assertStringNotContainsString('user-email', $output);

        // Test with long names and emails (text truncation)
        $longDataUser = [
            'name' => 'Very Long User Name That Should Be Truncated',
            'email' => 'very.long.email.address.that.should.be.truncated@example.com',
            'user_id' => 3,
            'avatar' => null,
            'logged_in' => true
        ];

        $session->set($longDataUser);

        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify long text handling
        $this->assertStringContainsString('user-profile', $output);
        $this->assertStringContainsString('title="Very Long User Name That Should Be Truncated"', $output);
        $this->assertStringContainsString('title="very.long.email.address.that.should.be.truncated@example.com"', $output);

        // Test with special characters
        $specialCharUser = [
            'name' => 'José María O\'Connor',
            'email' => 'jose.maria@example.com',
            'user_id' => 4,
            'avatar' => null,
            'logged_in' => true
        ];

        $session->set($specialCharUser);

        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify special character handling
        $this->assertStringContainsString('user-profile', $output);
        $this->assertStringContainsString('José María O&#039;Connor', $output);
        $this->assertStringContainsString('jose.maria@example.com', $output);
    }

    /**
     * Test responsive design on various screen sizes
     * Requirements: 1.5, 2.3
     */
    public function testResponsiveDesignOnVariousScreenSizes(): void
    {
        // Set up user data
        $session = session();
        $session->set([
            'name' => 'Responsive User',
            'email' => 'responsive@example.com',
            'user_id' => 1,
            'avatar' => null,
            'logged_in' => true
        ]);

        // Get the component output
        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify responsive CSS classes are present
        $this->assertStringContainsString('user-profile', $output);
        $this->assertStringContainsString('avatar-medium', $output);

        // Read the CSS file to verify responsive styles exist
        $cssContent = file_get_contents(FCPATH . 'assets/css/admin.css');

        // Verify mobile breakpoint styles exist
        $this->assertStringContainsString('@media (max-width: 768px)', $cssContent);
        $this->assertStringContainsString('@media (max-width: 480px)', $cssContent);

        // Verify responsive avatar sizing
        $this->assertStringContainsString('avatar-small', $cssContent);
        $this->assertStringContainsString('avatar-medium', $cssContent);
        $this->assertStringContainsString('avatar-large', $cssContent);

        // Verify responsive layout adjustments
        $this->assertStringContainsString('flex-direction: column', $cssContent);
        $this->assertStringContainsString('text-align: center', $cssContent);

        // Test different avatar sizes
        $avatarSizes = ['small', 'medium', 'large'];
        foreach ($avatarSizes as $size) {
            $avatarUrl = get_user_avatar(null, $size);
            $this->assertNotEmpty($avatarUrl);
            
            // Verify size-specific parameters in URL
            switch ($size) {
                case 'small':
                    $this->assertStringContainsString('size=32', $avatarUrl);
                    break;
                case 'medium':
                    $this->assertStringContainsString('size=48', $avatarUrl);
                    break;
                case 'large':
                    $this->assertStringContainsString('size=64', $avatarUrl);
                    break;
            }
        }
    }

    /**
     * Test accessibility features and ARIA labels
     * Requirements: 1.5, 3.5
     */
    public function testAccessibilityFeaturesAndAriaLabels(): void
    {
        // Set up user data
        $session = session();
        $session->set([
            'name' => 'Accessible User',
            'email' => 'accessible@example.com',
            'user_id' => 1,
            'avatar' => null,
            'logged_in' => true
        ]);

        // Get the component output
        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify ARIA labels and accessibility attributes
        $this->assertStringContainsString('aria-label="User profile: Accessible User"', $output);
        $this->assertStringContainsString('role="button"', $output);
        $this->assertStringContainsString('tabindex="0"', $output);

        // Verify alt text for avatar
        $this->assertStringContainsString('alt="Accessible User Avatar"', $output);

        // Verify title attributes for tooltips
        $this->assertStringContainsString('title="Accessible User"', $output);
        $this->assertStringContainsString('title="accessible@example.com"', $output);

        // Verify error handling for broken images
        $this->assertStringContainsString('onerror=', $output);

        // Verify lazy loading for performance
        $this->assertStringContainsString('loading="lazy"', $output);

        // Read CSS to verify accessibility features
        $cssContent = file_get_contents(FCPATH . 'assets/css/admin.css');

        // Verify high contrast mode support
        $this->assertStringContainsString('@media (prefers-contrast: high)', $cssContent);

        // Verify reduced motion support
        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $cssContent);

        // Verify focus styles
        $this->assertStringContainsString(':focus-within', $cssContent);
        $this->assertStringContainsString(':focus', $cssContent);

        // Verify print styles
        $this->assertStringContainsString('@media print', $cssContent);

        // Test with completely empty session data
        $session->remove(['name', 'email', 'user_id', 'avatar', 'logged_in']);

        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $emptyOutput = ob_get_clean();

        // Component should not render when session is completely empty
        // This matches the unit test behavior for consistency
        $this->assertEmpty(trim($emptyOutput), 'Component should not render without session data');
    }

    /**
     * Test integration with existing admin layout
     * Requirements: 1.3, 3.5
     */
    public function testIntegrationWithExistingAdminLayout(): void
    {
        // Set up user session
        $session = session();
        $session->set([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'user_id' => 1,
            'avatar' => null,
            'logged_in' => true
        ]);

        // Test integration with admin sidebar
        ob_start();
        include APPPATH . 'Views/admin/_partials/side_nav.php';
        $sideNavOutput = ob_get_clean();

        // Verify user profile is included in sidebar when logged in
        $this->assertStringContainsString('user-profile', $sideNavOutput);
        $this->assertStringContainsString('Admin User', $sideNavOutput);
        $this->assertStringContainsString('admin@example.com', $sideNavOutput);

        // Verify sidebar structure is maintained
        $this->assertStringContainsString('side-nav', $sideNavOutput);
        $this->assertStringContainsString('brand', $sideNavOutput);
        $this->assertStringContainsString('MyProjek Admin', $sideNavOutput);
        $this->assertStringContainsString('<nav>', $sideNavOutput);

        // Verify navigation links are still present
        $this->assertStringContainsString('Dashboard', $sideNavOutput);
        $this->assertStringContainsString('Posts', $sideNavOutput);
        $this->assertStringContainsString('Feedback', $sideNavOutput);
        $this->assertStringContainsString('Settings', $sideNavOutput);
        $this->assertStringContainsString('Logout', $sideNavOutput);

        // Test without logged in session
        $session->set('logged_in', false);

        ob_start();
        include APPPATH . 'Views/admin/_partials/side_nav.php';
        $loggedOutSideNavOutput = ob_get_clean();

        // User profile should not be included when not logged in
        $this->assertStringNotContainsString('user-profile', $loggedOutSideNavOutput);
        $this->assertStringNotContainsString('Admin User', $loggedOutSideNavOutput);

        // But sidebar structure should remain
        $this->assertStringContainsString('side-nav', $loggedOutSideNavOutput);
        $this->assertStringContainsString('MyProjek Admin', $loggedOutSideNavOutput);

        // Test CSS integration
        $cssContent = file_get_contents(FCPATH . 'assets/css/admin.css');

        // Verify user profile styles integrate well with existing sidebar styles
        $this->assertStringContainsString('.side-nav', $cssContent);
        $this->assertStringContainsString('.user-profile', $cssContent);

        // Verify consistent color scheme
        $this->assertStringContainsString('background: teal', $cssContent); // Sidebar background
        $this->assertStringContainsString('background: rgba(255, 255, 255, 0.1)', $cssContent); // User profile background

        // Verify consistent spacing and layout
        $this->assertStringContainsString('padding: 1rem', $cssContent);
        $this->assertStringContainsString('margin-bottom: 1rem', $cssContent);
    }

    /**
     * Test user profile component with custom avatar
     * Requirements: 2.3, 3.5
     */
    public function testUserProfileComponentWithCustomAvatar(): void
    {
        // Create a test avatar file
        $testAvatarPath = FCPATH . 'uploads/avatars/test_avatar.jpg';
        $testImageContent = base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/8A');
        file_put_contents($testAvatarPath, $testImageContent);

        // Set up user data with custom avatar
        $session = session();
        $session->set([
            'name' => 'Avatar User',
            'email' => 'avatar@example.com',
            'user_id' => 1,
            'avatar' => 'test_avatar.jpg',
            'logged_in' => true
        ]);

        // Get the component output
        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify custom avatar is used (URL may be encoded)
        $this->assertTrue(
            strpos($output, 'uploads/avatars/test_avatar.jpg') !== false ||
            strpos($output, 'uploads%2Favatars%2Ftest_avatar.jpg') !== false,
            'Custom avatar path should be present in output'
        );

        // Clean up test file
        if (file_exists($testAvatarPath)) {
            unlink($testAvatarPath);
        }
    }

    /**
     * Test user profile component error handling
     * Requirements: 2.3, 3.5
     */
    public function testUserProfileComponentErrorHandling(): void
    {
        // Test with broken avatar file reference
        $session = session();
        $session->set([
            'name' => 'Broken Avatar User',
            'email' => 'broken@example.com',
            'user_id' => 1,
            'avatar' => 'nonexistent.jpg',
            'logged_in' => true
        ]);

        // Get the component output
        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Should fallback to default avatar since file doesn't exist
        $this->assertStringContainsString('ui-avatars.com', $output);
        $this->assertStringContainsString('name%3DBA', $output); // URL encoded initials for "Broken Avatar"

        // Verify error handling JavaScript is present
        $this->assertStringContainsString('onerror=', $output);
        $this->assertStringContainsString('this.classList.add(\'error\')', $output);

        // Test with malformed session data (both name and email empty)
        $session->set([
            'name' => '',
            'email' => '',
            'user_id' => null,
            'avatar' => null,
            'logged_in' => true
        ]);

        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $nullOutput = ob_get_clean();

        // Component should not render with empty name and email
        $this->assertEmpty(trim($nullOutput));
    }

    /**
     * Test user profile component performance and loading states
     * Requirements: 3.5
     */
    public function testUserProfileComponentPerformanceAndLoadingStates(): void
    {
        // Set up user data
        $session = session();
        $session->set([
            'name' => 'Performance User',
            'email' => 'performance@example.com',
            'user_id' => 1,
            'avatar' => null,
            'logged_in' => true
        ]);

        // Get the component output
        ob_start();
        include APPPATH . 'Views/admin/_partials/user_profile.php';
        $output = ob_get_clean();

        // Verify lazy loading is implemented
        $this->assertStringContainsString('loading="lazy"', $output);

        // Read CSS to verify loading states
        $cssContent = file_get_contents(FCPATH . 'assets/css/admin.css');

        // Verify loading animation exists
        $this->assertStringContainsString('@keyframes loading', $cssContent);
        $this->assertStringContainsString('animation: loading', $cssContent);

        // Verify error state styling
        $this->assertStringContainsString('.avatar-img.error', $cssContent);
        $this->assertStringContainsString('background-color: #dc3545', $cssContent);

        // Test avatar URL generation performance
        $startTime = microtime(true);
        for ($i = 0; $i < 100; $i++) {
            get_user_avatar();
        }
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Avatar generation should be fast (less than 1 second for 100 calls)
        $this->assertLessThan(1.0, $executionTime);
    }

    protected function tearDown(): void
    {
        // Clean up session
        session()->destroy();
        
        // Clean up any test files
        $testFiles = glob(FCPATH . 'uploads/avatars/test_*');
        foreach ($testFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        
        parent::tearDown();
    }
}