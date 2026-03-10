module.exports = {
    methods: {
        /**
         * Translate the given key.
         */
        __(key, replace) {
            let translation, translationNotFound = true

            if (!window._translations || !window._locale) {
                return key
            }

            try {
                translation = key.split('.').reduce((t, i) => t[i] || null, window._translations[window._locale].php)
                if (translation) {
                    translationNotFound = false
                }
            } catch (e) {
                translation = key
            }

            if (translationNotFound) {
                translation = window._translations[window._locale]['json'] && window._translations[window._locale]['json'][key]
                    ? window._translations[window._locale]['json'][key]
                    : key
            }

            if (replace && typeof replace === 'object') {
                _.forEach(replace, (value, key) => {
                    translation = translation.replace(':' + key, value)
                })
            }

            return translation
        }
    },
}