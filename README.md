# Simple Infopanel 

[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/InfoDeskMe)

## Web
https://www.simple-infopanel.com

## About
The simple infopanel is a free digital signage tool. It works on localhost:80 and shows such information like images or videos, titles, description and QR-Codes that you can edit via the built-in edting tool (GUI). Moreover, you can upload your own images on server or link them from the Internet. The GUI generates some QR-Code with link automatically. The Infopanel GUI provides a very simple role concept. As Admin, you can add editors so that they can sign in too. The editor role can only change / edit the information and has no access to the basic settings. The infopanel and its GUI are open source and free to install on any devices capable with all modern browsers. IE 11 will not work. 

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
| Frontend | [React](https://reactjs.org/) JavaScript |
| Frontend | [TWIG](https://twig.symfony.com/) HTML |
| Backend | [Symfony](https://symfony.com/) PHP |

## Requirements
- Docker must have been installed and STARTED before installation
- Docker composer must have been installed 
- At least 1 Gb free space on Server if you do NOT want to use the built-in image upload feature. 

## What's new? 
- The entire project has been restructured. From now on, there are two separate services: frontend and backend. 
- Frontend has been switched to Vite.js for an improved build process.
- Symfony version update in Backend.
- Please note that the backoffice has a new URL! See "After installation".
- New licence (!)


## Installation

**It is possible that you will be asked about the database or admin account during the installation. In such a case, you only need to answer "yes" (It is necessary because I use a function for creating a default administrator account on the very first attempt).**

Linux (as root): 
```sh
chmod +x ./install_linux.sh
./install_linux.sh
```

Windows (as Administrator !!!): 
```sh
install_windows.bat
```

Please note if you started an installation via the script and it failed you would not have an opportunity to install the app again. Anyway, you can also try all steps from the both shell scripts and just install the app manuelly. Otherwise, please redownload the app and try again. 

## After installation 
- Go to your YOUR-SITE.COM or http://LOCALHOST and if the installation has been successfully fineshed you will see the infopanel. 
- Of course you can edit the content: You need to go to YOUR-SITE.COM/login or http://LOCALHOST:8080/, edit the slides, changing the configuration and add new informations as well. Also, you can activate or deactivate the ticker if necessary. 
- LOGIN: admin@admin.com
- PASSWORD: admin
**DO NOT FORGET TO CHANGE THE PASSWORD AFTER THE VERY FIRST LOGIN!**

> Please note if you would like to use the Infopanel in the Internet: 
- Please make sure that the `public` folder is not accessable from the Internet in case you would like to use this infopanel accessable via the Internet. You can adjust the necessary settings in `.development/nginx/default.conf`
- You need to change the credentials saved in the docker-compose.yml or BETTER you need to extract them into the separate `.ENV` file. 
- Good luck :-) 

## Donate
This software is absolutly free, but a very small donation would make me happy!

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

<<<<<<< HEAD
v.1.0.8
=======
v.1.0.7
>>>>>>> 9b759b1b08d3ad44bc33ec046c0bd9690f899810
