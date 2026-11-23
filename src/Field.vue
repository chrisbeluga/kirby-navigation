<template>
  <k-field
      class="k-form-field navigation-field"
      v-bind:help="help"
      v-bind:label="label"
      v-bind:levels="levels"
      v-bind:disabled="disabled"
      v-bind:required="required">

    <template v-slot:options>
      <k-button v-if="!disabled"
          icon="add"
          v-on:click="$refs.menu.toggle()">
        {{ $t('menu.link.add') }}
      </k-button>
      <k-dropdown
          ref="menu">
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
      </k-dropdown>
    </template>

    <vue-nestable
        keyProp="uuid"
        v-bind:value="navigation"
        v-on:input="navigation = $event"
        childrenProp="children"
        v-bind:maxDepth="computed_levels"
        v-if="navigation.length">
      <template v-slot="{ item, index }">
        <listDefault
            v-if="item.type!='save'"
            v-bind:item="item"
            v-bind:navigation="navigation"
            v-bind:navigationdisabled="disabled"
            v-on:action_add="action_add"
            v-on:action_remove="action_remove">
          <template v-slot:handle>
            <VueNestableHandle v-bind:item="item">
              <k-button
                  icon="sort"
                  class="input-handle"
                  v-bind:tooltip="$t('editor.menu.sort')">
              </k-button>
            </VueNestableHandle>
          </template>
          <template v-slot:dropdown_fields>
            <k-grid v-if="item.type == 'page'">
              <k-column width="1">
                <k-info-field
                    v-bind:text="item[langkey('page_title')]"
                    icon="page">
                </k-info-field>
                <k-info-field
                    v-bind:text="item[langkey('page_url')]"
                    icon="url">
                </k-info-field>
              </k-column>

              <k-column width="1/2">
                <k-text-field
                    v-bind:label="$t('editor.label.text')"
                    v-bind:value="item[langkey('link_text')]"
                    v-on:input="item[langkey('link_text')] = $event">
                </k-text-field>
              </k-column>

              <k-column width="1/2" v-if="title_is_editable(item)">
                <k-text-field
                    v-bind:label="$t('editor.label.title')"
                    v-bind:value="item[langkey('link_title')]"
                    v-on:input="item[langkey('link_title')] = $event">
                </k-text-field>
              </k-column>

              <k-column width="1/2" v-if="anchor_is_editable(item)">
                <k-text-field
                    v-bind:label="$t('editor.label.anchor')"
                    v-bind:value="item[langkey('link_anchor')]"
                    v-on:input="item[langkey('link_anchor')] = $event">
                </k-text-field>
              </k-column>

              <k-column width="1/2" v-if="class_is_editable(item)">
                <k-text-field
                    v-bind:label="$t('editor.label.class')"
                    v-bind:value="item.class"
                    v-on:input="item.class = $event">
                </k-text-field>
              </k-column>

              <k-column width="1/2" v-if="target_is_editable(item)">
                <k-text-field
                    v-bind:label="$t('editor.label.target') + ' (_blank, _self, _parent, _top, ...)'"
                    v-bind:value="item.target"
                    v-on:input="item.target = $event">
                </k-text-field>
              </k-column>

              <k-column width="1/2" v-if="popup_is_editable(item)">
                <k-toggle-field
                    @input="item.target=$event ? '_blank' : ''"
                    :value="(item.target=='_blank' ? true : false)"
                    v-bind:label="$t('editor.label.popup')">
                </k-toggle-field>
              </k-column>
            </k-grid>

            <k-grid v-else-if="item.type == 'custom'">
              <k-column width="1/2">
                <k-text-field
                    v-bind:label="$t('editor.label.text')"
                    v-bind:value="item[langkey('link_text')]"
                    v-on:input="item[langkey('link_text')] = $event">
                </k-text-field>
              </k-column>

              <k-column width="1/2" v-if="title_is_editable(item)">
                <k-text-field
                    v-bind:label="$t('editor.label.title')"
                    v-bind:value="item[langkey('link_title')]"
                    v-on:input="item[langkey('link_title')] = $event">
                </k-text-field>
              </k-column>

              <k-column width="1/2">
                <k-text-field
                    v-bind:label="$t('editor.label.url')"
                    v-bind:value="item.url"
                    v-on:input="item.url = $event">
                </k-text-field>
              </k-column>

              <k-column width="1/2" v-if="class_is_editable(item)">
                <k-text-field
                    v-bind:label="$t('editor.label.class')"
                    v-bind:value="item.class"
                    v-on:input="item.class = $event">
                </k-text-field>
              </k-column>

              <k-column width="1/2" v-if="target_is_editable(item)">
                <k-text-field
                    v-bind:label="$t('editor.label.target') + ' (_blank, _self, _parent, _top, ...)'"
                    v-bind:value="item.target"
                    v-on:input="item.target = $event">
                </k-text-field>
              </k-column>

              <k-column width="1/2" v-if="popup_is_editable(item)">
                <k-toggle-field
                    @input="item.target=$event ? '_blank' : ''"
                    :value="(item.target=='_blank' ? true : false)"
                    v-bind:label="$t('editor.label.popup')">
                </k-toggle-field>
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

    <template v-slot:help>
      <k-grid>
        <k-column width="1/2">
          <k-help
              v-if="help"
              class="k-field-help"
              v-html="help">
          </k-help>
        </k-column>

        <k-column width="1/2">
          <k-help
              v-if="computed_levels<=5"
              class="k-field-help k-field-depth">
            {{ $t('help.depth.text') }} <strong>{{ computed_levels }}</strong>
          </k-help>
        </k-column>
      </k-grid>
    </template>

  </k-field>
</template>

<script>
// Import Components
import ListDefault from './components/Lists/Default.vue'

export default {
  props: {
    help: String,
    value: Array,
    label: String,
    levels: Number,
    edit_title: Boolean,
    edit_popup: Boolean,
    edit_target: Boolean,
    edit_class: Boolean,
    edit_anchor: Boolean,
    disabled: Boolean,
    required: Boolean,
    endpoints: Object,
  },
  components: {
    ListDefault
  },
  emits: ['input'],
  data() {
    return {
      navigation: this.value || []
    }
  },
  watch: {
    navigation: {
      handler() {
        this.$emit('input', this.navigation)
      },
      deep: true
    },
    panel_content_has_diff() {
      if (!this.panel_content_has_diff) {
        // If previously detected changes disappeared,
        // probably because of the "Discard" button in Panel,
        // then reset the navigation items to the initial values.
        this.navigation = this.value;
      }
    }
  },
  methods: {
    modal_open(data) {
      this.$panel.dialog.open({
        component: 'k-navigation-dialog',
        props: {
          type: data,
          endpoints: this.endpoints,
          edit_popup: this.edit_popup,
          edit_target: this.edit_target,
        },
        on: {
          add: this.action_add,
          submit: () => this.$panel.dialog.close()
        }
      });
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
      if (data.type=='page') {
        let newitem = {
          type: data.type,
          id: data.id,
          uuid_uri: data.uuid_uri,
          target: '',
          uuid: Math.random().toString(36).substring(2, 15),
          children: [],
        };
        newitem[this.langkey('link_text')]=data[this.langkey('link_text')];
        newitem[this.langkey('page_url')]=data[this.langkey('page_url')];
        newitem[this.langkey('page_title')]=data[this.langkey('page_title')];
        this.navigation.push(newitem);
      }
      else if (data.type=='custom') {
        let newitem = {
          type: data.type,
          url: data.url,
          target: data.popup ? '_blank' : data.target,
          uuid: Math.random().toString(36).substring(2, 15),
          children: [],
        };
        newitem[this.langkey('link_text')]=data[this.langkey('link_text')] ?? '';
        newitem[this.langkey('link_title')]='';
        this.navigation.push(newitem);
      }
      else {
        console.warn('Invalid data.type value');
      }
    },
    langkey(key) {
      let language = this.$panel.language.code ?? 'default';
      return language + '_' + key;
    },
    title_is_editable(item) {
      if (typeof item[this.langkey('link_title')] === 'string') {
        if (item[this.langkey('link_title')]!=='') {
          return true;
        }
      }
      return this.edit_title;
    },
    anchor_is_editable(item) {
      if (typeof item[this.langkey('link_anchor')] === 'string') {
        if (item[this.langkey('link_anchor')]!=='') {
          return true;
        }
      }
      return this.edit_anchor;
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
    },
    class_is_editable(item) {
      if (typeof item.class === 'string') {
        if (item.class!=='') {
          return true;
        }
      }
      return this.edit_class;
    }
  },
  computed: {
    computed_navigation() {
      return this.navigation
    },
    computed_levels() {
      if (this.levels && parseInt(this.levels) && (parseInt(this.levels)<1)) {
        return parseInt(this.levels);
      }
      return 10;
    },
    panel_content_has_diff() {
      return this.$panel.content.hasDiff();
    },
  }
}
</script>

<style lang="scss">
.navigation-field {
  .k-field-depth {
    text-align: right;
  }

  .k-field-header {
    .k-dropdown-content {
      margin-top: 10px;
    }

    .k-dropdown-item {
      width: 180px;
      height: auto;
      padding: 8px;
      margin-bottom: 8px;

      .k-button-text {
        opacity: 1;
        white-space: normal;
        text-align: left;

        .k-menu-title {
          opacity: 1;
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
    width: auto;
    height: auto;
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
    
    .k-column {
      margin-top: 8px;
      margin-right: 8px;
    }

    .k-info-field + .k-info-field {
      margin-top: 8px;
    }
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
