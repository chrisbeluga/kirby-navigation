<template>
  <div class="k-form-input">
    <div class="k-form-actions">
      <slot name="handle"/>
    </div>
    <div class="k-form-inner">
      <k-item
          v-bind:text="item.text"
          v-on:action="item_action"
          v-bind:options="[
					{
						icon: 'add',
						text: active ? $t('editor.menu.close') : $t('editor.menu.edit'),
						click: { type: 'edit' }
					},
					{
						icon: 'copy',
						text: $t('editor.menu.duplicate'),
						click: { type: 'duplicate', item: item }
					},
					{
						icon: 'trash',
						text: $t('editor.menu.remove'),
						click: { type: 'remove', needle: item.uuid, haystack: navigation}
					}
				]"
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
          <k-button
              icon="remove"
              v-on:click="item_action({ type: 'edit' })">
            {{ $t('editor.menu.close') }}
          </k-button>
          <k-button
              icon="trash"
              theme="negative"
              v-on:click="item_action({ type: 'remove', haystack: navigation, needle: item.uuid })">
            {{ $t('editor.menu.remove') }}
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
    }
  }
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
      background: #e6e6e6;
      border: 1px solid #ccc;
      flex-direction: column;

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
