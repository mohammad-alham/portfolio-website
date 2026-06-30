# Network Engineer Portfolio Website

A modern, responsive portfolio website for a Professional Network Engineer / Network Administrator.

## Tech Stack

- **PHP** - Server-side includes and templating
- **HTML5** - Semantic markup
- **CSS3** - Custom styles with CSS variables, animations, and responsive design
- **JavaScript** - Vanilla JS for interactivity and animations
- **TailwindCSS** - Utility-first CSS framework (via CDN)

## Features

- Dark/Light mode with persistent theme
- Fully responsive (Mobile, Tablet, Laptop, Desktop)
- Smooth scroll animations (Intersection Observer)
- Typing animation effect
- Portfolio filtering system
- Project detail modal
- Contact form validation
- Skill progress bars with animation
- Statistics counter animation
- Back to top button
- SEO-friendly meta tags
- Loading animation

## Project Structure

```
/
├── index.php          # Home page
├── about.php          # About page with timeline
├── skills.php         # Skills with progress bars
├── services.php       # Services page
├── projects.php       # Projects with filtering
├── certificates.php   # Certifications showcase
├── contact.php        # Contact form & info
├── assets/
│   ├── css/
│   │   └── style.css  # Custom styles
│   ├── js/
│   │   └── main.js    # JavaScript functionality
│   ├── images/        # Image assets
│   └── icons/         # Icon assets
├── includes/
│   ├── header.php     # Header, meta, CDN links
│   ├── navbar.php     # Navigation bar
│   └── footer.php     # Footer & scripts
└── README.md
```

## Setup

1. Upload all files to your web server (works on Apache, Nginx, IIS)
2. No database or server-side processing required
3. Replace placeholder content:
   - Edit `includes/header.php` for SEO meta tags
   - Edit each page for your personal information
   - Replace images in `assets/images/`
   - Update social media links in footer and contact page
4. For the contact form to actually send emails, configure your server's mail handler or use a third-party service like Formspree

## Customization

- **Colors**: Edit CSS variables in `assets/css/style.css`
- **Content**: Edit individual PHP pages
- **Images**: Replace files in `assets/images/`
- **Resume/CV**: Update download link in `about.php`

## Hosting

Works perfectly on InfinityFree and any standard PHP hosting.

## License

All rights reserved.
