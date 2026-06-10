// config.js
//const BASE_URL = "http://181.204.219.154:3396/preoperacional"; 
//const BASE_URL = "http://localhost/preoperacional"; 


// config.js

const APP_FOLDER = "preoperacional";

const BASE_URL = (() => {
    const protocol = window.location.protocol;
    const host = window.location.host;

    return `${protocol}//${host}/${APP_FOLDER}`;
})();
