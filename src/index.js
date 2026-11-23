import VueNestable from "./components/Nestable/index.js";
import Field from './Field.vue'

panel.plugin('beluga/navigation', {
  fields: {
    navigation: Field
  },
  use: VueNestable
})
