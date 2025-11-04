# Avatar Directory

This directory stores user avatar images for the admin user profile system.

## Structure
- `default-avatar.svg` - Default SVG avatar placeholder
- `index.html` - Prevents directory browsing
- `.htaccess` - Security configuration
- `README.md` - This documentation file

## File Naming Convention
User avatars should be named using the pattern: `user_{user_id}_avatar.{extension}`

Example: `user_123_avatar.jpg`

## Supported Formats
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)
- WebP (.webp)

## Security Features
- PHP execution disabled
- Only image files allowed
- Directory browsing disabled
- Proper MIME types set
- Cache headers configured

## File Size Recommendations
- Maximum file size: 2MB
- Recommended dimensions: 256x256px
- Supported sizes: 32px, 48px, 64px (generated dynamically)

## Usage
The avatar helper functions will automatically:
1. Check for custom user avatar
2. Fall back to default avatar if none exists
3. Generate appropriate URLs for different sizes
4. Handle missing or broken image files