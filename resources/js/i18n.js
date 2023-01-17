import i18n from 'qa'
import Backend from 'i18next-xhr-backend'
import { initReactI18next } from 'react-i18next'
import * as Constants from './constants'

function loadUserNativeLang() {
    localStorage["lang"] = 'en';
    // const that = this;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let result = JSON.parse(this.responseText);
            if (result.data.country_name == "Germany") {
                localStorage["lang"] = 'de';
            } else if (result.data.country_name == "France") {
                localStorage["lang"] = 'fr';
            }
            if (localStorage.lang != "en") {
                window.location.reload();
            }
        }
    };
    const url = Constants.BASE_URL + '/api/get-geo-location';
    xhttp.open("GET", url, true);
    xhttp.send();
}

if (!localStorage.lang) {

    loadUserNativeLang();
}

$('body').addClass(localStorage.lang);

i18n
    .use(Backend)
    .use(initReactI18next)
    .init({
        lng: localStorage.lang,
        backend: {
            /* translation file path */
            loadPath: '/i18n/{{ns}}/{{lng}}.json'
        },
        fallbackLng: localStorage.lang,
        debug: true,
        /* can have multiple namespace, in case you want to divide a huge translation into smaller pieces and load them on demand */
        ns: ['translations'],
        defaultNS: 'translations',
        keySeparator: ".",
        interpolation: {
            escapeValue: false,
            formatSeparator: ','
        },
        react: {
            wait: true
        }
    })

export default i18n