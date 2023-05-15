const defaultConfig = require('./vendor/arkecosystem/foundation/resources/tailwind.config.js');

module.exports = {
    ...defaultConfig,
    theme: {
        ...defaultConfig.theme,
        extend: {
            ...defaultConfig.theme.extend,
            height: {
                ...defaultConfig.theme.extend.height,
                '50': '12.5rem',
                '50.5' : '12.625rem'
            },
            width: {
                ...defaultConfig.theme.extend.width,
                '33': '8.25rem',
                '50': '12.5rem',
                '100': '25rem',
                '137': '34.25rem',
                '175': '43.5rem',
                '191': '47.75rem',
            },
            minWidth: {
                ...defaultConfig.theme.extend.minWidth,
                '50': '12.5rem',
                '60': '15rem',
            },
            animation: {
                'reverse-spin': 'reverse-spin 1s linear infinite'
            },
            keyframes: {
                'reverse-spin': {
                    from: {
                        transform: 'rotate(360deg)'
                    },
                }
            },
        },
    },

    variants: {
        ...defaultConfig.variants,
        borderWidth: ['responsive', 'hover', 'focus', 'first', 'focus-visible'],
        borderColor: ['responsive', 'dark', 'group-hover', 'focus-within', 'hover', 'focus', 'disabled', 'focus-visible'],
        borderRadius: ['dark', 'responsive', 'hover', 'focus', 'focus-within'],
        margin: ['responsive', 'hover', 'focus', 'first'],
        padding: ['responsive', 'hover', 'focus', 'first'],
        textColor: ['responsive', 'dark', 'group-hover', 'focus-within', 'hover', 'focus', 'disabled'],
        pointerEvents: ['responsive', 'disabled'],
    },
}
