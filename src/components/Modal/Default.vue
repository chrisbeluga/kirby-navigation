<template>
  <k-dialog
      size="medium"
      class="k-pages-dialog"
      v-bind:visible="visible"
      v-on:cancel="modal_close"
      v-on:submit="modal_submit">
    <header class="k-pages-dialog-navbar">
      <template v-if="type === 'default'">
        <k-button
            icon="angle-left"
            v-on:click="action_fetch(computed_breadcrumbs)"
            v-if="query.breadcrumbs.length > 0">
          {{ $t('modal.link.breadcrumb') }}
        </k-button>

        <k-headline>
          {{ $t('modal.link.title') }}
        </k-headline>
      </template>

      <template v-if="type === 'custom'">
        <k-headline>
          {{ $t('modal.custom.title') }}
        </k-headline>
      </template>
    </header>

    <template v-if="type === 'default'">
      <listModal
          v-for="(item, index) in query.content"
          v-bind:key="item.uuid"
          v-bind:item="item">
        <template v-slot:text>
          <span class="k-menu-text">{{ item[langkey('page_title')] }}</span>
        </template>

        <template v-slot:fetch>
          <k-button
              icon="angle-right"
              v-if="item.count > 0"
              v-on:click="action_fetch(item.id)">
          </k-button>
        </template>

        <template v-slot:add>
          <k-button
              icon="add"
              v-on:click="$emit('add', item)">
          </k-button>
        </template>
      </listModal>
    </template>

    <template v-if="type === 'custom'">
      <div class="k-fieldset">
        <k-grid>
          <k-column>
            <k-text-field
                v-bind:label="$t('editor.label.text')"
                v-bind:value="item[langkey('link_text')]"
                v-on:input="item[langkey('link_text')] = $event">
            </k-text-field>
          </k-column>

          <k-column>
            <k-text-field
                v-bind:label="$t('editor.label.url')"
                v-bind:value="item.url"
                v-on:input="item.url = $event">
            </k-text-field>
          </k-column>

          <k-column v-if="popup_is_editable(item)">
            <k-toggle-field
                @input="item.target=$event ? '_blank' : ''"
                :value="(item.target=='_blank' ? true : false)"
                v-bind:label="$t('editor.label.popup')">
            </k-toggle-field>
          </k-column>

          <k-column v-if="target_is_editable(item)">
            <k-text-field
                v-bind:label="$t('editor.label.target') + ' (_blank, _self, _parent, _top, ...)'"
                v-bind:value="item.target"
                v-on:input="item.target = $event">
            </k-text-field>
          </k-column>

        </k-grid>
      </div>
    </template>
  </k-dialog>
</template>

<script>
import ListModal from '../Lists/Modal.vue'

export default {
  props: {
    type: String,
    visible: Boolean,
    endpoints: Object,
    edit_popup: Boolean,
    edit_target: Boolean,
  },
  components: {
    ListModal
  },
  emits: ['add', 'cancel', 'submit'],
  data() {
    return {
      query: {content: [], breadcrumbs: []},
      item: {url: '', uuid_uri: '', text: '', target: ''}
    }
  },
  methods: {
    modal_close() {
      this.$emit('cancel')
    },
    modal_submit() {
      if (this.type === 'custom') {
        this.item.type='custom'
        this.$emit('add', this.item)
      }
      this.$emit('submit')
    },
    action_fetch(data) {
      let language = this.$panel.language.code ?? 'default';
      this.$api.get(this.endpoints.field + '/listings/' + language + '/' + data)
          .then((response) => {
            this.query = response
          })
          .catch((error) => {
            this.query = {content: [], breadcrumbs: []};
            console.log(error)
          })
    },
    langkey(key) {
      let language = this.$panel.language.code ?? 'default';
      return language + '_' + key;
    },
    popup_is_editable(item) {
      // always hide popup, if target is enabled, or target contains data
      if (typeof item.target === 'string') {
        if ((item.target!=='') && (item.target!=='_blank')) {
          return false;
        }
      }
      if (this.edit_target) {
        return false;
      }
      return this.edit_popup;
    },
    target_is_editable(item) {
      if (typeof item.target === 'string') {
        if ((item.target!=='') && (item.target!=='_blank')) {
          return true;
        }
      }
      return this.edit_target;
    }
  },
  computed: {
    computed_breadcrumbs() {
      return this.query.breadcrumbs.length >= 2 ? this.query.breadcrumbs[this.query.breadcrumbs.length - 2].id : 'site'
    }
  },
  mounted() {
    this.action_fetch('site');
  }
}
</script>

<style lang="scss" scoped>
.k-pages-dialog-navbar {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.5rem;
  padding-right: 38px;
}

.k-pages-dialog-navbar .k-button[disabled] {
  opacity: 0;
}

.k-pages-dialog-navbar .k-headline {
  flex-grow: 1;
  text-align: center;
}

.k-pages-dialog .k-list-item {
  cursor: pointer;
}

.k-pages-dialog .k-list-item .k-button[data-theme="disabled"],
.k-pages-dialog .k-list-item .k-button[disabled] {
  opacity: 0.25;
}

.k-pages-dialog .k-list-item .k-button[data-theme="disabled"]:hover {
  opacity: 1;
}
</style>
