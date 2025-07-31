# Football Tournament Website

A complete PHP-based football tournament management system with admin panel, automatic fixture generation, live group tables, and knockout bracket visualization.

## Features

### Admin Panel
- **Secure Authentication** - Admin login system with session management
- **Tournament Management** - Create, edit, delete tournaments with status tracking
- **Team Management** - Add teams to groups with flag uploads
- **Player Management** - Add players to teams with photos and details
- **Fixture Generation** - Automatic round-robin group stage fixture creation
- **Match Management** - Enter results, manage match dates and times
- **Live Standings** - Automatic group table calculations (P, W, D, L, GF, GA, GD, Pts)

### Public Frontend
- **Tournament Overview** - Modern responsive design with tournament statistics
- **Group Standings** - Live updated group tables with team flags
- **Match Results** - Display of recent and upcoming matches
- **Knockout Bracket** - Visual bracket progression (coming soon)
- **Mobile Responsive** - Works on all device sizes

### Technical Features
- **PHP 8+ Backend** - Modern PHP with PDO database connections
- **MySQL Database** - Relational database with proper foreign keys
- **MVC Structure** - Clean separation of concerns
- **File Uploads** - Team flags and player photos
- **Security** - Password hashing, SQL injection prevention, session management
- **Bootstrap UI** - Modern, responsive user interface

## Installation

### Requirements
- PHP 8.0 or higher
- MySQL 5.7 or MariaDB 10.3+
- Web server (Apache/Nginx)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd tournament
   ```

2. **Configure Database**
   - Edit `config/database.php` with your database credentials
   - Default settings:
     - Host: localhost
     - Database: football_tournament
     - Username: root
     - Password: (empty)

3. **Create Database and Tables**
   - Visit `http://your-domain/setup.php` in your browser
   - This will create the database schema and default admin user

4. **Set Permissions**
   ```bash
   chmod 755 uploads/
   chmod 755 uploads/flags/
   chmod 755 uploads/players/
   ```

## Usage

### Admin Panel
1. Go to `/admin/login.php`
2. Login with default credentials:
   - Username: `admin`
   - Password: `admin123`

### Creating a Tournament
1. **Create Tournament** - Add basic tournament information
2. **Add Teams** - Add teams to groups (A, B, C, etc.)
3. **Generate Fixtures** - Automatically create group stage matches
4. **Enter Results** - Update match scores as games are played
5. **View Standings** - Monitor live group table updates

### Public Access
- Visit the main site at `/` to view tournaments
- Select different tournaments from the dropdown
- View group standings, match results, and tournament progress

## Database Schema

### Main Tables
- `tournaments` - Tournament information and status
- `teams` - Teams with group assignments and flags
- `players` - Player details and photos
- `matches` - Match fixtures and results
- `group_standings` - Calculated group table data
- `admin_users` - Admin authentication

### Key Relationships
- Teams belong to tournaments and groups
- Players belong to teams
- Matches link home/away teams
- Standings are calculated from match results

## File Structure

```
tournament/
├── admin/                  # Admin panel
│   ├── includes/          # Shared admin components
│   ├── tournaments/       # Tournament management
│   ├── teams/            # Team management
│   ├── matches/          # Match management
│   └── login.php         # Admin authentication
├── assets/               # CSS and static files
├── classes/              # PHP classes (MVC models)
├── config/               # Configuration files
├── database/             # Database schema
├── uploads/              # File uploads directory
├── index.php            # Public homepage
└── setup.php            # Database setup script
```

## API Endpoints

The system is designed with RESTful principles for future mobile app integration:

- `GET /api/tournaments` - List tournaments
- `GET /api/tournaments/{id}` - Tournament details
- `GET /api/tournaments/{id}/teams` - Tournament teams
- `GET /api/tournaments/{id}/matches` - Tournament matches
- `GET /api/tournaments/{id}/standings` - Group standings

## Security Features

- **Password Hashing** - bcrypt for admin passwords
- **SQL Injection Prevention** - PDO prepared statements
- **Session Management** - Secure session handling
- **Input Sanitization** - All user input is sanitized
- **File Upload Security** - Restricted file types and locations

## Customization

### Adding New Groups
- Groups are dynamically created (A-H supported)
- Modify team creation form to add more groups if needed

### Styling
- Edit `assets/css/admin.css` for admin panel styling
- Edit `assets/css/public.css` for public site styling
- Bootstrap 5 classes can be used throughout

### Database Configuration
- Modify `config/database.php` for different database settings
- Update `config/config.php` for application settings

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check database credentials in `config/database.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **File Upload Issues**
   - Check directory permissions for `uploads/`
   - Verify PHP upload settings (upload_max_filesize, post_max_size)

3. **Admin Login Issues**
   - Run setup.php again to recreate admin user
   - Check session configuration in PHP

4. **Fixture Generation Not Working**
   - Ensure teams are assigned to groups
   - Check for existing fixtures (may create duplicates)

## License

This project is open source and available under the MIT License.

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review the database schema
3. Verify file permissions and PHP configuration
