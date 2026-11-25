// vue-nestable 2.6.0 (https://github.com/rhwilr/vue-nestable), ported to Vue 3
import VueNestable from './VueNestable.vue'
import VueNestableHandle from './VueNestableHandle.vue'

// export named components
export {
  VueNestable,
  VueNestableHandle
}

// export vue component installer
export default {
  install: function (Vue, options) {
    Vue.component('VueNestable', VueNestable)
    Vue.component('VueNestableHandle', VueNestableHandle)
  }
}
