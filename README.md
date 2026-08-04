# Simple Infopanel 

[![License](https://img.shields.io/badge/license-Custom%20(MIT%20%2B%20commercial)-orange.svg)](#license)

## Web
https://www.simple-infopanel.com

## About
Simple Infopanel is a free, open-source digital signage tool. It displays a rotating set of slides -- images or videos, a title, a description, and an automatically generated QR code -- on any screen with a modern browser (Internet Explorer 11 is not supported).

**Free for personal and non-commercial use.** If you're a company wanting to use this in production or as part of a product/service, you need a separate commercial license -- see [License](#license) below, or contact me directly.

Content is managed through a built-in editing tool (the GUI/backoffice). You can:
- Add slides with your own uploaded images, or link to images/videos hosted elsewhere on the Internet.
- Show a scrolling news ticker alongside the slides.
- Manage two user roles: **Admins** can do everything, including managing settings and other users; **Editors** can add and edit content but can't touch settings or users.

## Screenshots
![Infopanel](https://i.postimg.cc/GLSsC8kn/2021-10-31-13-16-10.png)
---------------------------------      
![Infopanel GUI](https://i.postimg.cc/W3yYvN76/Infopanel1.png)
---------------------------------
![Infopanel Settings](https://i.postimg.cc/WtL9d5Cd/Infopanel2.png)
---------------------------------
![Infopanel Usermanagement](https://i.postimg.cc/zvRtDstm/Infopanel3.png)
---------------------------------

## Tech Stack

| Stack | Technologies |
| ------ | ------ |
| Frontend | [React](https://reactjs.org/) JavaScript (the public display) |
| Backend | [Symfony](https://symfony.com/) PHP, with [Twig](https://twig.symfony.com/) HTML (the backoffice/GUI) |

## Requirements
- Docker, installed and running, before you start the installation.
- Docker Compose, installed alongside Docker (it ships with Docker Desktop; on Linux it may be a separate package).
- At least 1 GB of free disk space. More if you plan to upload a lot of images/videos through the built-in upload feature -- uploaded files are stored on the server.

## What's new in this version
- The project has been restructured into two separate services: frontend (the public display) and backend (the Symfony app and backoffice).
- The frontend build now uses Vite.js.
- Symfony has been updated to a newer version.
- **The backoffice moved to a new URL** -- see "After installation" below.
- New license terms (see "License" below).


## Installation

Linux (as root): 
```sh
chmod +x ./install_linux.sh
./install_linux.sh
```

Windows (as Administrator !!!): 
```sh
install_windows.bat
```

If the install script fails partway through, re-running it usually won't help, since it may have already generated a `.env` file or partially started containers. If that happens, look at the individual steps inside the script (generating `.env`, `docker compose build`, `docker compose up -d --wait`) and run them by hand one at a time so you can see which one is failing. As a last resort, redownload the app and start over.

## After installation 
- Open `http://YOUR-SITE.COM` (or `http://localhost` on your own machine). Once the installation has finished, you'll see the public display.
- To edit content, open `http://YOUR-SITE.COM:8080/login` (or `http://localhost:8080/login`) instead -- edit slides, change settings, and turn the ticker on or off from there. The backoffice deliberately listens on its own port (8080), separate from the public display on port 80, so the admin login isn't exposed on whatever port you make public for the display itself.
- The installer generates a random database password (saved to `.env` in the project root) and a random initial admin password, printed to the console **once** at the end of the install. Copy it down -- logging in with it immediately forces a password change, and no other page is reachable until you do.

> If you're exposing the Infopanel to the public Internet:
- Only forward/expose port 80 (the public display) to the Internet. Keep port 8080 (the backoffice) reachable only from a trusted network, e.g. your local network or a VPN -- see `.development/nginx/default.conf` if you need to adjust this.
- Good luck :-) 

## Running under your own domain
The panel talks to the backend through a relative `/api` path by default, proxied by nginx -- no rebuild needed to deploy under a different domain or behind HTTPS. If you need the frontend to reach the backend somewhere else entirely (a different host or port), set `VITE_API_BASE` in `frontend/.env` (see `frontend/.env.example`) before building the frontend image.

### HTTPS
This project's own nginx serves plain HTTP. For a public deployment, put a reverse proxy in front of it that terminates TLS, for example [Caddy](https://caddyserver.com/) (which fetches and renews Let's Encrypt certificates automatically):

```
your-domain.example {
    reverse_proxy localhost:80
}
```

Login sends a session cookie, so this matters even for a "just me" install once it's reachable over the internet.

## Donate
This software is absolutely free, but a very small donation would make me happy!

[![PAYPAL](https://www.paypalobjects.com/en_US/DK/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/donate?hosted_button_id=VE3KZYNP87DDW)

or other options:

<noscript><a href="https://liberapay.com/AntonS/donate"><img alt="Donate using Liberapay" src="https://liberapay.com/assets/widgets/donate.svg"></a></noscript>

## License
Simple Infopanel | Copyright (c) Anton S

This Software License Agreement ("Agreement") governs the use of the software ("Software"). By downloading, installing, or using the Software, you agree to be bound by the terms and conditions set forth in this Agreement.

1. Open-Source and Free Usage
The Software is released as open-source under the MIT. You are free to use, modify, and distribute the Software for non-commercial purposes, provided that you comply with the terms of the selected open-source license.

2. Commercial Use by Companies
Any company, organization, or business entity ("Company") wishing to use the Software for commercial purposes must obtain a commercial license. Commercial use includes, but is not limited to, using the Software in a production environment, distributing the Software as part of a product or service, or using it internally for business operations.

3. Licensing Terms for Companies
Companies are required to enter into a separate commercial licensing agreement with the developer. The terms, pricing, and conditions of this commercial license will be determined on a case-by-case basis. For inquiries regarding commercial licensing, please contact [anton @ anton-s .com].

4. Disclaimer of Warranties
The Software is provided "as-is," without any express or implied warranties, including, but not limited to, the implied warranties of merchantability and fitness for a particular purpose. The author shall not be held liable for any damages arising from the use of the Software.

5. Limitation of Liability
In no event shall the author or copyright holders be liable for any claims, damages, or other liability, whether in an action of contract, tort, or otherwise, arising from, out of, or in connection with the Software or the use or other dealings in the Software.

6. Termination
This Agreement is effective until terminated. Your rights under this Agreement will terminate automatically without notice from the author if you fail to comply with any term(s) of this Agreement.

7. Governing Law
This Agreement shall be governed by and construed in accordance with the laws of Germany, without regard to its conflict of laws principles.

8. Contact Information
For any questions or concerns regarding this Agreement, please contact [anton @ anton-s .com].

v.2.0.1