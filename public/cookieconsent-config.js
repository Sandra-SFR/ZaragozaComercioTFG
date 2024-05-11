import 'https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@v3.0.0-rc.17/dist/cookieconsent.umd.js';

CookieConsent.run({
    categories: {
        necessary: {
            enabled: true,  // this category is enabled by default
            readOnly: true  // this category cannot be disabled
        },
        analytics: {}
    },

    language: {
        default: 'es',
        translations: {
            es: {
                consentModal: {
                    title: 'Utilizamos cookies',
                    description: '',
                    acceptAllBtn: 'Aceptar todas',
                    acceptNecessaryBtn: 'Rechazar todas',
                    //showPreferencesBtn: 'Gestionar preferencias individuales'
                },
                // preferencesModal: {
                //     title: 'Gestionar preferencias de cookies',
                //     acceptAllBtn: 'Aceptar todas',
                //     acceptNecessaryBtn: 'Rechazar todas',
                //     savePreferencesBtn: 'Aceptar selección actual',
                //     closeIconLabel: 'Cerrar modal',
                //     sections: [
                //         {
                //             title: '¿Alguien dijo... cookies?',
                //             description: '¡Quiero una!'
                //         },
                //         {
                //             title: 'Cookies estrictamente necesarias',
                //             description: 'Estas cookies son esenciales para el correcto funcionamiento del sitio web y no se pueden desactivar.',
                //             linkedCategory: 'necesarias'
                //         },
                //         {
                //             title: 'Rendimiento y análisis',
                //             description: 'Estas cookies recopilan información sobre cómo utilizas nuestro sitio web. Todos los datos se anonimizan y no se pueden utilizar para identificarte.',
                //             linkedCategory: 'analiticas'
                //         },
                //         {
                //             title: 'Más información',
                //             description: 'Para cualquier consulta relacionada con mi política de cookies y tus opciones, por favor <a href="#contact-page">contáctanos</a>'
                //         }
                //     ]
                // }
            }
        }
    }
});