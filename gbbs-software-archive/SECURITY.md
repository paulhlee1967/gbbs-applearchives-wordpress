# Security Policy

## Supported Versions

We release patches for security vulnerabilities in the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |

## Reporting a Vulnerability

If you discover a security vulnerability within the GBBS Software Archive plugin, please send an email to the plugin author at [your-email@example.com]. Please include the following information:

- A description of the vulnerability
- Steps to reproduce the issue
- The potential impact of the vulnerability
- Any suggested fixes or mitigations

### Security Best Practices

The GBBS Software Archive plugin implements several security measures:

- **File Type Validation**: Only allows specific Apple II file formats
- **Upload Restrictions**: Configurable file size limits and type restrictions
- **Rate Limiting**: Download rate limiting per IP address
- **Secure File Storage**: Files stored outside web root with .htaccess protection
- **Input Sanitization**: All user inputs are sanitized and validated
- **Nonce Verification**: All forms use WordPress nonces for CSRF protection
- **Capability Checks**: Proper WordPress capability checks for all admin functions

### File Security

- All uploaded files are stored in a dedicated directory outside the web root
- Each directory contains .htaccess files to prevent direct access
- Files are only accessible through WordPress download handlers
- File type validation prevents execution of potentially dangerous files

### User Permissions

- Download access can be restricted to logged-in users
- Admin functions require appropriate WordPress capabilities
- File uploads are restricted to users with proper permissions

## Security Updates

Security updates will be released as soon as possible after a vulnerability is discovered and verified. We will:

1. Acknowledge receipt of the vulnerability report
2. Investigate and verify the issue
3. Develop and test a fix
4. Release an update with security patches
5. Document the vulnerability and fix in the changelog

## Responsible Disclosure

We follow responsible disclosure practices:

- We will not publicly disclose vulnerabilities until a fix is available
- We will credit security researchers who responsibly report vulnerabilities
- We will work with researchers to ensure proper testing of fixes

## Contact

For security-related questions or to report vulnerabilities, please contact:

- **Email**: [your-email@example.com]
- **Plugin Author**: Paul H. Lee

Thank you for helping keep the GBBS Software Archive plugin secure!
