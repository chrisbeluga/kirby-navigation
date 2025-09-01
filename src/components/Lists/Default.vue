<template>
  <div class="k-form-input">
    <div class="k-form-actions">
      <slot name="handle"/>
    </div>
    <div class="k-form-inner">
      <k-item v-if="!navigationdisabled"
          v-bind:text="computed_link_text(item)"
          :buttons="[
            {
              icon: active ? 'collapse' : (item.error ? 'question' : (item.type=='custom' ? 'pen' : 'edit')),
              click: function (e) { return item_action({type: 'edit'}) }
            },
          ]"
          :options="[
            {
              icon: 'copy',
              text: $t('editor.menu.duplicate'),
              click: function (e) { return item_action({ type: 'duplicate', item: item }) }
            },
            {
              icon: 'trash',
              text: $t('editor.menu.remove'),
              click: function (e) { return item_action({ type: 'remove', needle: item.uuid, haystack: navigation}) }
            }
          ]"
      />
      <k-item v-else
          v-bind:text="computed_link_text(item)"
      />
      <div
          ref="config"
          v-if="active"
          class="k-form-config">
        <div
            ref="config"
            class="k-form-group">
          <slot name="dropdown_fields"/>
        </div>
        <div class="k-form-footer">
          <span></span>
          <k-button
              icon="hidden"
              v-on:click="item_action({ type: 'edit' })">
            {{ $t('editor.menu.close') }}
          </k-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    item: Object,
    fields: Object,
    navigation: Array,
    navigationdisabled: Boolean,
  },
  data() {
    return {
      active: false
    }
  },
  methods: {
    item_action(data) {
      if (data.type === 'edit') {
        this.active = !this.active
      }
      if (data.type === 'remove') {
        this.$emit('action_remove', data)
      }
      if (data.type === 'duplicate') {
        this.$emit('action_add', data.item)
      }
    },
    langkey(key) {
      let language = this.$panel.language.code ?? 'default';
      return language + '_' + key;
    },
    computed_link_text(item) {
      if (item.type === 'page') {
        if (item[this.langkey('link_text')] === '') {
          // if link text of a page is empty, use page title
          return item[this.langkey('page_title')];
        }
      }
      return item[this.langkey('link_text')];
    },
  },
}
</script>

<style lang="scss" scoped>
.k-form-input {
  width: 100%;
  display: flex;
  position: relative;
  margin-bottom: 2px;

  .k-form-inner {
    width: 100%;
    display: flex;
    position: relative;
    flex-direction: column;

    .k-form-config {
      width: 100%;
      border-top: 0;
      display: flex;
      border: 1px solid #ccc;
      flex-direction: column;
      margin-top: 5px;

      .k-form-group {
        flex-grow: 1;
        padding: 1rem;
        display: flex;
        flex-direction: column;
      }

      .k-form-footer {
        width: 100%;
        display: flex;
        margin-top: 2rem;
        align-items: center;
        padding: 0.6rem 1rem;
        border-top: 1px solid #ccc;
        justify-content: space-between;
      }
    }
  }

  .k-form-actions {
    display: flex;
    flex-direction: column;

    .input-handle {
      padding: 0 0.4rem 1rem 0.4rem;
    }
  }
}
</style>
