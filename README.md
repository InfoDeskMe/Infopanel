# Simple Infopanel 

[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/InfoDeskMe)

## About
The simple infopanel is a free digital signage tool. It works on localhost:80 and shows such information like images, titles, description and QR-Codes that you can edit via the built-in edting tool (GUI). Moreover, you can upload your own images on server or link them from the Internet. The GUI generates some QR-Code with link automatically. The Infopanel GUI provides a very simple role concept. As Admin, you can add editors so that they can sign in too. The editor role can only change / edit the information and has no access to the basic settings. The infopanel and its GUI are open source and free to install on any devices capable with all modern browsers. IE 11 will not work. 

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
- At least 1 Gb free space on Server if you do NOT want to use the built-in image upload feature. 

## Installation

**It is possible that you will be asked about the database or admin account during the installation. In such a case, you only need to answer "yes" (It is necessary because I use a function for creating a default administrator account on the very first attempt).**

Linux (as root): 
```sh
chmod +x ./install_linux.sh
./install_linux.sh
```

Windows (as Administrator): 
```sh
installation_windows.bat
```

Please note if you started an installation via the script and it failed you would not have an opportunity to install the app again. Anyway, you can also try all steps from the both shell scripts and just install the app manuelly. Otherwise, please redownload the app and try again. 

## After installation 
- Go to your YOUR-SITE.COM or http://LOCALHOST and if the installation has been successfully fineshed you will see the infopanel. 
- Of course you can edit the content: You need to go to YOUR-SITE.COM/login or http://LOCALHOST/login, edit the slides, changing the configuration and add new informations as well. Also, you can activate or deactivate the ticker if necessary. 
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

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions.

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

## Contact
You can contact me via [anton @ anton-s .com] no spaces! I do not promise to answer ;-) 

v.1.0.3