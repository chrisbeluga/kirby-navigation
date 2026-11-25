import VueNestable from "./components/Nestable/index.js";
import Field from './Field.vue'
import ModalDefault from './components/Modal/Default.vue'

panel.plugin('beluga/navigation', {
  components: {
    'k-navigation-dialog': ModalDefault
  },
  fields: {
    navigation: Field
  },
  use: VueNestable
})
