import VueNestable from "vue-nestable/dist/index.esm";
import Field from './Field.vue'

panel.plugin('beluga/navigation', {
  fields: {
    navigation: Field
  },
  use: VueNestable
})
