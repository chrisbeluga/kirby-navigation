<template>
  <k-field
      class="k-form-field navigation-field"
      v-bind:help="help"
      v-bind:label="label"
      v-bind:levels="levels"
      v-bind:disabled="disabled"
      v-bind:required="required">

    <template v-slot:options>
      <k-dropdown>
        <k-button
            icon="add"
            v-on:click="$refs.menu.toggle()">
          {{ $t('menu.link.add') }}
        </k-button>
        <k-dropdown-content
            ref="menu"
            align="right">
          <k-dropdown-item v-on:click="modal_open('default')">
						<span class="k-menu-title">
							{{ $t('menu.link.title') }}
						</span>
            <p class="k-menu-subtitle">
              {{ $t('menu.link.text') }}
            </p>
          </k-dropdown-item>
          <k-dropdown-item
              v-on:click="modal_open('custom')">
						<span class="k-menu-title">
							{{ $t('menu.custom.title') }}
						</span>
            <p class="k-menu-subtitle">
              {{ $t('menu.custom.text') }}
            </p>
          </k-dropdown-item>
        </k-dropdown-content>
      </k-dropdown>
    </template>

    <vue-nestable
        keyProp="uuid"
        v-model="navigation"
        childrenProp="children"
        v-bind:maxDepth="computed_levels"
        v-if="navigation.length">
      <template
          slot-scope="{ item, index }"
          v-bind:item="item">
        <listDefault
            v-bind:item="item"
            v-bind:navigation="navigation"
            v-on:action_add="action_add"
            v-on:action_remove="action_remove">
          <template
              v-slot:handle
              v-bind:item="item">
            <VueNestableHandle v-bind:item="item">
              <k-button
                  icon="sort"
                  class="input-handle"
                  v-bind:tooltip="$t('editor.menu.sort')">
              </k-button>
            </VueNestableHandle>
          </template>
          <template v-slot:dropdown_fields>
            <k-grid>
              <k-column width="1/2">
                <k-text-field
                    v-bind:label="$t('editor.label.text')"
                    v-model="item.text">
                </k-text-field>
              </k-column>

              <k-column width="1/2">
                <k-text-field
                    v-bind:label="$t('editor.label.title')"
                    v-model="item.title">
                </k-text-field>
              </k-column>

              <k-column width="1/2">
                <k-text-field
                    v-bind:label="$t('editor.label.id')"
                    v-model="item.id">
                </k-text-field>
              </k-column>

              <k-column width="1/2">
                <k-toggle-field
                    v-bind:label="$t('editor.label.popup')"
                    v-model="item.popup">
                </k-toggle-field>
              </k-column>

              <k-column width="1/2">
                <k-text-field
                    v-bind:label="$t('editor.label.url')"
                    v-model="item.url">
                </k-text-field>
              </k-column>
            </k-grid>
          </template>
        </listDefault>
      </template>
    </vue-nestable>

    <k-empty
        v-else
        icon="page">
      {{ $t('help.empty.text') }}
    </k-empty>

    <modalDefault
        v-if="modal.status"
        v-bind:modal="modal.status"
        v-on:modal_close="modal_close"
        v-on:modal_submit="modal_submit">
      <template v-slot:modal_header>
        <header class="k-pages-dialog-navbar">
          <template v-if="modal.type === 'default'">
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

          <template v-else>
            <k-headline>
              {{ $t('modal.custom.title') }}
            </k-headline>
          </template>
        </header>
      </template>

      <template v-slot:modal_body>
        <template v-if="modal.type === 'default'">
          <listModal
              v-for="(item, index) in query.content"
              v-bind:key="item.uuid"
              v-bind:item="item">
            <template v-slot:text>
              <span class="k-menu-text">{{ item.text }}</span>
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
                  v-on:click="action_add(item)">
              </k-button>
            </template>
          </listModal>
        </template>

        <template v-else>
          <div class="k-fieldset">
            <k-grid>
              <k-column>
                <k-text-field
                    v-bind:label="$t('editor.label.text')"
                    v-model="item.text">
                </k-text-field>
              </k-column>

              <k-column>
                <k-text-field
                    v-bind:label="$t('editor.label.url')"
                    v-model="item.url">
                </k-text-field>
              </k-column>

              <k-column>
                <k-toggle-field
                    v-bind:label="$t('editor.label.popup')"
                    v-model="item.popup">
                </k-toggle-field>
              </k-column>
            </k-grid>
          </div>
        </template>
      </template>
    </modalDefault>

    <template v-slot:help>
      <k-grid>
        <k-column width="1/2">
          <k-text
              v-if="help"
              theme="help"
              class="k-field-help"
              v-html="help">
          </k-text>
        </k-column>

        <k-column width="1/2">
          <k-text
              theme="help"
              class="k-field-help k-field-depth">
            {{ $t('help.depth.text') }} <strong>{{ computed_levels }}</strong>
          </k-text>
        </k-column>
      </k-grid>
    </template>

  </k-field>
</template>

<script>
// Import Components
import ListModal from './components/Lists/Modal.vue'
import ListDefault from './components/Lists/Default.vue'
import ModalDefault from './components/Modal/Default.vue'

export default {
  props: {
    help: String,
    value: Array,
    label: String,
    levels: Number,
    disabled: Boolean,
    required: Boolean,
    endpoints: Object,
  },
  components: {
    ListModal,
    ListDefault,
    ModalDefault
  },
  data() {
    return {
      navigation: this.value || [],
      modal: {type: '', status: false},
      query: {content: [], breadcrumbs: []},
      item: {url: '', text: '', popup: false}
    }
  },
  watch: {
    navigation: {
      handler() {
        this.$emit('input', this.navigation)
      },
      deep: true
    }
  },
  methods: {
    modal_close() {
      this.modal = {type: '', status: false}
    },
    modal_open(data) {
      this.modal = {type: data, status: true}
    },
    modal_submit() {
      if (this.modal.type === 'custom') {
        this.action_add(this.item)
        this.item = {url: '', text: '', popup: false}
      }
      this.modal = {type: '', status: false}
    },
    action_fetch(data) {
      this.$api.get(this.endpoints.field + '/listings/' + data)
          .then((response) => {
            this.query = response
          })
          .catch((error) => {
            console.log(error)
          })
    },
    action_remove(data) {
      return this.navigation = data.haystack.filter(item => item.uuid !== data.needle).map(item => {
        if (item.children && item.children.length) {
          item.children = this.action_remove({
            haystack: item.children,
            needle: data.needle
          })
        }
        return item
      })
    },
    action_add(data) {
      this.navigation.push({
        children: [],
        id: data.id,
        text: data.text,
        url: data.url,
        popup: data.popup,
        uuid: Math.random().toString(36).substring(2, 15)
      })
    }
  },
  computed: {
    computed_navigation() {
      return this.navigation
    },
    computed_levels() {
      return this.levels ? this.levels : 10
    },
    computed_breadcrumbs() {
      return this.query.breadcrumbs.length >= 2 ? this.query.breadcrumbs[this.query.breadcrumbs.length - 2].id : 'site'
    }
  },
  mounted() {
    this.action_fetch('site');
  }
}
</script>

<style lang="scss">
.navigation-field {
  .k-field-depth {
    text-align: right;

    strong {
      color: #000;
    }
  }

  .k-field-header {
    .k-dropdown-content {
      margin-top: 10px;
    }

    .k-dropdown-item {
      width: 180px;
      margin-bottom: 10px;

      .k-button-text {
        opacity: 1;
        white-space: normal;
        text-align: left;

        .k-menu-title {
          opacity: 1;
          color: #fff;
          width: 100%;
          display: block;
          margin-bottom: 8px;
        }

        .k-menu-subtitle {
          opacity: 0.75;
          font-size: .675rem;
          line-height: 0.875rem;
        }
      }
    }
  }

  .nestable-handle {
    width: 100%;
    display: block;
  }

  .nestable-item-content {
    width: 100%;
    display: flex;
    flex-wrap: nowrap;
    position: relative;
    align-items: center;

    &:hover {
      .nestable-handle {
        .k-button {
          opacity: 1;
          transition: all 0.3s ease-in-out;
        }
      }
    }
  }

  .nestable-handle {
    width: 26px;
    height: 100%;
    display: flex;
    flex-wrap: nowrap;
    position: relative;
    align-items: flex-start;
    margin-top: 7px;

    .k-button {
      opacity: 0.2;
      cursor: move;
      transition: all 0.3s ease-in-out;
    }
  }

  .nestable {
    position: relative;
  }

  .nestable .nestable-list {
    margin: 0;
    padding: 0 0 0 26px;
    list-style-type: none;
  }

  .nestable > .nestable-list {
    padding: 0;
  }

  .nestable-item:first-child,
  .nestable-item-copy:first-child {
    margin-top: 0;
  }

  .nestable-item {
    position: relative;
  }

  .nestable-item.is-dragging .nestable-list {
    pointer-events: none;
  }

  .nestable-item.is-dragging * {
    opacity: 0;
  }

  .nestable-item.is-dragging:before {
    content: '';
    position: absolute;
    top: 0;
    left: 26px;
    right: 0;
    bottom: 0;
    background: #e6e6e6;
    transition: all 0.3s ease-in-out;
  }

  .nestable-drag-layer {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 100;
    pointer-events: none;
  }

  .nestable-rtl .nestable-drag-layer {
    left: auto;
    right: 0;
  }

  .nestable-handle {
    cursor: move;
  }
}
</style>
