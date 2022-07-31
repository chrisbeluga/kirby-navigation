<template>
  <k-dialog
      size="medium"
      class="k-pages-dialog"
      v-bind:visible="modal"
      v-on:cancel="modal_close"
      v-on:submit="modal_submit">
    <slot name="modal_header"/>
    <slot name="modal_body"/>
  </k-dialog>
</template>

<script>
export default {
  props: {
    modal: Object,
  },
  methods: {
    modal_close() {
      this.$emit('modal_close')
    },
    modal_submit() {
      this.$emit('modal_submit')
    }
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

.k-pages-dialog-navbar .k-button {
  width: 38px;
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
