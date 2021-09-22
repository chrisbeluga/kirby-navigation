// Import Libraries
import VueNestable from 'vue-nestable'

// Fields
import app from './app.vue'

// Init
panel.plugin('beluga/navigation', {
    fields: {
        navigation: app
    },
	use: VueNestable
})
