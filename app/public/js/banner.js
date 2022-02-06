let banner = document.getElementById('banner')
let backgroundLayout = document.getElementsByClassName('container')[0]

function showDonateBanner() {
    if (document.cookie.split(';').filter((item) => item.trim().startsWith('bannersi=')).length) {
        console.log('The cookie "bannersi" exists')
    } else {
        backgroundLayout.style.filter = "blur(8px)"; 
        
        let closeButton = '<div class=\'donateBanner\'><button onclick=\"deleteBlur()\" class=\"btn-close btn-place\"></button>'
        let bannerContent = "<h3>Just a second!</h3><br><br><p>Dear Administrator,</p><br><br><p>thanks for using this software. As you noticed, it is absolutely free, but the author spent a lot of time creating it in the hope that someone will find it cool and useful. If so, I would appreciate a small donation via paypal. Thank you.</p> <br><br>"
        let bannerDonateButton = "<a target=\"_blank\" href=\"https://www.paypal.com/donate?hosted_button_id=VE3KZYNP87DDW\"><button class=\"btn btn-info\" onclick=\"deleteBlur()\">DONATE</button></a></div>"
        
        banner.innerHTML = closeButton + bannerContent + bannerDonateButton 
        setBannerCookie("bannersi","true",365);
    }
}

function deleteBlur() {
    banner.remove();
    backgroundLayout.style.filter = "unset";
}

function setBannerCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
  }

showDonateBanner() 