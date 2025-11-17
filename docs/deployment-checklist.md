# Quick Deployment Checklist

## Pre-Deployment Checklist
- [ ] cPanel hosting account ready
- [ ] Domain pointed to hosting
- [ ] SSL certificate installed
- [ ] PHP 8.2+ available
- [ ] MySQL 8.0+ available

## Database Setup
- [ ] Database `ormawa_unp` created
- [ ] Database user created with privileges
- [ ] Migration SQL imported via phpMyAdmin
- [ ] Seeding SQL imported (optional)
- [ ] Database connection tested

## File Upload
- [ ] Deployment package created
- [ ] Files uploaded to public_html
- [ ] Directory permissions set to 755
- [ ] File permissions set to 644
- [ ] Storage directory permissions set to 755
- [ ] Bootstrap/cache permissions set to 755
- [ ] .env file permissions set to 600

## Configuration
- [ ] .env file created from template
- [ ] Database credentials updated
- [ ] APP_URL set to correct domain
- [ ] Application key generated
- [ ] Mail configuration updated
- [ ] Cache and session drivers configured

## Final Setup
- [ ] Composer dependencies installed
- [ ] Node.js assets built (if applicable)
- [ ] Storage directories created
- [ ] Symbolic links created
- [ ] Application caches optimized
- [ ] Storage link created

## Testing
- [ ] Homepage loads correctly
- [ ] Database connection working
- [ ] Login functionality tested
- [ ] File upload tested
- [ ] Email functionality tested
- [ ] All pages accessible

## Security
- [ ] Default passwords changed
- [ ] Sensitive files secured (600 permissions)
- [ ] Test files removed
- [ ] Debug mode disabled
- [ ] Error logging configured

## Post-Deployment
- [ ] Regular backups configured
- [ ] Monitoring set up
- [ ] Performance optimized
- [ ] Documentation reviewed
- [ ] Team trained on maintenance

## Emergency Contacts
- Hosting Support: [Phone/Email]
- Database Admin: [Phone/Email]
- Developer: [Phone/Email]

## Useful URLs
- cPanel: https://yourdomain.com:2083
- phpMyAdmin: Available in cPanel
- Application: https://yourdomain.com
- Admin Panel: https://yourdomain.com/admin

## Default Credentials (CHANGE IMMEDIATELY)
- Admin: admin@ormawa-unp.com / admin123
- BEM: bem@ormawa-unp.com / admin123
- HIMTIF: himtif@ormawa-unp.com / admin123

---

**Deployment Status:** [ ] In Progress [ ] Completed [ ] Failed

**Notes:** _____________________________________________________________
_______________________________________________________________________